<?php
class TimeHelper
{
    private static $cache = Array();
    
    private static function RemoveWeekend($tfrom) 
    {
        if (date("w", $tfrom) == 6) {
            $tfrom = $tfrom + 2 * 24 * 60 * 60;
        } elseif (date("w", $tfrom) == 0) {
            $tfrom = $tfrom + 1 * 24 * 60 * 60;
        }

        return $tfrom;
    }

    public static function GetNextDay($tfrom) 
    {  
        do {
            $remWeekend = self::RemoveWeekend($tfrom);
            $tfrom = $remWeekend;
            getVar("pluginManager")->ExecuteEvent("generate_tfrom_next", $tfrom);
        } while ($tfrom != $remWeekend);

        return $tfrom;
    }

    public static function GetTFrom($day) 
    {
        if (isset(self::$cache[$day])) {
            return self::$cache[$day];
        }

        if ($day == 'today') {
            $tfrom = gmmktime(0, 0, 0, date("n"), date("j"), date("Y"));
        } elseif($day == 'tomorrow') {
            $tfrom = gmmktime(0, 0, 0, date("n"), date("j"), date("Y"))+24*3600;
            $tfrom = self::GetNextDay($tfrom);
        } elseif(is_numeric($day))
            $tfrom = $day;
        else {
            throw new Exception("UNKNOWN DAY PARAMETER"); 
        }

        self::$cache[$day] = $tfrom;
        return $tfrom;
    }
}
?>
