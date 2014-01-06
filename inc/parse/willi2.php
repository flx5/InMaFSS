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

function parseHTML($html) {

    if (strpos($html, 'Vertretungen:') !== false) {
        return parseTeacherHTML($html);
    } else {
        return parsePupilHTML($html);
    }
}

function parsePupilHTML($html) {
    $date = substr($html, strpos($html, 'Vertretungsplan ') + strlen('Vertretungsplan '), strpos($html, ')') - strpos($html, 'Vertretungsplan '));
    $date = substr($date, strpos($date, ',')); 
    $date = explode("-", $date); 

    $time = trim(substr($date[1], 0, strpos($date[1], ")")));
    $time = explode(":", $time);

    $date = substr($date[0], strpos($date[0], ",") + 2);
    $date_for = substr($date, 0, strpos($date, "<"));
    $date_update = substr($date, strpos($date, "(") + 1);
    $date_update = trim(substr($date, strpos($date, " ")));

    $date_for = explode(".", $date_for);
    $date_update = explode(".", $date_update); 
    $stamp_for = gmmktime(0, 0, 0, intval($date_for[1]), intval($date_for[0]), intval($date_for[2])); 
    $stamp_update = gmmktime($time[0], $time[1], 0, intval($date_update[1]), intval($date_update[0]));

    $html = substr($html, strpos($html, 'Bemerkung</th>') + strlen('Bemerkung</th>') + 9);

    $notes = Array();
    if (strpos($html, '<table class="F">') !== false) {
        $notes = substr($html, strpos($html, '<table class="F">') + strlen('<table class="F">'));
        $notes = substr($notes, 0, strpos($notes, '</table>'));
        $notes = explode("<th", $notes);
    }

    $final_notes = Array();

    foreach ($notes as $note) {
        $note = substr($note, strpos($note, ">") + 1);
        $note = trim(substr($note, 0, strpos($note, "</th>")));
        $note = htmlentities($note);

        if (trim(preg_replace("#\*#", "", $note)) != "") {
            $final_notes[] = Array('content' => $note, 'stamp_for' => $stamp_for);
        }
    }

    $html = substr($html, 0, strpos($html, '</table>'));

    $graden = Array();
    if (strpos($html, '<tr class="k">') !== false) {
        $graden = explode('<tr class="k">', $html);
    }

    $replacements = Array();


    foreach ($graden as $grade) {

        if ($grade == "") {
            continue;
        }

        $vertretung = explode("<tr>", $grade);
        preg_match('#<th rowspan="(.*)" class="k">\n(.*)</th>#i', $vertretung[0], $data);
        $grade = htmlentities($data[2]);

        foreach ($vertretung as $v) {
            $v = explode("<td>", $v);
            preg_match('#<center>(.*)</center>#i', $v[1], $data);
            $teacher = trim($data[1]);
            preg_match('#<center>(.*)</center>#i', $v[2], $data);
            $lesson = trim($data[1]);
            preg_match('#\n(.*)</td>#i', $v[3], $data);
            $teacher2 = trim($data[1]);
            preg_match('#<center>(.*)</center>#i', $v[4], $data);
            $raum = trim($data[1]);
            preg_match('#\n(.*)</td>#i', $v[5], $data);
            $hint = trim($data[1]);

            $teacher2 = preg_replace("/&nbsp;/", "", $teacher2);
            $hint = preg_replace("/&nbsp;/", "", $hint);


            $addition = 0;
            if (strpos($teacher, '<font color="red">') !== false) {
                $teacher = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $teacher));
                $lesson = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $lesson));
                $teacher2 = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $teacher2));
                $raum = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $raum));
                $hint = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $hint));

                $addition = 1;
            }

            $teacher = htmlentities($teacher);
            $lesson = htmlentities($lesson);
            $teacher2 = htmlentities($teacher2);
            $raum = htmlentities($raum);
            $hint = htmlentities($hint);

            $replacements[$grade][] = Array('stamp_update' => $stamp_update, 'stamp_for' => $stamp_for, 'addition' => $addition, 'teacher' => $teacher, 'lesson' => $lesson, 'replacement' => $teacher2, 'room' => $raum, 'hint' => $hint);
        }
    }

    return Array('type' => 0, "replacements" => $replacements, "notes" => $final_notes);
}

