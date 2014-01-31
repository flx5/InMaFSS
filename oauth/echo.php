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

require_once 'global.php';

$authorized = false;

try {
    if ($oauth->GetServer()->verifyIfSigned()) {
        $authorized = true;
    }
} catch (OAuthException2 $e) {
    header('HTTP/1.1 401 Unauthorized');
    header('Content-Type: text/plain');
    echo "OAuth Verification Failed: " . $e->getMessage();
    die;
}

if (!$authorized) {
    header('HTTP/1.1 401 Unauthorized');
    header('Content-Type: text/plain');

    echo "OAuth Verification Failed";
    die;
}

header('Content-type: text/plain');
print_r($_GET);
?>