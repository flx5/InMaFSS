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

namespace InMaFSS {

    

/*
// TODO Time management!
    date_default_timezone_set('Europe/Berlin');

    if (is_dir(__DIR__ . "/installer") && file_exists(__DIR__ . "/inc/config.php") && !file_exists(__DIR__ . "/dev.txt")) {
        die("ERROR: YOU HAVE TO REMOVE THE installer folder BEFORE YOU WILL BE ABLE TO USE THIS!");
    }

    if (!file_exists(__DIR__ . "/inc/config.php")) {
        header("Location: ./installer/");
        exit;
    }
*/
    

    $session_factory = new \Aura\Session\SessionFactory;
    $session = $session_factory->newInstance($_COOKIE);
}