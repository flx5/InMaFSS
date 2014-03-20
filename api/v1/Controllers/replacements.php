<?php

class Controller_Replacements extends RestController {

    public function RequiredScope($method) { 
        switch($method)
        {
            case 'POST':
                return ScopeData::UPDATE_SUBSTITION_PLAN;
                break;
            default:
                return ScopeData::SUBSTITUTION_PLAN;
        }
    }
    
    public function RequireUser($method) {
        if($method == "POST")
            return false;
        return true;
    }
    
    public function GET() {
        if (!isset($this->args[0])) {
            $this->AddError(APIErrorCodes::PARAM_DAY_MISSING);
            return;
        }

        $user = RestUtil::GetUserData($this->user);
        
        if(RestUtil::CheckUserType($this->user, $user) === false)
            return;

        $replacements = RestUtil::GetReplacements($user['type'], $this->args[0]);
        
        if($replacements === null)
            return;
        
        if ($user['type'] == ReplacementsTypes::TEACHER) {
            $this->response = $replacements->GetReplacements(
                    Array(
                        Array('type' => 'fullname', 'value' => $user['display_name'])
                    )
            );
        } else {
            if (count($user['groups']) == 0) {
                $this->AddError(APIErrorCodes::INVALID_GROUP);
                return;
            }

            $this->response = $replacements->GetReplacements($user['groups']);
        }

        $this->responseStatus = 200;
    }

    public function POST() {
        if (count($_FILES) == 0) {
            $this->AddError(APIErrorCodes::MISSING_FILE);
            return;
        }

        foreach ($_FILES as $file) {
            if($file['error'] != UPLOAD_ERR_OK) { echo $file['error'];
                $this->AddError(APIErrorCodes::UPLOAD_FAILED);
                return;
            }
        }
        
        $p = new parsePlan();

        $count = Array('success'=>0, 'failure'=>0);
        
        foreach($_FILES as $file) {
            $status = $p->parse($file['tmp_name']);
            if($status)
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
