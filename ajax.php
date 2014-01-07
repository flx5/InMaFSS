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

if (!isset($_GET['limit']) || !is_numeric($_GET['limit']))
{
    header("HTTP/1.1 403");
    exit;
}

require_once("global.php");
lang()->add('home');

$limit = $_GET['limit'];

require_once("inc/view.php");

$data = Array();

$left = getVar("tpl")->getTemplate('plan');
$left->setVar('site', 'left');
$view_left = new view('left', $limit);
$view_left->type = (isset($_GET['teacher']) ? 1 : 0);
$left->setVar('view', $view_left);

$data['left'] = $left->GetHtml();

$right = getVar("tpl")->getTemplate('plan');
$right->setVar('site', 'right');
$view_right = new view('right', $limit);
$view_right->type = (isset($_GET['teacher']) ? 1 : 0);
$right->setVar('view', $view_right);

$data['right'] = $right->GetHtml();

getVar("tpl")->Write('<div id="footer">');
$footer = getVar("tpl")->getTemplate('footer');
$footer->setVar('view_left', $view_left);
$footer->setVar('view_right', $view_right);

$data['footer'] = $footer->GetHtml();

echo json_encode($data);
?>
