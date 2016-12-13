<?php

/*
 * Copyright (C) 2016 Felix Prasse <me@flx5.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace InMaFSS;

/**
 * Loads configuration and provides access to its' values.
 *
 * @author Felix Prasse <me@flx5.com>
 */
class Config {
    /*
     * $dbuser='';
$dbpass='';
$basepath='';
$dbname='';
$dbserver='';
$dbport='';
$dbtype='';
     */
    
    private $dbUser;
    private $dbPass;
    private $dbName;
    private $dbServer;
    private $dbPort;
    private $dbType;
    
    public function __construct() {
        require __DIR__.'/../config.php';
        
        $this->dbUser = $dbUser;
        $this->dbPass = $dbpass;
        $this->dbName = $dbname;
        $this->dbServer = $dbserver;
        $this->dbPort = $dbport;
        $this->dbType = $dbtype;
    }
    
    public function getDbAdapter() {
        return $this->dbType;
    }
    
    public function getDbDSN() {
        return $this->dbType . ":host=".$this->dbServer.";dbname=".$this->dbName;
    }
    
    public function getDbUser() {
        return $this->dbUser;
    }

    public function getDbPass() {
        return $this->dbPass;
    }
}
