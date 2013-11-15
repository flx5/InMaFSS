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
        if (class_exists("core"))
            core::systemError('Database Error', $errorString);
        else
            echo("Database Error: <br>\n" . $errorString);
    }

    public static function GenerateInstance($type, $host, $user, $pass, $db) {
        if (strpos($type, ".") !== false || strpos($type, "/") !== false || strpos($type, "\\") !== false)
            return false;

        if (!file_exists(dirname(__FILE__) . "/db/" . $type . ".php"))
            return false;

        require_once(dirname(__FILE__) . "/db/" . $type . ".php");

        $type = "_" . $type;

        return new $type($host, $user, $pass, $db);
    }

    public static function GetType($typeId) {
        switch ($typeId) {
            case DB_TYPE_DECIMAL:
                return 'DECIMAL';
            case DB_TYPE_NEWDECIMAL:
                return 'NEWDECIMAL';
            case DB_TYPE_BIT:
                return 'BIT';
            case DB_TYPE_TINY:
                return 'TINY';
            case DB_TYPE_SHORT:
                return 'SHORT';
            case DB_TYPE_LONG:
                return 'LONG';
            case DB_TYPE_FLOAT:
                return 'FLOAT';
            case DB_TYPE_DOUBLE:
                return 'DOUBLE';
            case DB_TYPE_NULL:
                return 'NULL';
            case DB_TYPE_TIMESTAMP:
                return 'TIMESTAMP';
            case DB_TYPE_LONGLONG:
                return 'LONGLONG';
            case DB_TYPE_INT24:
                return 'INT24';
            case DB_TYPE_DATE:
                return 'DATE';
            case DB_TYPE_TIME:
                return 'TIME';
            case DB_TYPE_DATETIME:
                return 'DATETIME';
            case DB_TYPE_YEAR:
                return 'YEAR';
            case DB_TYPE_NEWDATE:
                return 'NEWDATE';
            case DB_TYPE_INTERVAL:
                return 'INTERVAL';
            case DB_TYPE_ENUM:
                return 'ENUM';
            case DB_TYPE_SET:
                return 'SET';
            case DB_TYPE_TINY_BLOB:
                return 'BLOB';
            case DB_TYPE_MEDIUM_BLOB:
                return 'BLOB';
            case DB_TYPE_LONG_BLOB:
                return 'BLOB';
            case DB_TYPE_BLOB:
                return 'BLOB';
            case DB_TYPE_VAR_STRING:
                return 'STRING';
            case DB_TYPE_STRING:
                return 'STRING';
            case DB_TYPE_CHAR:
                return 'CHAR';
            case DB_TYPE_GEOMETRY:
                return 'GEOMETRY';
            default:
                return 'UNKNOWN';
        }
    }
}

?>
