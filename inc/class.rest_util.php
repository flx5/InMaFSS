<?php
class RestUtil
{
    public static function GetUserData($userID) 
    {
         $auth = Authorization::GenerateInstance('LDAP');
        /* @var $auth LDAP_Auth */
        $user = $auth->getUserDataByID($userID);
        return $user;
    }
    
    public static function CheckUserType($userID, $userData = null) 
    {
        if($userData == null) {
            $userData = self::GetUserData($userID); 
        }

        switch ($userData['type']) {
        case ReplacementsTypes::PUPIL:
        case ReplacementsTypes::TEACHER:
            return $userData['type'];
        default:
            self::AddError(APIErrorCodes::UNKNOWN_USER_TYPE);
            return false;
        }
    }
    
    public static function GetReplacements($type, $day) 
    {
        $replacements = null;

        try {
            $replacements = new Replacements($type, $day);
        } catch (Exception $e) {
            self::AddError(APIErrorCodes::PARAM_DAY_INVALID);
            return null;
        }
        
        return $replacements;
    }
    
    public static function GetTFrom($day) 
    {
        try {
            return TimeHelper::GetTFrom($day);
        } catch (Exception $e) {
            self::AddError(APIErrorCodes::PARAM_DAY_INVALID);
            return null;
        }
    }
    
    public static function GetNextTFrom($today) 
    {
        // Force TFrom to be at the start of day
        $tfrom = gmmktime(0, 0, 0, gmdate('n', $today), gmdate('j', $today)+1, gmdate('Y', $today));
        return TimeHelper::GetNextDay($tfrom);
    }
}
?>
