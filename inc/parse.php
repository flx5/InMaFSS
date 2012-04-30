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
               require_once(CWD.DS."inc".DS."parse".DS.config("system").".php");
               $data = parseHTML($html);

               $this->replacements[] = $data['replacements'];
               $this->notes[] = $data['notes'];
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

                           $sql = dbquery("SELECT id FROM replacements WHERE grade_pre = '".$pre."' AND grade = '".$k1."' AND grade_last = '".$last."' AND lesson = '".$data['lesson']."' AND timestamp = '".$data['stamp_for']."' AND teacher = '".$data['teacher']."'");

                           if(mysql_num_rows($sql) == 1) {
                                  dbquery("UPDATE replacements SET teacher = '".$data['teacher']."', replacement = '".$data['replacement']."', room = '".$data['room']."',  hint = '".$data['hint']."', timestamp_update = '".$data['stamp_update']."', addition = '".$data['addition']."' WHERE id = ".mysql_result($sql,0));
                                  continue;
                           }

                           dbquery("INSERT INTO replacements (grade_pre, grade, grade_last, lesson, teacher, replacement, room, hint, timestamp, timestamp_update, addition) VALUES ('".$pre."', '".$k1."','".$last."','".$data['lesson']."','".$data['teacher']."','".$data['replacement']."', '".$data['room']."', '".$data['hint']."', '".$data['stamp_for']."','".$data['stamp_update']."', '".$data['addition']."')");
                    }
               }
            }

            foreach($this->notes as $notes) {
                foreach($notes as $note) {
                        dbquery("DELETE FROM ticker WHERE automatic = 1 AND from_stamp = '".$note['stamp_for']."'");  
                }

                foreach($notes as $note) {
                       dbquery("INSERT INTO ticker (automatic, value, from_stamp, to_stamp) VALUES (1, '".$note['content']."', '".$note['stamp_for']."', '". mktime(23,59,59, date("n",$note['stamp_for']), date("j",$note['stamp_for']), date("Y",$note['stamp_for']))."')");
                }
            }
       }


        public static function ICS2XML($data) {
              $data = htmlspecialchars($data);
              $data = preg_replace("/\r\n /","",$data);

              $data = preg_replace("/BEGIN:(.*)/","<$1>",$data);
              $data = preg_replace("/END:(.*)/","</$1>",$data);

              $data = preg_replace("/\n([A-z]*);VALUE=(.*)/","\n<$1>$2</$1>",$data);
              $data = preg_replace(Array("/\n([A-z]*):(.*)/"),"\n<$1>$2</$1>",$data);

              return $data;
       }

       public static function ICS2UnixStamp($data) {
                 $data = substr($data,5);
                 return mktime(0,0,0, substr($data,4,2), substr($data,6,2), substr($data,0,4));
       }
}
?>