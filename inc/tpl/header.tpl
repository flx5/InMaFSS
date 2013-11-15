<?php
/* =================================================================================*\
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
  \*================================================================================= */
?>
<div id="header" class='bar'>   
    <span style="float:left; width:20%; text-align:left; padding-left:20px;"><?php echo config("schoolname"); ?></span>
    <span style="float:left; width:60%" id="header_cached"><?php lang()->loc('cache.warning'); ?></span>
    <span style="float:left; width:60%" id="header_normal">InMaFSS</span>
    <span id='clock' style="text-align:right; padding-right:20px; float:right;"><?php echo date("j.m.Y H:i:s"); ?></span>
</div>