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

require_once("global.php");

new API();

class API {

    private $data = Array();

    public function __construct() {
        header('content-type: application/json; charset=utf-8');

        if (!isset($_GET['key']) || !$this->CheckAuth($_GET['key'])) {
            $this->Error("You have not been authenticated!");
        }

        if (!isset($_GET['action'])) {
            $this->Error("No action specified");
        }

        if (!$this->HasPerm($_GET['action'])) {
            $this->Error("This action does not exist or you don't have sufficient rights for this action");
        }

        switch ($_GET['action']) {
            case 'plan_update':
                if (!isset($_POST['data'])) {
                    $this->Error("No file content found!");
                }

                $_POST['data'] = urldecode($_POST['data']);

                $files = explode(chr(1), $_POST['data']);
                $p = new parse();

                foreach ($files as $file) {
                    # $file = stripslashes($file);
                    $file = utf8_decode($file);
                    $file = substr($file, strpos($file, "<html>"));
                    $p->parseHTML($file);
                }

                $p->UpdateDatabase();
                $this->Output(Array('STATUS' => "OK", 'message' => 'Import completed'));
                break;

            case 'replacements':

                if (!$this->HasPerm('replacements_all') && !isset($_GET['g'])) {
                    $this->Error("You must provide a Grade!");
                }

                $view = $this->GetView();
                $view->AddRepacements();

                $output = Array();

                foreach ($view->replacements as $page) {
                    foreach ($page as $grade => $val) {
                        if (!isset($_GET['g']) || $grade == $_GET['g']) {
                            foreach ($val as $k => $v) {
                                $val[$k]['comment'] = preg_replace("/&nbsp;/", "", htmlentities($v['comment']));
                                $val[$k]['replacement'] = preg_replace("/&nbsp;/", "", htmlentities($v['replacement']));
                            }
                            $output[$grade] = $val;
                        }
                    }
                }

                $this->Output($output);
                break;

            case 'other':

                if (!isset($_GET['type'])) {
                    $this->Error("Specify a type");
                }

                $view = $this->GetView();
                $view->type = 1;
                $view->AddRepacements();

                $output = Array();


                foreach ($view->replacements[1] as $k => $val) {
                    if ($k == $_GET['type']) {
                        $output[$k] = $val;
                    }
                }


                $this->Output($output);

                break;

            case 'teacher_sub':
                $view = $this->GetView();
                $view->type = 1;
                $view->AddRepacements();

                $output = Array();

                foreach ($view->replacements as $page) { // Can only be one page!
                    foreach ($page as $k => $val) {
                        switch ($k) {
                            case 't':
                            case 'n':
                            case 'g':
                            case 's':
                            case 'a':
                            case 'r':
                                continue;
                                break;

                            default:
                                if (!isset($_GET['short']) || $k == $_GET['short']) {                                 
                                    foreach($val as $i=>$entry) {   
                                        foreach($entry as $f=>$x) 
                                            $val[$i][$f] = html_entity_decode($x, ENT_COMPAT, "UTF-8");
                                    }
                        
                                    $output[$k] = $val;
                                }
                                break;
                        }
                    }
                }

                $this->Output($output);
                break;

            case 'ticker':
                $view = $this->GetView();
                $this->Output($view->GetTickers());

                break;

            default:
                $this->Error("Unknown action!");
                break;
        }
    }

    function Error($msg) {
        $this->Output(Array('STATUS' => "ERROR", 'message' => $msg));
        exit;
    }

    function Output($output) {
        $output = getVar('core')->FormatJson(json_encode($output));
        echo $output;
    }

    function CheckAuth($api) {

        if (isset($_GET['licence'])) {
            if (strpos(file_get_contents("http://licence.flx5.com/inmafss.php?ver=" . getVersion() . "&licence=" . $_GET['licence']), "OK") !== false) {
                $this->data = Array("all");
            }
        }

        $api = filter($api);
        $sql = dbquery("SELECT permissions FROM api WHERE apikey = '" . $api . "'");

        if (mysql_num_rows($sql) != 1) {
            return false;
        }

        $data = mysql_result($sql, 0);
        $data = explode("|", $data);
        $this->data = $data;
        return true;
    }

    function HasPerm($perm) {
        return true; // todo!
        return in_array($perm, $this->data);
    }

    function GetView() {
        if (!isset($_GET['day']) || !is_numeric($_GET['day']) || strlen($_GET['day']) != 10) {
            $this->Error("Day must be Unix Timestamp");
        }

        require_once("inc/view.php");

        $day = $_GET['day'];

        $tfrom = gmmktime(0, 0, 0, date('m', $day), date('d', $day), date('Y', $day));

        $view = new View(null, 99e99);
        $view->tfrom = $tfrom;
        return $view;
    }

}

?>