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

require_once(INC.'class.parse_plan.php');
require_once(INC.'class.parse_mensa.php');
require_once(INC.'class.parse_appointments.php');

class parse {
    public static function ICS2XML($data) {
        $data = htmlspecialchars($data);
        $data = preg_replace("/\r\n /", "", $data);

        $data = preg_replace("/BEGIN:(.*)/", "<$1>", $data);
        $data = preg_replace("/END:(.*)/", "</$1>", $data);

        $data = preg_replace("/\n([A-z]*);VALUE=(.*)/", "\n<$1>$2</$1>", $data);
        $data = preg_replace(Array("/\n([A-z]*):(.*)/"), "\n<$1>$2</$1>", $data);

        return $data;
    }

    public static function ICS2UnixStamp($data) { 
        $data = substr($data, 5);
        return gmmktime(0, 0, 0, substr($data, 4, 2), substr($data, 6, 2), substr($data, 0, 4));
    }
    
    private static function ParseXML($xml) {
        $mCount = 0;
        $secondCount = 0;

        $canIncrease = false;

        $output = Array();

        while ($xml->read()) {

            if ($mCount < 5 && $canIncrease) {
                $secondCount++;
                $canIncrease = false;
            }

            if ($mCount > 5) {
                $canIncrease = true;
            }

            switch ($xml->nodeType) {
                case XMLReader::TEXT:
                    $output[$secondCount][] = $xml->value;
                    break;
                case XMLReader::ELEMENT:
                    if (!$xml->isEmptyElement)
                        $mCount++;
                    break;
                case XMLReader::END_ELEMENT:
                    $mCount--;
                    break;
            }
        }

        foreach ($output as $k => $p) {
            $output[$k] = implode("", $p);
        }

        return $output;
    }

    public static function ParseXMLString($data) {
        $xml = new XMLReader();
        $xml->XML($data);

        return self::ParseXML($xml);
    }
}

interface ParseInterface {
    public function parse($file);
    public function CleanDatabase();
    public function UpdateDatabase();
}

interface Parser {
    public function parse($file);
}
?>