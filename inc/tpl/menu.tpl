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

<div class="menu">
<h1>InMaFSS</h1>
<font color="#C0C0C0" size="+1" style="padding:4px;" >Information Management for School Systems</font><br><br>
<?php
lang()->add('menu');

$www = substr(WWW,strpos(WWW,'//')+2);
$www = substr($www, strpos($www,'/'));
$current = preg_replace('#'.$www.'/manage/#',"",$_SERVER["REQUEST_URI"]);
if(strpos($current,'?') !== false) {
   $current = substr($current,0,strpos($current,'?'));
}

echo '<a '. (($current == 'admin.php') ? 'class="selected"' : '').' href="admin.php" >'.lang()->loc('home',false).'</a>';
echo '<a '. (($current == 'ticker.php') ? 'class="selected"' : '').' href="ticker.php" >'.lang()->loc('ticker',false).'</a>';
echo '<a '. (($current == 'pages.php') ? 'class="selected"' : '').' href="pages.php" >'.lang()->loc('pages',false).'</a>';
echo '<a '. (($current == 'import.php') ? 'class="selected"' : '').' href="import.php" >'.lang()->loc('import',false).'</a>';
?>

</div>
