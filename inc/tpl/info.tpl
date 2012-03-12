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

 lang()->add('info'); ?>
<div style="width:60%; border:2px solid black; margin:5px auto;">
<h2>Information</h2>
<?php

  $left1 = mysql_num_rows(dbquery("SELECT null FROM  replacements WHERE timestamp >= ".mktime(0,0,0)." AND timestamp <= ".mktime(23,59,59, date("n"), date("j"))));
  $right1 = mysql_num_rows(dbquery("SELECT null FROM  replacements WHERE timestamp >= ".mktime(0,0,0, date("n"), date("j")+1)." AND timestamp <= ".mktime(23,59,59, date("n"), date("j")+1)));

  $left2 = mysql_num_rows(dbquery("SELECT null FROM pages WHERE timestamp_end >= ".mktime(0,0,0, date("n"), date("j"))." AND timestamp_from <= ".mktime(0,0,0, date("n"), date("j")+1)));
  $right2 = mysql_num_rows(dbquery("SELECT null FROM  pages WHERE timestamp_end >= ".mktime(0,0,0, date("n"), date("j")+1)." AND timestamp_from <= ".mktime(0,0,0, date("n"), date("j")+2)));

  echo '<b>'.lang()->loc('today',false).'</b><br>';

  if($left1 == 0) {
      lang()->loc('no.plan');
      echo "<br>";
  }  else {
      echo $left1.' ';
      lang()->loc('replacements');
      echo '<br>';
  }

  if($left2 == 0) {
      lang()->loc('no.page');
      echo "<br>";
  } else {
      echo $left2.' ';
      lang()->loc('pages');
      echo '<br>';
  }

  echo '<br><b>'.lang()->loc('tomorrow',false).'</b><br>';

  if($right1 == 0) {
      lang()->loc('no.plan');
      echo "<br>";
  }  else {
      echo $right1.' ';
      lang()->loc('replacements');
      echo '<br>';
  }

  if($right2 == 0) {
      lang()->loc('no.page');
      echo "<br>";
  } else {
      echo $right2.' ';
      lang()->loc('pages');
      echo '<br>';
  }
?>
</div>
