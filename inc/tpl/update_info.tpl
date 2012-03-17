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
<div style="width:60%; border:2px solid black; margin:5px auto;">
<h2><?php lang()->loc('title'); ?></h2><br>
<ul>
<?php
  global $update;
  $updates = $update->GetUpdates();
  if(count($updates) == 0) {
     echo '<li>'.lang()->loc('no.updates',false).'</li>';
  } else {
     end($updates);
     $key = key($updates);
                              
     echo '<a href="update.php?v='.$key.'">'.lang()->loc('update.to.version',false).' '.$key.'</a><br>';
     echo '<h3>'.lang()->loc('missed.updates',false).'</h3>';
  }

  foreach($updates as $ver=>$url) {
        echo '<li>'.$ver.'</li>';
  }

?>
</div>