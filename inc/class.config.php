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

class config {

    private $schoolname;
    private $dbtype;
    private $dbhost;
    private $dbusr;
    private $dbpass;
    private $dbname;
    private $salt;
    private $lang;
    private $auto_addition;
    private $time_for_next_page;
    private $teacher_time_for_next_page;
    private $use_ftp;
    private $ftp;
    private $system;
    private $spalten_t = Array('200px', '30px', '100px', '75px', '*');
    private $spalten = Array('75px', '75px', '30px', '180px', '75px', '*');

    public function config() {
        include(CWD . "inc/config.php");
        $this->dbtype = $dbtype;
        $this->dbhost = $dbhost;
        $this->dbusr = $dbusr;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;
        $this->salt = $salt;
    }

    public function LoadFromDB() {
        $val = dbquery("SELECT * FROM settings LIMIT 1")->fetchObject();

        $this->schoolname = $val->schoolname;
        $this->lang = $val->lang;
        $this->auto_addition = $val->auto_addition;
        $this->time_for_next_page = $val->time_for_next_page;
        $this->teacher_time_for_next_page = $val->teacher_time_for_next_page;
        $this->use_ftp = $val->use_ftp;

        $ftp = Array();
        $ftp['server'] = $val->ftp_server;
        $ftp['usr'] = $val->ftp_user;
        $ftp['pwd'] = $val->ftp_password;
        $ftp['path'] = $val->ftp_path;

        $this->ftp = $ftp;
        $this->system = $val->system;
    }

    public function Get($var) {
        return $this->$var;
    }

}

?>