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

 lang()->add('updates'); ?>
<div class="round" style="width:60%; border:2px solid black; margin:5px auto;">
<h2><?php lang()->loc('title'); ?></h2><br>
<div class="inner">
<ul>
<?php
  $update = getVar("update")->GetLatest();
  if($update === false) {
     echo '<li>'.lang()->loc('no.updates',false).'</li>';
  } else {
     echo '<a href="update.php">'.lang()->loc('update.to.version',false).' '.$update['name'].'</a><br>';
  }
?>
</div></div>
