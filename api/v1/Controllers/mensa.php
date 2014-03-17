<?php

require_once(INC . 'class.time_helper.php');

class Controller_Mensa extends RestController {

    public function RequiredScope($method) {
        switch ($method) {
            case 'POST':
                return ScopeData::UPDATE_MENSA;
                break;
            default:
                return ScopeData::BASIC;
        }
    }

    public function GET() {
        if (!isset($this->args[0])) {
            $this->AddError(APIErrorCodes::PARAM_DAY_MISSING);
            return;
        }

        $tfrom = RestUtil::GetTFrom($this->args[0]);

        // Defining gmmktime with date command to ensure the usage of the correct date (Could be a problem between 0 and 1 o'clock at UTC+1 i.e.
        $sql = dbquery("SELECT * FROM mensa WHERE day >= " . $tfrom . " AND day < " . ($tfrom + 24 * 3600) . " LIMIT 1");

        $menu = $sql->fetchAssoc();

        $menu['additives'] = unserialize($menu['additives']);

        $this->response = $menu;
        $this->responseStatus = 200;
    }

    public function POST() {
        if (count($_FILES) == 0) {
            $this->AddError(APIErrorCodes::MISSING_FILE);
            return;
        }

        foreach ($_FILES as $file) {
            if ($file['error'] != UPLOAD_ERR_OK) {
                $this->AddError(APIErrorCodes::UPLOAD_FAILED);
                return;
            }
        }

        $p = new parseMensa();
        $count = Array('success' => 0, 'failure' => 0);

        foreach ($_FILES as $file) {
            $status = $p->parse($file['tmp_name']);
            if ($status)
                $count['success']++;
            else
                $count['failure']++;
        }

        $count['uploaded'] = true;
        $p->UpdateDatabase();
        $this->response = $count;
        $this->responseStatus = 200;
    }

}

?>