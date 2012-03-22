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

define('DS', DIRECTORY_SEPARATOR);
define('CWD', str_replace('manage' . DS, '', dirname(__FILE__) . DS));

$www = str_replace('manage', '', $_SERVER['REQUEST_URI']);
$www = substr($www,0, strrpos($www,"/"));
if(substr($www,strlen($www)-1) == "/") {
     $www = substr($www,0, -1);
}
define('WWW', $www);

if(file_exists(CWD."install.php") && file_exists(CWD."inc/config.php")) {
     die("ERROR: YOU HAVE TO REMOVE THE install.php BEFORE YOU WILL BE ABLE TO USE THIS!");
}

if(!file_exists(CWD."inc/config.php") && !file_exists(CWD."install.php")) {
     die("ERROR: CONFIG AND INSTALLER NOT FOUND!");
}

if(!file_exists(CWD."inc/config.php") && file_exists(CWD."install.php")) {
     header("Location: ".WWW."/install.php");
     exit;
}

require_once("inc/config.php");
require_once("inc/core.php");
require_once("inc/sql.php");
require_once("inc/lang.php");
require_once("inc/tpl.php");
require_once("inc/update.php");

$core = new core();
$lang = new lang($clang);
$sql = new MySQL();
$tpl = new tpl();
$update = new Update();

$sql->connect($dbhost, $dbusr, $dbpass, $dbname);

session_start();

if(isset($_SESSION['user']) && isset($_SESSION['timestamp'])) {
 define('LOGGED_IN',true);
 define('USERNAME', $_SESSION['user']);
} else {
 define('LOGGED_IN',false);
 define('USERNAME', 'GUEST');
}

function lang() {
  global $lang;

  return $lang;
}

function filter($input) {
  global $core;
  return $core->filter($input);
}

function dbquery($input) {
   global $sql;
   return $sql->dbquery($input);
}

function config($var) {
   global $$var;
   return $$var;
}
?>