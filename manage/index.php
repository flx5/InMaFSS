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


require_once("../global.php");

if(LOGGED_IN) {
  header("Location: admin.php");
  exit;
}

lang()->add('admin');

getVar("tpl")->Init(lang()->loc('title',false));
getVar("tpl")->setParam('error','');

if(isset($_POST['usr']) && isset($_POST['pwd'])) {
      $usr = filter($_POST['usr']);
      $pwd = filter($_POST['pwd']);
      $pwd = getVar("core")->generatePW($usr,$pwd);

      $sql = dbquery("SELECT id FROM users WHERE username = '".$usr."' AND password = '".$pwd."' LIMIT 1");
      if($sql->count() == 0) {
         getVar("tpl")->setParam('error','<font color="#FF0000">'.lang()->loc('wrong',false).'</font><br><br>');
      } else {
          $_SESSION['user'] = $usr;
          $_SESSION['timestamp'] = time();
          header("Location: admin.php");
      }
}



if(isset($_GET['cookies']) && $_GET['cookies'] == "no") {
getVar("tpl")->addTemplate('manage/no_cookies');
}

getVar("tpl")->addTemplate('manage/manage');
getVar("tpl")->Output();
?>