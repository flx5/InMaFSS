<?php

class Controller_Replacements extends RestController {

    public function GET() {
        if (!isset($this->args[0])) {
            $this->AddError(APIErrorCodes::PARAM_DAY_MISSING);
            return;
        }

        switch (Authorization::GetUserType('LDAP')) {
            case ReplacementsTypes::PUPIL:
            case ReplacementsTypes::TEACHER:
                break;
            default:
                $this->AddError(APIErrorCodes::UNKNOWN_USER_TYPE);
                return;
                break;
        }

        $replacements = null;

        try {
            $replacements = new Replacements(Authorization::GetUserType('LDAP'), $this->args[0]);
        } catch (Exception $e) {
            $this->AddError(APIErrorCodes::PARAM_DAY_INVALID);
            return;
        }

        if (Authorization::GetUserType('LDAP') == ReplacementsTypes::TEACHER) {
            $this->response = $replacements->GetReplacements(
                    Array(
                        Array('type' => 'fullname', 'value' => Authorization::GetDisplayName('LDAP'))
                    )
            );
        } else {
            $grades = Authorization::GetClasses('LDAP');
            
            if(count($grades) == 0)
            {
                $this->AddError(APIErrorCodes::INVALID_GROUP);
                return;
            }
            
            $this->response = $replacements->GetReplacements($grades);
        }

        $this->responseStatus = 200;
    }

}

?>
