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

namespace InMaFSS {

    require __DIR__ . '/vendor/autoload.php';


// TODO Time management!
    date_default_timezone_set('Europe/Berlin');

    if (is_dir(__DIR__ . "/installer") && file_exists(__DIR__ . "/inc/config.php") && !file_exists(__DIR__ . "/dev.txt")) {
        die("ERROR: YOU HAVE TO REMOVE THE installer folder BEFORE YOU WILL BE ABLE TO USE THIS!");
    }

    if (!file_exists(__DIR__ . "/inc/config.php")) {
        header("Location: ./installer/");
        exit;
    }

    Compability::magicQuotes();
    $config = new Config();

    \Propel::setConfiguration(
            array(
                'datasources' =>
                array(
                    'inmafss' =>
                    array(
                        'adapter' => $config->getDbAdapter(),
                        'connection' =>
                        array(
                            'dsn' => $config->getDbDSN(),
                            'user' => $config->getDbUser(),
                            'password' => $config->getDbPass(),
                        ),
                    ),
                    'default' => 'inmafss',
                )
            )
    );
    \Propel::initialize();
    
    Data\EventQuery::create()->find();

    $vars->set("sql", SQL::GenerateInstance($config->Get("dbtype"), $config->Get("dbhost"), $config->Get("dbusr"), $config->Get("dbpass"), $config->Get("dbname")));
    vars::Init($vars);

    getVar("sql")->connect();
    $config->LoadFromDB();

    $vars->Set("lang", new lang($config->Get("lang")));
    getVar("pluginManager")->Init();

    session_start();


    $user = Authorization::IsLoggedIn();
    if ($user != null) {
        define('LOGGED_IN', true);
        define('USERNAME', $user['name']);
        define('USER_ID', $user['id']);
    } else {
        define('LOGGED_IN', false);
        define('USERNAME', 'GUEST');
        define('USER_ID', -1);
    }
}