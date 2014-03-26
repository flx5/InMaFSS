<?php

require_once(INC . 'class.time_helper.php');

class Controller_Appointments extends RestController {

    public function RequiredScope($method) {
        switch ($method) {
            case 'POST':
                return ScopeData::UPDATE_EVENTS;
                break;
            default:
                return ScopeData::EVENTS;
        }
    }

    public function RequireUser($method) {
        if ($method == "POST")
            return false;
        return true;
    }

    public function GET() {
        if (!isset($this->args[0])) {
            $this->AddError(APIErrorCodes::PARAM_DAY_MISSING);
            return;
        }

        $tfrom = RestUtil::GetTFrom($this->args[0]);

        // Defining gmmktime with date command to ensure the usage of the correct date (Could be a problem between 0 and 1 o'clock at UTC+1 i.e.
        $sql = dbquery("SELECT * FROM events WHERE startdate >= " . $tfrom . " AND startdate < " . ($tfrom + 24 * 3600));

        $events = Array();

        while ($event = $sql->fetchAssoc()) {
            $event['startdate'] = (int) $event['startdate'];
            $events[] = $event;
        }

        $this->meta = Array('next' => RestUtil::GetNextTFrom($tfrom));
        $this->response = $events;
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

        $p = new parseAppointments();
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