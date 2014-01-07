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


require_once("global.php");

lang()->add('home');
getVar("tpl")->Init(lang()->loc('title', false));
getVar("tpl")->addTemplate('clock');
getVar("tpl")->addTemplate('header');
getVar("tpl")->addHeader('<link rel="stylesheet" type="text/css" href="' . WWW . '/teacher.css">');

getVar("tpl")->Write('<div class="main" id="plan_left" style="border-right:0px solid black;" >');
if (!isset($_GET['size']) || !is_numeric($_GET['size'])) {
    getVar("tpl")->Write('</div>');

    getVar("tpl")->Write('
        <script language="JavaScript">
        SetHeight();
        </script>');
} elseif ($_GET['size'] < 533) {
    getVar("tpl")->addTemplate('too_small');
} else {

    $size = $_GET['size'];
    $limit = floor(($size - 40) / 20) - 2;

    require_once("inc/view.php");

    $left = getVar("tpl")->getTemplate('plan');
    $left->setVar('site', 'left');
    $view_left = new view('left', $limit);
    $view_left->type = 1;
    $left->setVar('view', $view_left);
    getVar("tpl")->addTemplateClass($left);
    getVar("tpl")->Write('</div>');

    getVar("tpl")->Write('<div class="main tomorrow" id="plan_right" style="right:0px; border-left:0px solid black;" >');
    $right = getVar("tpl")->getTemplate('plan');
    $right->setVar('site', 'right');
    $view_right = new view('right', $limit);
    $view_right->type = 1;
    $right->setVar('view', $view_right);
    getVar("tpl")->addTemplateClass($right);
    getVar("tpl")->Write('</div>');

    getVar("tpl")->Write('
<script language="JavaScript">
Init(' . config("teacher_time_for_next_page") . ', "' . config("updateStyle") . '", ' . $limit . ', true);
</script>');
}

getVar("tpl")->Write('<noscript>');
getVar("tpl")->addTemplate('no_js');
getVar("tpl")->Write('</noscript>');
getVar("tpl")->Output();
?>