<?php

class PARSE_MENSA_GYMDON implements Parser {

    public function parse($content) {
        $zip = zip_open($content);

        if(!is_resource($zip))
            return false;
        
        $ret = null;

        while ($zipEntry = zip_read($zip)) {
            if (zip_entry_name($zipEntry) == "word/document.xml") {
                zip_entry_open($zip, $zipEntry);

                $data = "";

                while ($bit = zip_entry_read($zipEntry)) {
                    $data .= $bit;
                }

                $ret = parse::ParseXMLString($data);
                zip_entry_close($zipEntry);
            }
        }

        zip_close($zip);

        if ($ret == null) {
            return false;
        } 

        $startDate = strtotime($ret[5]);
        //$endDate = strtotime($ret[7]);

        $menu = Array();

        for ($m = 1; $m <= 2; $m++) {

            $startFrom = 20;

            if ($m == 2)
                $startFrom = 26;

            if (!isset($menu[$m]))
                $menu[$m] = Array();

            for ($i = 1; $i <= 5; $i++) {
                if (isset($ret[$startFrom + $i]))
                    $menu[$m][$i] = $ret[$startFrom + $i];
                else
                    $menu[$m][$i] = "";
            }
        }

        $desert = $ret[32];
        $salad = $ret[33];

        // Replace “ and „ (those are multibyte characters, thus we have to use multiple ascii chars to replace)
        $salad = str_replace(Array(chr(226) . chr(128) . chr(158), chr(226) . chr(128) . chr(156)), "", $salad);

        $additives = $ret[34];

        $additives = explode(chr(32) . chr(226) . chr(151) . chr(143) . chr(32), $additives);

        $final_additives = Array();

        foreach ($additives as $additive) {
            $additive = explode(" = ", $additive);
            $final_additives[$additive[0]] = trim($additive[1]);
        }

        $final_additives = serialize($final_additives);

        $data = Array();

        for ($i = 1; $i <= 5; $i++) {
            $date = $startDate + (($i - 1) * 86400);

            $menu1 = "";
            $menu2 = "";

            if (isset($menu[1][$i]))
                $menu1 = $menu[1][$i];

            if (isset($menu[2][$i]))
                $menu2 = $menu[2][$i];

            $data[] = Array(
                'date' => $date,
                'menu1' => $menu1,
                'menu2' => $menu2,
                'desert' => $desert,
                'salad' => $salad,
                'additives' => $final_additives
            );
        }

        return $data;
    }

}

?>