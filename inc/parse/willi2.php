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

       function parseHTML($html) {
               $date = substr($html,strpos($html,'Vertretungsplan ')+strlen('Vertretungsplan '), strpos($html, ')')-strpos($html,'Vertretungsplan '));
               $date = substr($date, strpos($date,','));       
               $date = explode("-", $date);

               $time = trim(substr($date[1],0, strpos($date[1], ")")));
               $time = explode(":", $time);

               $date = substr($date[0], strpos($date[0],",")+2);
               $date_for = substr($date, 0, strpos($date,"<"));
               $date_update = substr($date, strpos($date,"(")+1);
               $date_update = trim(substr($date, strpos($date," ")));

               $date_for = explode(".",$date_for);
               $date_update = explode(".",$date_update);
               $stamp_for = mktime(0,0,0, intval($date_for[1]), intval($date_for[0]), intval($date_for[2]));
               $stamp_update = mktime($time[0],$time[1],0, intval($date_update[1]), intval($date_update[0]));

               $html = substr($html,strpos($html,'Bemerkung</th>')+strlen('Bemerkung</th>')+9);

               $notes = Array();
               if(strpos($html, '<table class="F">') !== false) {
                     $notes = substr($html, strpos($html, '<table class="F">')+strlen('<table class="F">'));
                     $notes = substr($notes,0,strpos($notes,'</table>'));
                     $notes = explode("<th", $notes);
               }

               $final_notes = Array();

               foreach($notes as $note) {
                     $note = substr($note, strpos($note, ">")+1);
                     $note = trim(substr($note, 0, strpos($note, "</th>")));
                     if(trim(preg_replace("#\*#","",$note)) != "") {
                       $final_notes[] = Array('content'=>$note, 'stamp_for'=>$stamp_for);
                     }
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
                          $replacements[$grade][] = Array('stamp_update'=>$stamp_update, 'stamp_for'=>$stamp_for, 'addition'=>$addition,'teacher'=>$teacher,'lesson'=>$lesson,'replacement'=>$teacher2,'room'=>$raum,'hint'=>$hint);
                     }
               }

               return Array("replacements"=>$replacements,"notes"=>$final_notes);
       }
?>