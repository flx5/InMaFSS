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
getVar("tpl")->addStandards('plan');
getVar("tpl")->addTemplate('clock');
getVar("tpl")->addTemplate('plan/header');

getVar("tpl")->Write('<div class="main" id="plan_left" style="border-right:0px solid black;" >');
if (!isset($_GET['size']) || !is_numeric($_GET['size'])) {
    getVar("tpl")->Write('</div>');

    getVar("tpl")->Write('
        <script language="JavaScript">
        SetHeight();
        </script>');
    
} elseif ($_GET['size'] < 533) {
    getVar("tpl")->addTemplate('plan/too_small');
} else {

    $size = $_GET['size'];
    $limit = floor(($size-50 ) / 25) - 4;

    require_once(INC."class.view.php");

    $left = getVar("tpl")->getTemplate('plan/plan');
    $left->setVar('site', 'left');
    $view_left = new view('left', $limit);
    $left->setVar('view', $view_left);
    getVar("tpl")->addTemplateClass($left);
    getVar("tpl")->Write('</div>');

    getVar("tpl")->Write('<div class="main tomorrow" id="plan_right" style="right:0px; border-left:0px solid black;" >');
    $right = getVar("tpl")->getTemplate('plan/plan');
    $right->setVar('site', 'right');
    $view_right = new view('right', $limit);
    $right->setVar('view', $view_right);
    getVar("tpl")->addTemplateClass($right);
    getVar("tpl")->Write('</div>');

    getVar("tpl")->Write('<div id="footer">');
    $footer = getVar("tpl")->getTemplate('plan/footer');
    $footer->setVar('view_left', $view_left);
    $footer->setVar('view_right', $view_right);
    getVar("tpl")->addTemplateClass($footer);
    getVar("tpl")->Write('</div>');

    getVar("tpl")->Write('
        <script language="JavaScript">
        Init(' . config("time_for_next_page") . ', "' . config("updateStyle") . '", ' . $limit . ', false);
        </script>');
}

getVar("tpl")->Write('<noscript>');
getVar("tpl")->addTemplate('plan/no_js');
getVar("tpl")->Write('</noscript>');

getVar("tpl")->Output();
?>