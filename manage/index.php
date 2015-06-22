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

define('LOGIN', true);
require_once "global.php";

$auth = Authorization::GenerateInstance('DB');
/* @var $auth DB_Auth */
if ($auth->IsLoggedIn() && $auth->HasFuse('manage')) {
    header("Location: admin.php");
    exit;
}

setcookie('test', true);

lang()->add('admin');

getVar("tpl")->Init(lang()->loc('title', false));
getVar("tpl")->addStandards('admin');
getVar("tpl")->setParam('error', '');

if (isset($_POST['usr']) && isset($_POST['pwd'])) {
    if (!isset($_COOKIE['test'])) {
        getVar("tpl")->addTemplate('manage/no_cookies');
    } else {

        $usr = $_POST['usr'];
        $pwd = $_POST['pwd'];

        if ($auth->Login($usr, $pwd)) {
            if ($auth->HasFuse('manage')) {
                header("Location: admin.php");
                exit;
            } else {
                getVar("tpl")->setParam('error', '<font color="#FF0000">' . lang()->loc('no.fuse', false) . '</font><br><br>');
            }
        } else {
            getVar("tpl")->setParam('error', '<font color="#FF0000">' . lang()->loc('wrong', false) . '</font><br><br>');
        }
    }
}

getVar("tpl")->addTemplate('manage/manage');
getVar("tpl")->Output();
?>