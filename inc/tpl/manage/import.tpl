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
<div class="round" style="width:90%; border:2px solid black; margin:5px auto; margin-top:20px; text-align:center;">
<h2><?php lang()->loc('title'); ?></h2>
<font color="#00FF00" size="+1">%msg%</font><br>
<form enctype="multipart/form-data" action="" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
<?php lang()->loc('file.upload'); ?>: <input name="uploadedfile" type="file" /><br />  <br />
<select name="type">
    <option value="plan" selected="selected"><?php lang()->loc('plan'); ?></option>
    <option value="mensa"><?php lang()->loc('mensa'); ?></option>
</select>
<input type="submit" value="<?php lang()->loc('upload'); ?>" />
</form>
</div>
</div>
