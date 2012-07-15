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

header('content-type: application/json; charset=utf-8');

if(trim(config("apikey")) == "") {
    die("NO API KEY SET!");
}



      if(isset($_GET['licence'])) {
               if(file_get_contents("http://licence.flx5.com/inmafss.php?ver=".getVersion()."&licence=".$_GET['licence']) == "OK") {
                           $_GET['key'] = config("apikey");
               }
      }

      if(!isset($_GET['key']) || $_GET['key'] != config("apikey")) {
           Error("You have not been authenticated!");
      }

      if(!isset($_GET['action'])) {
           Error("No action specified");
      }

      switch($_GET['action']) {
              case 'plan_update':
                      if(!isset($_POST['data'])) {
                            Error("No file content found!");
                      }
                      $files = explode(chr(1),$_POST['data']);
                      $p = new parse();

                      foreach($files as $file) {
                             $file = stripslashes(urldecode($file));
                             $file = substr($file, strpos($file,"<html>"));
                             $p->parseHTML($file);
                      }

                      $p->UpdateDatabase();
                      Output(Array('STATUS'=>"OK",'message'=>'Import completed'));
              break;

              case 'replacements':
                   $view = GetView();
                   $view->AddRepacements();

                   $output = Array();

                   foreach($view->replacements as $page) {
                              foreach($page as $grade=>$val) {
                                      if(!isset($_GET['g']) || $grade == $_GET['g']) {
                                            foreach($val as $k=>$v) {
                                                   $val[$k]['comment'] = preg_replace("/&nbsp;/","",htmlentities($v['comment']));
                                                   $val[$k]['replacement'] = preg_replace("/&nbsp;/","",htmlentities($v['replacement']));
                                            }
                                            $output[$grade] = $val;
                                      }
                              }
                   }

                   Output($output);
              break;

              case 'other':

                   if(!isset($_GET['type'])) {
                       Error("Specify a type");
                   }

                   $view = GetView();
                   $view->type = 1;
                   $view->AddRepacements();

                   $output = Array();


                   foreach($view->replacements[1] as $k=>$val) {
                          if($k == $_GET['type']) {
                               $output[$k] = $val;
                          }
                   }


                   Output($output);

              break;

              case 'teacher_sub':
                   $view = GetView();
                   $view->type = 1;
                   $view->AddRepacements();

                   $output = Array();


                   foreach($view->replacements[1] as $k=>$val) {
                          switch($k) {
                            case 't':
                            case 'n':
                            case 'g':
                            case 's':
                            case 'a':
                            case 'r':
                                continue;
                            break;

                            default:
                               $output[$k] = $val;
                            break;
                          }
                   }


                   Output($output);
              break;

              case 'ticker':
                   $view = GetView();
                   Output($view->GetTickers());

              break;

              default:
                     Error("Unknown action!");
              break;
      }


function Error($msg) {
   Output(Array('STATUS'=>"ERROR",'message'=>$msg));
   exit;
}

function Output($output) {
   $output = getVar('core')->FormatJson(json_encode($output));
   echo $output;
}

function GetView() {
      if(!isset($_GET['day']) || !is_numeric($_GET['day']) || strlen($_GET['day']) != 10) {
             Error("Day must be Unix Timestamp");
      }

      require_once("inc/view.php");

      $day = $_GET['day'];

      $tfrom = mktime(0,0,0, date('m',$day), date('d',$day), date('Y',$day));

      $view = new View(null,99e99);
      $view->tfrom = $tfrom;
      return $view;
}
?>