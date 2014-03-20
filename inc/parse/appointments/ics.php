<?php

require_once(INC . "libs/ics-parser/class.iCalReader.php");

class PARSE_APPOINTMENTS_ICS implements Parser {

    public function parse($file) {
        $ical = new ICal($file);
        
        if($ical === false)
            return false;
        
        $events = Array();

        foreach ($ical->events() as $event) {
            $events[] = Array(
                "start" => $ical->iCalDateToUnixTimestamp($event["DTSTART"]),
                "end" => $ical->iCalDateToUnixTimestamp($event["DTEND"]),
                "title" => $event["SUMMARY"],
                "desc" => utf8_decode($event["DESCRIPTION"])
            );
        }
        
        return $events;
    }

}

?>
