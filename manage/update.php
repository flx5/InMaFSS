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

lang()->add('updates');

getVar("tpl")->Init(lang()->loc('title',false));
getVar("tpl")->addStandards('admin');
getVar("tpl")->addJS("update.js");

getVar("tpl")->Write('<div class="content">');

getVar("tpl")->Write('Die folgenden Dateien werden ver&auml;ndert:<ul id="liste">');

  $updates = getVar('update')->GetUpdates();
  $changes = getVar('update')->GetAllChanges($updates);

getVar("tpl")->Write('
<script language="JavaScript">
liste = new Array();');

  $i = 0;
  foreach($changes as $name=>$change) {
      getVar("tpl")->Write('liste['.$i.'] = new Array("'.$name.'", "'.$change['status'].'", "'.$change['url'].'");');
      $i++;
  }

getVar("tpl")->Write('DoUpdate(liste);</script></ul>');

getVar("tpl")->Output();

#echo '<div style="width:90%; margin:50px auto;">';

  #getVar('update')->PerformUpdate($changes);

#echo '</div>';
?>