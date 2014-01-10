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
<div class="round" style="width:60%; border:2px solid black; margin:5px auto;">
<h2>Information</h2>
<?php
  require_once("../inc/view.php");
  $left = new view('left', 100);
  $today['repl_count'] = count($left->GetReplacements());

  $right = new view('right', 100);
  $nextday['repl_count'] = count($right->GetReplacements());

  $right->GetPages();
  $left->GetPages();


  $left1 = dbquery("SELECT null FROM  replacements WHERE timestamp >= ".gmmktime(0,0,0)." AND timestamp <= ".gmmktime(23,59,59, date("n"), date("j")))->count();
  $right1 = dbquery("SELECT null FROM  replacements WHERE timestamp >= ".gmmktime(0,0,0, date("n"), date("j")+1)." AND timestamp <= ".gmmktime(23,59,59, date("n"), date("j")+1))->count();

  $left2 = dbquery("SELECT null FROM pages WHERE timestamp_end >= ".gmmktime(0,0,0, date("n"), date("j"))." AND timestamp_from <= ".gmmktime(0,0,0, date("n"), date("j")+1))->count();
  $right2 = dbquery("SELECT null FROM  pages WHERE timestamp_end >= ".gmmktime(0,0,0, date("n"), date("j")+1)." AND timestamp_from <= ".gmmktime(0,0,0, date("n"), date("j")+2))->count();

  echo '<b>'.lang()->loc('today',false).'</b><br>';

  if($today['repl_count'] == 0) {
      lang()->loc('no.plan');
      echo "<br>";
  }  else {
      echo $today['repl_count'].' ';
      lang()->loc('replacements');
      echo '<br>';
  }

  if(count($left->pages) == 0) {
      lang()->loc('no.page');
      echo "<br>";
  } else {
      echo count($left->pages).' ';
      lang()->loc('pages');
      echo '<br>';
  }

  echo '<br><b>'.lang()->loc('next.day',false).'</b><br>';

  if($nextday['repl_count'] == 0) {
      lang()->loc('no.plan');
      echo "<br>";
  }  else {
      echo $nextday['repl_count'].' ';
      lang()->loc('replacements');
      echo '<br>';
  }

  if(count($right->pages) == 0) {
      lang()->loc('no.page');
      echo "<br>";
  } else {
      echo count($right->pages).' ';
      lang()->loc('pages');
      echo '<br>';
  }
?>
</div>