function parseTeacherHTML($html) {

    $html = preg_replace(Array('#\r#', '#\n#'), "", $html);

    $date = substr($html, strpos($html, 'Vertretungsplan ') + strlen('Vertretungsplan '), strpos($html, '</p>') - strpos($html, 'Vertretungsplan '));
    $date = substr($date, strpos($date, ','));
    $date = explode("erstellt:", $date);

    $date[1] = trim($date[1]);
    $time = trim(substr($date[1], strpos($date[1], " ")));
    $time = trim(substr($time, 0, strpos($time, " ")));

    $time = explode(":", $time);

    $date_for = substr($date[0], strpos($date[0], ",") + 1);
    $date_for = substr($date_for, 0, strpos($date_for, "<"));

    $date_update = trim(substr($date[1], 0, strpos($date[1], " ")));

    $date_for = explode(".", $date_for);
    $date_update = explode(".", $date_update);

    $stamp_for = gmmktime(0, 0, 0, intval($date_for[1]), intval($date_for[0]), intval($date_for[2]));
    $stamp_update = gmmktime(intval($time[0]), intval($time[1]), 0, intval($date_update[1]), intval($date_update[0]));

    $replacements = substr($html, strpos($html, 'Vertretungen:') + strlen('Vertretungen:'));
    $replacements = substr($replacements, strpos($replacements, '</tr>') + strlen('</tr>'));
    $replacements = substr($replacements, 0, strpos($replacements, '</table>'));

    $not_available = substr($html, strpos($html, 'Abwesende Lehr'));
    $not_available = substr($not_available, strpos($not_available, '<tr '));
    $not_available = substr($not_available, 0, strpos($not_available, '</table>'));

    $grades = substr($html, strpos($html, 'Abwesende Klassen'));
    $grades = substr($grades, strpos($grades, '<tr'));
    $grades = substr($grades, 0, strpos($grades, '</table>'));

    $rooms = substr($html, strpos($html, 'R�ume'));
    $rooms = substr($rooms, strpos($rooms, '<tr'));
    $rooms = substr($rooms, 0, strpos($rooms, '</table>'));

    $school = substr($html, strpos($html, 'Gesamte Schule'));
    $school = substr($school, strpos($school, '<tr'));
    $school = substr($school, 0, strpos($school, '</table>'));

    $aufsicht = substr($html, strpos($html, 'Aufsichten'));
    $aufsicht = substr($aufsicht, strpos($aufsicht, '<tr'));
    $aufsicht = substr($aufsicht, 0, strpos($aufsicht, '</table>'));


    $notes = Array();
    if (strpos($html, '<table class="F">') !== false) {
        $notes = substr($html, strpos($html, '<table class="F">') + strlen('<table class="F">'));
        $notes = substr($notes, 0, strpos($notes, '</table>'));
        $notes = explode("<th", $notes);
    }

    $final = Array('type' => 1);

    $final['notes'] = Array();

    foreach ($notes as $note) {
        $note = substr($note, strpos($note, ">") + 1);
        $note = trim(substr($note, 0, strpos($note, "</th>")));
        $note = htmlentities($note);

        if (trim(preg_replace("#\*#", "", $note)) != "") {
            $final['notes'][] = Array('teacher' => '', 'lesson' => '', 'reason' => $note, 'addition' => 0, 'stamp_update' => $stamp_update, 'stamp_for' => $stamp_for);
        }
    }


    if (strpos($replacements, '<tr class="l">') !== false) {
        $replacements = explode('<tr class="l">', $replacements);
    } else {
        $replacements = Array();
    }

    $final['replacements'] = Array();

    foreach ($replacements as $repl) {
        $vertretung = explode("<tr>", $repl);
        preg_match('#<th rowspan="(.*)" class="l">(.*)</th>#i', $vertretung[0], $data);

        if (!isset($data[2]) || trim($data[2]) == "") {
            continue;
        }

        $teacher = htmlentities($data[2]);

        foreach ($vertretung as $v) {
            $v = explode("<td>", $v);

            $lesson = substr($v[1], 0, strrpos($v[1], '<'));
            $grade = substr($v[2], 0, strrpos($v[2], '<'));
            $room = substr($v[3], 0, strrpos($v[3], '<'));
            $note = substr($v[4], 0, strrpos($v[4], '<'));
            $note = substr($note, 0, strrpos($note, '<'));


            $addition = 0;
            if (strpos($lesson, '<font color="red">') !== false) {
                $lesson = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $lesson));
                $grade = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $grade));
                $room = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $room));
                $note = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $note));

                $addition = 1;
            }

            $lesson = htmlentities($lesson);
            $grade = htmlentities($grade);
            $room = htmlentities($room);
            $note = htmlentities($note);

            $final['replacements'][substr($teacher, 0, strpos($teacher, '#'))][] = Array('grade' => $grade, 'teacher' => substr($teacher, strpos($teacher, '#') + 1), 'addition' => $addition, 'lesson' => $lesson, 'room' => $room, 'hint' => $note, 'stamp_update' => $stamp_update, 'stamp_for' => $stamp_for);
        }
    }
    $not_available = explode('<tr class="L">', $not_available);

    $final['not_available'] = Array();

    foreach ($not_available as $teacher) {
        preg_match('#<th rowspan="[0-9]*" class="L">(.*)</th>#', $teacher, $name);

        if (!isset($name[1]) || trim($name[1]) == "") {
            continue;
        }

        $name = htmlentities(trim($name[1]));
        
        $teacher = explode("<tr>", $teacher);
        foreach ($teacher as $val) {
            preg_match('#<td>(.*)</td><td>#', $val, $lesson);
            preg_match('#</td><td>(.*)</td>#', $val, $reason);

            $addition = 0;

            if (strpos($lesson[1], '<font color="red">') !== false) {
                $addition = 1;
                $lesson[1] = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $lesson[1]));
                $reason[1] = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $reason[1]));
            }
          
            $lesson = htmlentities(trim($lesson[1]));
            $reason = htmlentities(trim($reason[1]));

            $final['not_available'][] = Array('teacher' => trim($name), 'lesson' => $lesson, 'reason' => $reason, 'stamp_update' => $stamp_update, 'stamp_for' => $stamp_for, 'addition' => $addition);
        }
    }

    $final['grades'] = Array();
    $grades = explode('<tr class="K">', $grades);
    foreach ($grades as $grade) {
        preg_match('#<th rowspan="1" class="K">(.*)</th>#', $grade, $name);
        preg_match('#<td>(.*)</td>#', $grade, $lesson);

        if (!isset($name[1]) || trim($name[1]) == "") {
            continue;
        }

        $addition = 0;

        if (strpos($lesson[1], '<font color="red">') !== false) {
            $addition = 1;
            $lesson[1] = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $lesson[1]));
        }

        $name = htmlentities(trim($name[1]));
        $lesson = htmlentities(trim($lesson[1]));

        $final['grades'][] = Array('reason' => '', 'teacher' => $name, 'lesson' => $lesson, 'stamp_update' => $stamp_update, 'stamp_for' => $stamp_for, 'addition' => $addition);
    }

    $final['rooms'] = Array();
    $rooms = explode('<tr class="R">', $rooms);


    foreach ($rooms as $room) {
        preg_match('#<th rowspan="1" class="R">(.*)</th>#', $room, $name);
        preg_match('#<td>(.*)</td>#', $room, $lesson);

        if (!isset($name[1]) || trim($name[1]) == "") {
            continue;
        }

        $addition = 0;

        if (strpos($lesson[1], '<font color="red">') !== false) {
            $addition = 1;
            $lesson[1] = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $lesson[1]));
        }

        $name = htmlentities(trim($name[1]));
        $lesson = htmlentities(trim($lesson[1]));

        $final['rooms'][] = Array('reason' => '', 'teacher' => $name, 'lesson' => $lesson, 'stamp_update' => $stamp_update, 'stamp_for' => $stamp_for, 'addition' => $addition);
    }

    $final['school'] = Array();
    $school = explode('<tr class="G">', $school);

    foreach ($school as $event) {
        preg_match('#<th rowspan="1" class="G">(.*)</th>#', $event, $lesson);
        preg_match('#<td>(.*)</td>#', $event, $reason);

        if (!isset($lesson[1]) || trim($lesson[1]) == "") {
            continue;
        }

        $addition = 0;

        if (strpos($reason[1], '<font color="red">') !== false) {
            $addition = 1;
            $reason[1] = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $reason[1]));
        }

        $reason = htmlentities(trim($reason[1]));
        $lesson = htmlentities(trim($lesson[1]));

        $final['school'][] = Array('teacher' => '', 'reason' => $reason, 'lesson' => $lesson, 'stamp_update' => $stamp_update, 'stamp_for' => $stamp_for, 'addition' => $addition);
    }

    $final['aufsicht'] = Array();
    $aufsicht = explode('<tr class="a">', $aufsicht);

    foreach ($aufsicht as $item) {
        preg_match('#<th rowspan="1" class="a">(.*)</th>#', $item, $place);
        preg_match('#<td>(.*)</td>#', $item, $teacher);

        if (!isset($place[1]) || trim($place[1]) == "") {
            continue;
        }

        $addition = 0;

        if (strpos($teacher[1], '<font color="red">') !== false) {
            $addition = 1;
            $teacher[1] = trim(preg_replace(Array('#<font color="red">#', '#</font>#'), "", $teacher[1]));
        }

        $place = htmlentities(trim($place[1]));
        $teacher = htmlentities(trim($teacher[1]));

        $final['aufsicht'][] = Array('lesson' => '', 'reason' => $place, 'teacher' => $teacher, 'stamp_update' => $stamp_update, 'stamp_for' => $stamp_for, 'addition' => $addition);
    }

    return $final;
}

?>