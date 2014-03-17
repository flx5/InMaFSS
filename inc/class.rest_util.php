<?php
class RestUtil {
    public static function GetUserData($userID) {
         $auth = Authorization::GenerateInstance('LDAP');
        /* @var $auth LDAP_Auth */
        $user = $auth->getUserDataByID($userID);
        return $user;
    }
    
    public static function CheckUserType($userID, $userData = null) {
       if($userData == null)
            $userData = self::GetUserData($userID);

        switch ($userData['type']) {
            case ReplacementsTypes::PUPIL:
            case ReplacementsTypes::TEACHER:
                return $userData['type'];
            default:
                $this->AddError(APIErrorCodes::UNKNOWN_USER_TYPE);
                return false;
        }
    }
    
    public static function GetReplacements($type, $day) {
        $replacements = null;

        try {
            $replacements = new Replacements($type, $day);
        } catch (Exception $e) {
            $this->AddError(APIErrorCodes::PARAM_DAY_INVALID);
            return null;
        }
        
        return $replacements;
    }
    
    public static function GetTFrom($day) {
        try {
            return TimeHelper::GetTFrom($day);
        } catch (Exception $e) {
            $this->AddError(APIErrorCodes::PARAM_DAY_INVALID);
            return null;
        }
    }
}
?>
