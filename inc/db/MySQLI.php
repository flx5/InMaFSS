<?php
/*=================================================================================*\
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
\*=================================================================================*/

class _MySQLI extends SQL {

    private $connected = false;
    private $hostname = "localhost";
    private $username = "root";
    private $password = "pass";
    private $database = "uberdb";
    private $link;
    private $count = 0;
    private $requests = Array();

    public function __construct($host, $user, $pass, $db) {
        $this->connected = false;
        $this->hostname = $host;
        $this->username = $user;
        $this->password = $pass;
        $this->database = $db;
    }

    public function IsConnected() {
        if ($this->connected) {
            return true;
        }

        return false;
    }

    public function Connect() {
        $this->link = @new mysqli($this->hostname, $this->username, $this->password, $this->database);

        if (PHP_VERSION_ID < 50300) {
            $err = mysqli_connect_error();
        } else {
            $err = $this->link->connect_error;
        }

        if ($err) {
            $this->Error('Connect Error (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
            return;
        }

        $this->connected = true;
    }

    public function Disconnect() {
        if ($this->connected) {
            $this->link->close() or $this->error("could not close conn");
            $this->connected = false;
        }
    }

    public function DoQuery($query) {
        $this->requests[] = $query;
        $resultset = $this->link->query($query, MYSQLI_STORE_RESULT) or $this->error($this->link->error);

        if ($resultset === true)
            return true;

        if ($resultset === false)
            return false;

        return new _MYSQLI_Result($resultset);
    }
    
    public function insertID() {
        return $this->link->insert_id;
    }
    
    public function affected_rows() {
        return $this->link->affected_rows;
    }
    
    public function real_escape_string($strInput = '')
    {
        return $this->link->real_escape_string($strInput);
    }

    public function __destruct() {
        $this->disconnect();
    }

    public function GetCount() {
        return count($this->requests);
    }

    public function GetRequests() {
        return $this->requests;
    }

}

class _MYSQLI_Result {

    private $res;
    private $closed;

    public function __construct($result) { 
        $this->res = $result;
        $this->closed = true; // Use this only if the query uses MYSQLI_USE_RESULT
    }

    public function count() {
        return $this->res->num_rows;
    }

    public function fetchAssoc() {
        $ret = $this->res->fetch_assoc();
        return $ret;
    }
    
    public function fetchArray() {
        $ret = $this->res->fetch_array();
        return $ret;
    }

    public function fetchObject() {
        return $this->res->fetch_object();
    }
    
    public function fetchRow() {
        return $this->res->fetch_row();
    }

    function result($row = 0, $field = 0) {
        $this->res->data_seek($row);
        $datarow = $this->res->fetch_array();
        return $datarow[$field];
    }  

    public function close() {
        if ($this->closed)
            return;

        $this->res->close();
        $this->closed = true;
    }

    public function __destruct() {
        $this->close();
    }

}

?>