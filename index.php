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


require_once("global.php");

lang()->add('home');
$tpl->Init(lang()->loc('title',false));
$tpl->addTemplate('clock');
$tpl->addTemplate('header');

$tpl->Write('<div class="main" id="plan_left" style="border-right:0px solid black;" >');
if(!isset($_GET['size']) || !is_numeric($_GET['size'])) {
      $tpl->Write('</div>');
} elseif($_GET['size'] < 533) {
      $tpl->addTemplate('too_small');
} else {

$size = $_GET['size'];
$limit = floor(($size-30)/26)-2;

require_once("inc/view.php");

$left = $tpl->getTemplate('plan');
$left->setVar('site','left');
$view_left = new view('left', $limit);
$left->setVar('view',$view_left);
$tpl->addTemplateClass($left);
$tpl->Write('</div>');

$tpl->Write('<div class="main tomorrow" style="right:0px; border-left:0px solid black;" >');
$right = $tpl->getTemplate('plan');
$right->setVar('site','right');
$view_right = new view('right', $limit);
$right->setVar('view',$view_right);
$tpl->addTemplateClass($right);
$tpl->Write('</div>');
}

$tpl->Write('
<script language="JavaScript">
Init();
</script>
<noscript>');

$tpl->addTemplate('no_js');
$tpl->Write('</noscript>');

$footer = $tpl->getTemplate('footer');
$footer->setVar('view_left',$view_left);
$footer->setVar('view_right',$view_right);
$tpl->addTemplateClass($footer);
$tpl->Output();
?>