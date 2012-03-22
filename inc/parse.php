<?php
/*=================================================================================*\
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
\*=================================================================================*/


class parse {
       var $replacements = Array();
       var $notes = Array();

       function parseHTML($html) {
               $date = substr($html,strpos($html,'Vertretungsplan f&uuml;r')+strlen('Vertretungsplan f&uuml;r'), strpos($html, ')')-strpos($html,'Vertretungsplan f&uuml;r'));
               $date = explode("-", $date);

               $time = trim(substr($date[1],0, strpos($date[1], ")")));
               $time = explode(":", $time);

               $date = substr($date[0], strpos($date[0],",")+2);
               $date_for = substr($date, 0, strpos($date,"</br>"));

               $date_for = explode(".",$date_for);
               $stamp_for = mktime(0,0,0, intval($date_for[1]), intval($date_for[0]), intval($date_for[2]));

               $html = substr($html,strpos($html,'Bemerkung</th>')+strlen('Bemerkung</th>')+9);

               $notes = Array();
               if(strpos($html, '<table class="F">') === true) {
                     $notes = substr($html, strpos($html, '<table class="F">')+strlen('<table class="F">'));
                     $notes = substr($notes,0,strpos($notes,'</table>'));
                     $notes = explode("<th", $notes);
                     unset($notes[0]);
               }

               $final_notes = Array();

               foreach($notes as $note) {
                     $note = substr($note, strpos($note, ">")+1);
                     $note = trim(substr($note, 0, strpos($note, "</th>")));
                     $final_notes[] = Array('content'=>$note, 'stamp_for'=>$stamp_for);
               }

               $html = substr($html,0,strpos($html,'</table>'));
               $graden = explode('<tr class="k">',$html);
               $replacements = Array();

               foreach($graden as $grade) {

                     if($grade == "") {
                        continue;
                     }

                     $vertretung =  explode("<tr>",$grade);
                     preg_match('#<th rowspan="(.*)" class="k">\r\n(.*)</th>#i',$vertretung[0],$data);
                     $grade = $data[2];

                     foreach($vertretung as $v) {
                          $v = explode("<td>",$v);
                          preg_match('#<center>(.*)</center>#i',$v[1],$data); $teacher = trim($data[1]);
                          preg_match('#<center>(.*)</center>#i',$v[2],$data); $lesson = trim($data[1]);
                          preg_match('#\r\n(.*)</td>#i',$v[3],$data); $teacher2 = trim($data[1]);
                          preg_match('#<center>(.*)</center>#i',$v[4],$data); $raum = trim($data[1]);
                          preg_match('#\r\n&nbsp;(.*)</td>#i',$v[5],$data); $hint = trim($data[1]);

                          $teacher2 = preg_replace("/&nbsp;/","",$teacher2);
                          $hint = preg_replace("/&nbsp;/","",$hint);


                          $addition = false;
                          if(strpos($teacher,'<font color="red">') !== false) {
                            $teacher = trim(preg_replace(Array('#<font color="red">#','#</font>#'),"",$teacher));
                            $lesson = trim(preg_replace(Array('#<font color="red">#','#</font>#'),"",$lesson));
                            $teacher2 = trim(preg_replace(Array('#<font color="red">#','#</font>#'),"",$teacher2));
                            $raum = trim(preg_replace(Array('#<font color="red">#','#</font>#'),"",$raum));
                            $hint = trim(preg_replace(Array('#<font color="red">#','#</font>#'),"",$hint));

                            $addition = true;
                          }
                          $replacements[$grade][] = Array('stamp_for'=>$stamp_for, 'addition'=>$addition,'teacher'=>$teacher,'lesson'=>$lesson,'replacement'=>$teacher2,'room'=>$raum,'hint'=>$hint);
                     }
               }

               $this->replacements[] = $replacements;
               $this->notes[] = $final_notes;

               return $replacements;
       }

       public function UpdateDatabase() {
            foreach($this->replacements as $replacements) {
               foreach($replacements as $k=>$grade) {
                    foreach($grade as $data) {
                           $pre = "";
                           $k1 = "";
                           $last = "";

                           for($i = 0; $i<strlen($k);$i++) {
                                 if(is_numeric($k[$i])) {
                                    $k1 .= $k[$i];
                                 } else {
                                    if($k1 == "") {
                                      $pre .= $k[$i];
                                    } else {
                                      $last .= $k[$i];
                                    }
                                 }
                           }

                           $sql = dbquery("SELECT id FROM replacements WHERE grade_pre = '".$pre."' AND grade = '".$k1."' AND grade_last = '".$last."' AND lesson = '".$data['lesson']."' AND timestamp = '".$data['stamp_for']."'");

                           if(mysql_num_rows($sql) == 1) {
                                  dbquery("UPDATE replacements SET teacher = '".$data['teacher']."', replacement = '".$data['replacement']."', room = '".$data['room']."',  hint = '".$data['hint']."', timestamp_update = '".time()."' WHERE id = ".mysql_result($sql,0));
                                  continue;
                           }

                           dbquery("INSERT INTO replacements (grade_pre, grade, grade_last, lesson, teacher, replacement, room, hint, timestamp, timestamp_update, addition) VALUES ('".$pre."', '".$k1."','".$last."','".$data['lesson']."','".$data['teacher']."','".$data['replacement']."', '".$data['room']."', '".$data['hint']."', '".$data['stamp_for']."','".time()."', '".$data['addition']."')");
                    }
               }
            }

            foreach($this->notes as $notes) {
                foreach($notes as $note) {
                       $sql = dbquery("SELECT null FROM notes WHERE content = '".$note['content']."' AND timestamp = '".$note['stamp_for']."'");
                       if(mysql_num_rows($sql) == 0) {
                               dbquery("INSERT INTO notes (content, timestamp) VALUES ('".$note['content']."', '".$note['stamp_for']."')");
                       }
                }
            }
       }
}
?>