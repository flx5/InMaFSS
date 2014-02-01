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

define('DS', DIRECTORY_SEPARATOR);
define('CWD', dirname(__FILE__) . DS);
define('INC', CWD . "inc" . DS);
define('PLUGIN_DIR', CWD . DS . "plugins" . DS);

$www = "http";

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off")
    $www .= "s";

$www .= "://".$_SERVER['SERVER_NAME'] .$_SERVER['REQUEST_URI'];
$www = substr($www, 0, strrpos($www, basename(dirname(__FILE__)))).basename(dirname(__FILE__));
define('WWW', $www);

#register_shutdown_function('Shutdown');
set_error_handler('error_handler');
date_default_timezone_set('Europe/Berlin');

header('Content-Type: text/html');

if (file_exists(CWD . "install.php") && file_exists(CWD . "inc/config.php")) {
   # die("ERROR: YOU HAVE TO REMOVE THE install.php BEFORE YOU WILL BE ABLE TO USE THIS!");
}

if (!file_exists(CWD . "inc/config.php") && !file_exists(CWD . "install.php")) {
    die("ERROR: CONFIG AND INSTALLER NOT FOUND!");
}

if (!file_exists(CWD . "inc/config.php") && file_exists(CWD . "install.php")) {
    header("Location: " . WWW . "/install.php");
    exit;
}

require_once(INC."class.variables.php");
require_once(INC."class.config.php");
require_once(INC."class.core.php");
require_once(INC."class.sql.php");
require_once(INC."class.lang.php");
require_once(INC."class.tpl.php");
require_once(INC."class.update.php");
require_once(INC."class.plugin.php");
require_once(INC."class.parse.php");
require_once(INC."class.OAuthHelper.php");

core::MagicQuotesCompability();

$config = new config();
$vars = new variables(new core(), null, null, new tpl(), new Update(), new pluginManager(), false);
$vars->set("sql", SQL::GenerateInstance($config->Get("dbtype"), $config->Get("dbhost"), $config->Get("dbusr"), $config->Get("dbpass"), $config->Get("dbname")));
vars::Init($vars);

getVar("sql")->connect();
$config->LoadFromDB();

$vars->Set("lang", new lang($config->Get("lang")));
getVar("pluginManager")->Init();
getVar("update")->Init();


session_start();

if (isset($_SESSION['user']) && isset($_SESSION['timestamp']) && isset($_SESSION['userID'])) {
    define('LOGGED_IN', true);
    define('USERNAME', $_SESSION['user']);
    define('USER_ID', $_SESSION['userID']);
} else {
    define('LOGGED_IN', false);
    define('USERNAME', 'GUEST');
    define('USER_ID', -1);
}

function lang() {
    return getVar("lang");
}

function filter($input) {
    return getVar("core")->filter($input);
}

function dbquery($input) {
    if (getVar("PLUGIN") || !getVar("sql")->IsConnected()) {
        return null;
    }

    return getVar("sql")->DoQuery($input);
}

function config($var) {
    if (getVar("PLUGIN")) {
        return null;
    }

    global $config;
    return $config->Get($var);
}

class vars {
    private static $vars;

    public static function Init($vars) {
        self::$vars = $vars;
    }
    
    public static function getVar($var) {
        return self::$vars->Get($var);
    }

    public static function setVar($var, $val) {
        self::$vars->Set($var, $val);
    }
    
    public static function get() {
        return self::$vars;
    }
}

function getVar($var) {
    return vars::getVar($var);
}

function setVar($var, $val) {
    vars::setVar($var, $val);
}

function setPlugin($val, $actor) {
    global $vars;
    vars::get()->setPlugin($val, $actor);
}

function getVersion() {
    include("inc/version.php");
    return $version;
}

function error_handler($errno, $errstr, $errfile, $errline) {
    if (error_reporting() == 0)
        return true; // Ignore Messages with an @ before!

    return false;
}

function Shutdown() {
    $error = error_get_last();
    if ($error != null) {
        ob_end_clean();
        core::SystemError($error['message'], ' in ' . $error['file'] . ' on line ' . $error['line']);
    }
}

?>