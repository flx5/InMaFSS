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

<div class="login">
<h2><?php lang()->loc('title'); ?></h2>
%error%
<form method="post" action="">
          <label for="usr"><?php lang()->loc('username'); ?>:</label><input type="text" id="usr" name="usr"><br><br>
          <label for="pwd"><?php lang()->loc('password'); ?>:</label><input type="password" id="pwd" name="pwd"><br><br>
          <input type="submit" value="<?php lang()->loc('login'); ?>" style="background-color:#C0C0C0; border:1px solid #999999; width:200px; height:50px; font-size:2em;">
</form>
</div>
