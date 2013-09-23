<?php
abstract class SQL {    
    public abstract function __construct($host, $user, $pass, $db);
    public abstract function IsConnected();
    public abstract function Connect();
    public abstract function Disconnect();
    public abstract function DoQuery($query);
    public abstract function insertID();
    public abstract function affected_rows();
    public abstract function real_escape_string();
    public abstract function __destruct();
    public abstract function GetCount();
    public abstract function GetRequests();

    public function Error($errorString) {
        if(class_exists("core"))
             core::systemError('Database Error', $errorString);
        else 
            echo("Database Error: <br>\n".$errorString);
    }
    
    public static function GenerateInstance($type, $host, $user, $pass, $db) {
        if(strpos($type, ".") !== false || strpos($type, "/") !== false || strpos($type, "\\") !== false)
                return false;
        
        if(!file_exists(dirname(__FILE__)."/db/".$type.".php"))
                return false;
        
        require_once(dirname(__FILE__)."/db/".$type.".php");
        
        $type = "_".$type;
        
        return new $type($host, $user, $pass, $db);
    }
}
?>
