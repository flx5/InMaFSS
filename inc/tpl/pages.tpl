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
  if(isset($_GET['del']) && (!isset($_GET['do']) || $_GET['do'] != 'del')) {
      echo '<font size="+2" color="#FF0000">'.lang()->loc('del.rly',false).' <a href="?del='.$_GET['del'].'&do=del">'.lang()->loc('delete',false).'</a>&nbsp;|&nbsp;<a href="pages.php">'.lang()->loc('abort',false).'</a></font>';
  }

  if(isset($_GET['del'])  && isset($_GET['do']) && $_GET['do'] == 'del' && is_numeric($_GET['del'])) {
          dbquery("DELETE FROM pages WHERE id = ".$_GET['del']);
          echo '<font size="+2" color="#00ff00">'.lang()->loc('deleted',false).'</font>';
  }
?>
<table width="100%" border="1">
<tr><th rowspan="2" ><?php lang()->loc('id'); ?></th><th rowspan="2"><?php lang()->loc('caption'); ?></th><th colspan="2" ><?php lang()->loc('shown'); ?></th><th colspan="2" rowspan="2">Optionen</th></tr>
<tr><th><?php lang()->loc('from'); ?></th><th><?php lang()->loc('until'); ?></th></tr>
<?php

$sql = dbquery("SELECT * FROM pages ORDER BY order_num");

while($page = mysql_fetch_assoc($sql)) {
    echo  '<tr><td>'.$page['id'].'</td><td>'.$page['title'].'</td><td>'.date('d.m.Y',$page['timestamp_from']).'</td><td>'.date('d.m.Y',$page['timestamp_end']).'</td>';
    echo  '<td><a href="pedit.php?id='.$page['id'].'">'.lang()->loc('edit',false).'</a></td><td><a href="?del='.$page['id'].'">'.lang()->loc('delete',false).'</a></td></tr>';
}
?>
<tr><td>&nbsp;</td><td></td><td></td><td></td><td colspan="2" ><a href="pedit.php?new">NEU</a></td></tr>
</table>
</div></div>
