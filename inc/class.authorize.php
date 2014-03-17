<?php

require_once(INC . "class.replacements.php");

abstract class Authorization {

    public abstract function Login($username, $password);

    public abstract function Logout();

    public abstract function HasFuse($fuse);

    public static function GetDisplayName($type = 'DB') {
        if (isset($_SESSION[$type . '_displayName']))
            return $_SESSION[$type . '_displayName'];

        return '';
    }

    public static function GetClasses($type = 'DB') {
        if (isset($_SESSION[$type . '_classes']))
            return $_SESSION[$type . '_classes'];

        return Array();
    }

    public static function GetUserType($type = 'DB') {
        if (isset($_SESSION[$type . '_usertype']))
            return $_SESSION[$type . '_usertype'];

        return ReplacementsTypes::PUPIL;
    }
    
    public static function GetUserID($type = 'DB') {
        if (isset($_SESSION[$type . '_userID']))
            return $_SESSION[$type . '_userID'];

        return -1;
    }

    protected final function SetSession($username, $userid, $type = "DB", $classes = Array(), $userType = ReplacementsTypes::PUPIL, $displayName = '') {
        $_SESSION[$type . '_user'] = $username;
        $_SESSION[$type . '_userID'] = $userid;
        $_SESSION[$type . '_timestamp'] = time();
        $_SESSION[$type . '_classes'] = $classes;
        $_SESSION[$type . '_usertype'] = $userType;
        if($displayName == "")
            $displayName = $username;
        $_SESSION[$type . '_displayName'] = $displayName;
    }

    protected final function DestroySession() {
        session_destroy();
    }

    public static function IsLoggedIn($type = "DB") {
        if (isset($_SESSION[$type . '_user']) && isset($_SESSION[$type . '_timestamp']) && isset($_SESSION[$type . '_userID'])) {
            return Array('id' => $_SESSION[$type . '_userID'], 'name' => $_SESSION[$type . '_user'], 'displayName' => $_SESSION[$type . '_displayName']);
        }
        return null;
    }

    public static final function GenerateInstance($type) {
        if (strpos($type, ".") !== false || strpos($type, "/") !== false || strpos($type, "\\") !== false)
            return false;

        $type .= "_Auth";

        if (!file_exists(dirname(__FILE__) . "/auth/" . $type . ".php"))
            return false;

        require_once(dirname(__FILE__) . "/auth/" . $type . ".php");

        return new $type();
    }

}

?>
