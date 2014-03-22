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

lang()->add('updates');

getVar("tpl")->Init(lang()->loc('title', false));
getVar("tpl")->addStandards('admin');
getVar('tpl')->AddJS('update.js');

getVar('tpl')->addHeader('<script type="text/javascript">Update.Init();</script>');

getVar("tpl")->Write('<div class="content"><div class="round" style="width:60%; border:2px solid black; margin:30px auto;">');

getVar("tpl")->Write('<div id="status_bar_bg"><div id="status_bar"></div><div id="status_bar_content"></div></div>');

$version = getVar('update')->GetLatest(false);

$success = false;

if ($version !== false) {
    getVar("tpl")->Write('<div id="statusValue" style="display:none">');
    $file = getVar('update')->Download($version['id']);
    getVar("tpl")->Write('</div>');

    if ($file !== false) {
        getVar("tpl")->Write('<div class="update_log"><ul>');
        $update = getVar('update')->Unpack($file);
        getVar("tpl")->Write('</ul></div>');
        if ($update) {
            getVar('tpl')->Write(lang()->loc('success', false));
            $success = true;
        }
    }
} 

if (!$success)
    getVar('tpl')->Write('<font color="#ff0000">' . lang()->loc('failure', false) . '</font>');

getVar("tpl")->Write('</div></div>');
getVar("tpl")->Output();
?>