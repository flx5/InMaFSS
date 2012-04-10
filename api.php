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

require_once("global.php");

if(trim($apikey) == "") {
    die("NO API KEY SET!");
}

      if(!isset($_GET['key']) ||  $_GET['key'] != $apikey) {
           die("ERR|You have not been authenticated!");
      }

      if(!isset($_GET['action'])) {
           die("ERR|You must specify an action!");
      }

      switch($_GET['action']) {
              case 'plan_update':
                      if(!isset($_POST['data'])) {
                            die("ERR|You have to POST the content of your files!");
                      }
                      $files = explode(chr(1),$_POST['data']);
                      $p = new parse();

                      foreach($files as $file) {
                             $file = stripslashes(urldecode($file));
                             $file = substr($file, strpos($file,"<html>"));
                             $p->parseHTML($file);
                      }

                      $p->UpdateDatabase();
                      echo "OK|";
              break;

              default:
                     die("ERR|Unknown action!");
              break;
      }
?>