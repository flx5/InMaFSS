<?php

/*
 * Copyright (C) 2016 Felix Prasse <me@flx5.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace InMaFSS {
    /**
     * Description of Compability
     *
     * @author Felix Prasse <me@flx5.com>
     */
    class Compability {

        /**
         * Call this to undo the effect of magic quotes.
         * Prefer to change the system settings, if the possibility exists.
         * @link http://php.net/manual/de/security.magicquotes.disabling.php PHP Documentation
         */
        public static function magicQuotes() {
            // TODO Add warning!
            if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
                $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
                while (list($key, $val) = each($process)) {
                    foreach ($val as $k => $v) {
                        unset($process[$key][$k]);
                        if (is_array($v)) {
                            $process[$key][stripslashes($k)] = $v;
                            $process[] = &$process[$key][stripslashes($k)];
                        } else {
                            $process[$key][stripslashes($k)] = stripslashes($v);
                        }
                    }
                }
                unset($process);
            }
        }
    }
}