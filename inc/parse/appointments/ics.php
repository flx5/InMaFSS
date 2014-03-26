<?php

require_once(INC . "libs/ics-parser/class.iCalReader.php");

class PARSE_APPOINTMENTS_ICS implements Parser {

    public function parse($file) {
        $ical = new ICal($file);

        if ($ical === false)
            return false;

        $timeZone = date_default_timezone_get();

        if (isset($ical->cal["VTIMEZONE"]["TZID"]) && in_array($ical->cal["VTIMEZONE"]["TZID"], timezone_identifiers_list()))
            $timeZone = $ical->cal["VTIMEZONE"]["TZID"];

        $date = new DateTime(null, new DateTimeZone($timeZone));

        $events = Array();

        foreach ($ical->events() as $event) {
            $start = $ical->iCalDateToUnixTimestamp($event["DTSTART"]);
            $end = $ical->iCalDateToUnixTimestamp($event["DTEND"]);

            $date->setTimestamp($start);
            $start = strtotime($date->format('Y-m-d') . " GMT");

            $date->setTimestamp($end);
            $end = strtotime($date->format('Y-m-d') . " GMT");

            $events[] = Array(
                "start" => $start,
                "end" => $end,
                "title" => $event["SUMMARY"],
                "desc" => $event["DESCRIPTION"]
            );
        }

        return $events;
    }

}

?>
