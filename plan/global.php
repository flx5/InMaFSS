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

require_once(realpath(dirname(__FILE__)) . "/../global.php");
require_once(INC . 'libs/ip_in_range/ip_in_range.php');

lang()->add('admin');

$isAuth = false;
$error = '';

if (isset($_POST['usr']) && isset($_POST['pwd'])) {
    $usr = $_POST['usr'];
    $pwd = $_POST['pwd'];

    $auth = Authorization::GenerateInstance('DB');
    if ($auth->Login($usr, $pwd)) {
        if ($auth->HasFuse('plan')) {
            $_SESSION['plan_auth'] = true;
            $_SESSION['plan_auth_timeout'] = time() + 5 * 60;
            header("Location: index.php");
            exit;
        } else {
            $error = '<font color="#FF0000">' . lang()->loc('no.fuse', false) . '</font><br><br>';
        }
    } else {
        $error = '<font color="#FF0000">' . lang()->loc('wrong', false) . '</font><br><br>';
    }
}

if (isset($_SESSION['plan_auth']) && isset($_SESSION['plan_auth_timeout'])) {
    if ($_SESSION['plan_auth_timeout'] >= time() && $_SESSION['plan_auth'] === true) {
        $isAuth = true;
    }
}

if (!$isAuth) {
    $sql = dbquery("SELECT ip_range FROM ip_protection");

    $ip = core::GetIP();

    while ($range = $sql->fetchAssoc()) {
        if (ip_in_range($ip, $range['ip_range'])) {
            $isAuth = true;
            $_SESSION['plan_auth'] = true;
            $_SESSION['plan_auth_timeout'] = time() + 5 * 60;
            break;
        }
    }
}

//None of the above authentication tries was successfull. Ask for login!
if (!$isAuth) {
    getVar('tpl')->Init('Login');
    getVar('tpl')->addCSS(WWW . '/plan/css/login.css');
    getVar('tpl')->setParam('error', $error);
    getVar('tpl')->AddTemplate('plan/login');
    getVar('tpl')->Output();
    exit;
}

function PlanRegisterStandard($tpl) {
    $tpl->addCSS(WWW . '/plan/css/plan.css');
    $tpl->addJS(WWW . "/plan/js/plan.js");
    $tpl->addJS(WWW . "/plan/js/ajax.js");
    $tpl->addJS(WWW . "/plan/js/cache.js");
    $tpl->addJS(WWW . "/plan/js/height.js");
    $tpl->addJS(WWW . "/plan/js/pages.js");
    $tpl->addJS(WWW . "/plan/js/ticker.js");
    $tpl->addJS(WWW . "/plan/js/update.js");
}

getVar("tpl")->registerStandard('plan', 'PlanRegisterStandard');
?>