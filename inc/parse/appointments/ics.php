<?php

require_once INC . "libs/iCalcreator.class.php";

class PARSE_APPOINTMENTS_ICS implements Parser
{

    public function parse($file) 
    {
        $ical = new vcalendar();
        if (!$ical->parse(file_get_contents($file))) {
            return false; 
        }

        $timeZone = getTimezonesAsDateArrays($ical);

        $events = Array();

        while (($event = $ical->getComponent("vevent")) !== false) {
            $tstart = iCalUtilityFunctions::_date2timestamp($event->dtstart['value']);
            $tend = iCalUtilityFunctions::_date2timestamp($event->dtend['value']);

            //var_dump(iCalUtilityFunctions::_date2timestamp($event->dtstart['value']));
            $offset = getTzOffsetForDate($timeZone, 'Westeuropäische Normalzeit', $tstart);
            $tstart += $offset['offsetSec'];

            $categories = Array();

            while ($category = $event->getProperty("CATEGORIES")) {
                if (is_array($category)) {
                    array_merge($categories, $category); 
                }
                else {
                    $categories[] = $category; 
                }
            }
            var_dump($categories);
            $events[] = Array(
                "categories" => $event->categories,
                "start" => $tstart,
                "end" => $tend,
                "title" => $event->summary['value'],
                "desc" => trim($event->getProperty("description"))
            );
        }
        return $events;
    }

}

?>
