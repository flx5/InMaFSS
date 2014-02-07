<?php
abstract class Authorization {    
    public abstract function Login($username, $password);
    public abstract function Logout();
    public abstract function HasFuse($fuse);
    public abstract function GetClasses();
    public abstract function GetDisplayName();
    
    protected final function SetSession($username, $userid, $type = "DB") {
        $_SESSION[$type.'_user'] = $username;
        $_SESSION[$type.'_userID'] = $userid;
        $_SESSION[$type.'_timestamp'] = time();
    }
    
    protected final function DestroySession() {
        session_destroy();
    }
    
    
    public static function IsLoggedIn($type = "DB") {
        if (isset($_SESSION[$type.'_user']) && isset($_SESSION[$type.'_timestamp']) && isset($_SESSION[$type.'_userID'])) {
            return Array('id' => $_SESSION[$type.'_userID'], 'name' => $_SESSION[$type.'_user']);
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
