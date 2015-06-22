<?php

/* =================================================================================*\
  |* This file is part of InMaFSS                                                    *|
  |* InMaFSS - INformation MAnagement for School Systems - Keep yourself up to date! *|
  |* ############################################################################### *|
  |* Copyright (C) flx5                                                              *|
  |* E-Mail: me@flx5.com                                                             *|
  |* ############################################################################### *|
  |* InMaFSS is free software; you can redistribute it and/or modify                 *|
  |* it under the terms of the GNU Affero General Public License as published by     *|
  |* the Free Software Foundation; either version 3 of the License,                  *|
  |* or (at your option) any later version.                                          *|
  |* ############################################################################### *|
  |* InMaFSS is distributed in the hope that it will be useful,                      *|
  |* but WITHOUT ANY WARRANTY; without even the implied warranty of                  *|
  |* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                            *|
  |* See the GNU Affero General Public License for more details.                     *|
  |* ############################################################################### *|
  |* You should have received a copy of the GNU Affero General Public License        *|
  |* along with InMaFSS; if not, see http://www.gnu.org/licenses/.                   *|
  \*================================================================================= */

class _MySQL extends SQL
{

    private $connected = false;
    private $hostname = "localhost";
    private $username = "root";
    private $password = "pass";
    private $database = "inmafss";
    private $link;
    private $count = 0;
    private $requests = Array();

    public function __construct($host, $user, $pass, $db) 
    {
        $this->connected = false;
        $this->hostname = $host;
        $this->username = $user;
        $this->password = $pass;
        $this->database = $db;
    }

    public function IsConnected() 
    {
        if ($this->connected) {
            return true;
        }

        return false;
    }

    public function Connect() 
    {
        $this->link = @mysql_connect($this->hostname, $this->username, $this->password);
        
        if($this->link === false) {
            $this->Error('Connect Error (' . mysql_errno() . ') ' . mysql_error());
            return;
        }
        
        $selectDB = @mysql_select_db($this->database, $this->link);
         
        if($selectDB === false) {
            $this->Error('Connect Error (' . mysql_errno($this->link) . ') ' . mysql_error($this->link));
            return;
        }
        
        mysql_set_charset("utf-8", $this->link);
        $this->connected = true;
    }

    public function Disconnect() 
    {
        if ($this->connected) {
            mysql_close($this->link) or $this->error("could not close conn");
            $this->connected = false;
        }
    }

    public function DoQuery($query) 
    {
        $this->requests[] = $query;
        $resultset = mysql_query($query, $this->link) or $this->error(mysql_error());

        if ($resultset === true) {
            return true; 
        }

        if ($resultset === false) {
            return false; 
        }

        return new _MYSQL_Result($resultset);
    }

    public function insertID() 
    {
        return mysql_insert_id($this->link);
    }

    public function affected_rows() 
    {
        return mysql_affected_rows($this->link);
    }

    public function real_escape_string($strInput = '') 
    {
        return mysql_real_escape_string($strInput, $this->link);
    }

    public function __destruct() 
    {
        $this->disconnect();
    }

    public function GetCount() 
    {
        return count($this->requests);
    }

    public function GetRequests() 
    {
        return $this->requests;
    }

    public function getErrorList() 
    { 
        return null;
    }

    public function getFieldsInfo($table) 
    {
        $sql = dbquery("SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_TYPE FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . config("dbname") . "' AND `TABLE_NAME`='" . $table . "';");

        $info = Array();
        while ($field = $sql->fetchObject()) {  
            $info[$field->COLUMN_NAME] = Array(
                'name' => $field->COLUMN_NAME,
                'type' => $field->DATA_TYPE,
                'max_length' => $field->CHARACTER_MAXIMUM_LENGTH,
            );
            
            if($field->DATA_TYPE == 'enum') {
                $info[$field->COLUMN_NAME]['enum'] = explode(",", str_replace("'", "", substr($field->COLUMN_TYPE, 5, (strlen($field->COLUMN_TYPE)-6))));
            }
        }
        
        return $info;
    }
    
    public function GetLink() 
    {
        return $this->link;
    }

}

class _MYSQL_Result
{

    private $res;

    public function __construct($result) 
    {
        $this->res = $result;
    }

    public function count() 
    {
        return mysql_num_rows($this->res);
    }

    public function fetchAssoc() 
    {
        $ret = mysql_fetch_assoc($this->res);
        return $ret;
    }

    public function fetchArray() 
    {
        $ret = mysql_fetch_array($this->res);
        return $ret;
    }

    public function fetchObject() 
    {
        return mysql_fetch_object($this->res);
    }

    public function fetchRow() 
    {
        return mysql_fetch_row($this->res);
    }

    function result($row = 0, $field = 0) 
    {
        mysql_data_seek($this->res, $row);
        $datarow = mysql_fetch_array($this->res);
        return $datarow[$field];
    }

    function fetchFieldByName($columnName) 
    {
        $fields = $this->fetchFields($this->res);
        foreach ($fields as $field) {
            if ($field->name == $columnName) {
                return $field; 
            }
        }

        return null;
    }

    function fetchFields() 
    {
        $fields = Array();
        
        while($field = mysql_fetch_fields($this->res)) {
            $fields[] = $field; 
        }
        
        return $fields;
    }

    public function close() 
    {
    }

    public function __destruct() 
    {
    }

    // Easy access to parent function
    public function getErrorList() 
    {
        return getVar("sql")->getErrorList();
    }

}

?>