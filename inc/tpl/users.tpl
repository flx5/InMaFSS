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
?>

<div class="content">
<div style="width:90%; border:2px solid black; margin:5px auto; margin-top:20px; text-align:center;">
<h2><?php lang()->loc('title'); ?></h2>
<?php
  if(isset($_GET['del']) && is_numeric($_GET['del']) && (!isset($_GET['do']) || $_GET['do'] != 'del')) {
      $sql = dbquery("SELECT username FROM users WHERE id = ".$_GET['del']);
      if(mysql_num_rows($sql) == 0) {
              echo '<font size="+2" color="#FF0000">'.lang()->loc('not.found',false).'</font>';
      } elseif(mysql_result($sql,0) == USERNAME) {
              echo '<font size="+2" color="#FF0000">'.lang()->loc('del.self',false).'</font>';
      } else {
              echo '<font size="+2" color="#FF0000">'.lang()->loc('del.rly',false).' <a href="?del='.$_GET['del'].'&do=del">'.lang()->loc('delete',false).'</a>&nbsp;|&nbsp;<a href="pages.php">'.lang()->loc('abort',false).'</a></font>';
      }
  }

  if(isset($_GET['del'])  && isset($_GET['do']) && $_GET['do'] == 'del' && is_numeric($_GET['del'])) {
          dbquery("DELETE FROM users WHERE id = ".$_GET['del']." AND username != '".USERNAME."'");
          echo '<font size="+2" color="#00ff00">'.lang()->loc('deleted',false).'</font>';
  }
?>
<table width="100%" border="1">
<tr><th width="10%"><?php lang()->loc('id'); ?></th><th><?php lang()->loc('name'); ?></th><th colspan="2" width="30%" >Optionen</th></tr>
<?php
      $users = dbquery("SELECT id,username FROM users");
      while($user = mysql_fetch_array($users)) {
          echo '<tr><td>'.$user['id'].'</td><td>'.$user['username'].'</td>';
          echo  '<td><a href="uedit.php?id='.$user['id'].'">'.lang()->loc('edit',false).'</a></td><td><a href="?del='.$user['id'].'">'.lang()->loc('delete',false).'</a></td></tr>';
      }
?>
<tr><td></td><td></td><td colspan="2"><a href="uedit.php?new">NEU</a></td></tr>
</table>
</div></div>