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

/* =============================================*\
  | FILE SOURCE: http://www.schulferien.org/iCal/ |
  \*============================================= */

class german_holidays
{

    var $handler;
    var $events;

    public function Init($handler) 
    {
        $this->events = Array();

        $this->handler = $handler;
        $this->handler->RegisterEvent($this, "generate_tfrom_next", "check");
        return true;
    }

    public function parse_holidays($year = 0) 
    {
        if ($year == 0) {
            $year = date("Y");
        }

        $data = "";
        $data2 = "";

        if (file_exists(PLUGIN_DIR . "Ferien_Bayern_" . $year . ".ics")) {
            $data = file_get_contents(PLUGIN_DIR . "Ferien_Bayern_" . $year . ".ics"); 
        }

        if (file_exists(PLUGIN_DIR . "Feiertage_Bayern_" . $year . ".ics")) {
            $data2 = file_get_contents(PLUGIN_DIR . "Feiertage_Bayern_" . $year . ".ics"); 
        }

        $events = Array();


        if ($data2 != "") {

            $xml2 = parse::ICS2XML($data2);
            $xml2 = simplexml_load_string($xml2);

            foreach ($xml2->VEVENT as $event) {
                $events[] = Array("start" => parse::ICS2UnixStamp($event->DTSTART), "end" => parse::ICS2UnixStamp($event->DTEND), "value" => (String) $event->SUMMARY);
            }
        }

        if ($data != "") {
            $xml = parse::ICS2XML($data);
            $xml = simplexml_load_string($xml);

            foreach ($xml->VEVENT as $event) {
                $events[] = Array("start" => parse::ICS2UnixStamp($event->DTSTART), "end" => parse::ICS2UnixStamp($event->DTEND), "value" => (String) $event->SUMMARY);
            }
        }
        $this->events[$year] = $events;
    }

    public function check(&$param) 
    {

        $year = date("Y", $param);

        if (!isset($this->events[$year])) {
            $this->parse_holidays($year); 
        }

        $found = false;
        foreach ($this->events[$year] as $event) {
            if ($param >= $event['start'] && $param < $event['end']) {
                if (date("Y", $param) != date("Y", $event['end'])) {
                    $this->parse_holidays(date("Y", $event['end']));
                }
                $param = gmmktime(0, 0, 0, date("n", $event['end']), date("j", $event['end']), date("Y", $event['end']));
                $found = true;
            }
        }
        
        if($found) {
            $this->check($param); 
        }
    }

}

?>