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


require_once "global.php";

lang()->add('import');

$msg = "";
$msg_color = "00ff00";

if (isset($_FILES['uploadedfile']) && isset($_POST['type'])) {
    if ($_FILES['uploadedfile']['error'] != UPLOAD_ERR_OK) {
        $msg = core::UploadCodeToMessage($_FILES['uploadedfile']['error']);
        $msg_color = "ff0000";
    } else {
        switch ($_POST['type']) {
        case 'plan':
            $p = new parsePlan();
            $success = $p->parse($_FILES['uploadedfile']['tmp_name']);
            if ($success) {
                $p->UpdateDatabase();
                $msg = lang()->loc('success', false);
            } else {
                $msg = lang()->loc('parse.fail', false);
                $msg_color = 'ff0000';
            }
            break;
        case 'mensa':
            $p = new parseMensa();
            $success = $p->parse($_FILES['uploadedfile']['tmp_name']);
            if ($success) {
                $p->UpdateDatabase();
                $msg = lang()->loc('success', false);
            } else {
                $msg = lang()->loc('parse.fail', false);
                $msg_color = 'ff0000';
            }
            break;
        case 'appointments':
            $p = new parseAppointments();
            $success = $p->parse($_FILES['uploadedfile']['tmp_name']);
            if ($success) {
                $p->UpdateDatabase();
                $msg = lang()->loc('success', false);
            } else {
                $msg = lang()->loc('parse.fail', false);
                $msg_color = 'ff0000';
            }
            break;
        }
    }
}
getVar("tpl")->Init(lang()->loc('title', false));
getVar("tpl")->setParam("msg_color", $msg_color);
getVar("tpl")->setParam("msg", $msg);
getVar("tpl")->addStandards('admin');
getVar("tpl")->addTemplate('manage/import');
getVar("tpl")->Output();
?>