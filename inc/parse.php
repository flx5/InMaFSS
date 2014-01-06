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
       var $replacements = Array(0=>Array(), 1=>Array());
       var $notes = Array(0=>Array(), 1=>Array());
       var $teachers = Array();
       var $grades = Array();
       var $school = Array();
       var $rooms = Array();
       var $supervisor = Array();

       function parseHTML($html) {
           
                if($html == "")
                    return;
           
               $html = html_entity_decode($html);
               $html = preg_replace("#\r\n#","\n",$html);

               require_once(CWD.DS."inc".DS."parse".DS.config("system").".php");


               $data = parseHTML($html);

               if(isset($data['not_available'])) {   $this->teachers[]                    = $data['not_available'];  }
               if(isset($data['school']))        {   $this->school[]                      = $data['school'];         }
               if(isset($data['rooms']))         {   $this->rooms[]                       = $data['rooms'];          }
               if(isset($data['aufsicht']))      {   $this->supervisor[]                  = $data['aufsicht'];       }
               if(isset($data['replacements']))  {   $this->replacements[$data['type']][] = $data['replacements'];   }
               if(isset($data['notes']))         {   $this->notes[$data['type']][]        = $data['notes'];          }
               if(isset($data['grades']))        {   $this->grades[]                      = $data['grades'];         }
       }


       public function CleanDatabase() {
               $stamp = gmmktime(0,0,0);
               dbquery("DELETE FROM others WHERE timestamp<".$stamp);
               dbquery("DELETE FROM replacements WHERE timestamp<".$stamp);
               dbquery("DELETE FROM teacher_substitude WHERE timestamp<".$stamp);
               dbquery("DELETE FROM ticker WHERE to_stamp<".$stamp);
       }

       public function UpdateDatabase() {

            $this->CleanDatabase();

            foreach($this->replacements[0] as $replacements) {

               $stamps = Array();

               foreach($replacements as $k=>$grade) {
                    foreach($grade as $data) {
                           $pre = "";
                           $k1 = "";
                           $last = "";

                           for($i = 0; $i<strlen($k);$i++) {
                                 if(is_numeric($k[$i]) && $last == "") {
                                    $k1 .= $k[$i];
                                 } else {
                                    if($k1 == "") {
                                      $pre .= $k[$i];
                                    } else {
                                      $last .= $k[$i];
                                    }
                                 }
                           }

                            if(!in_array($data['stamp_for'], $stamps)) {
                              dbquery("DELETE FROM replacements WHERE timestamp = '".$data['stamp_for']."'");
                            }

                            $stamps[] = $data['stamp_for'];

                           dbquery("INSERT INTO replacements (grade_pre, grade, grade_last, lesson, teacher, replacement, room, comment, timestamp, timestamp_update, addition) VALUES ('".$pre."', '".$k1."','".$last."','".$data['lesson']."','".filter($data['teacher'])."','".filter($data['replacement'])."', '".filter($data['room'])."', '".filter($data['hint'])."', '".$data['stamp_for']."','".$data['stamp_update']."', '".$data['addition']."')");
                    }
               }
            }

            foreach($this->replacements[1] as $replacements) {

               $stamps = Array();

               foreach($replacements as $k=>$teacher) {
                    foreach($teacher as $data) {

                            if(!in_array($data['stamp_for'], $stamps)) {
                              dbquery("DELETE FROM teacher_substitude WHERE timestamp = '".$data['stamp_for']."'");
                            }

                            $stamps[] = $data['stamp_for'];

                           dbquery("INSERT INTO teacher_substitude (short, lesson, teacher, grade, room, comment, timestamp, timestamp_update, addition) VALUES ('".$k."','".$data['lesson']."','".filter($data['teacher'])."', '".$data['grade']."' , '".filter($data['room'])."', '".filter($data['hint'])."', '".$data['stamp_for']."','".$data['stamp_update']."', '".$data['addition']."')");
                    }
               }
            }

            foreach($this->notes[0] as $notes) {

                $stamps = Array();

                foreach($notes as $note) {
                       if(!in_array($note['stamp_for'], $stamps)) {
                          dbquery("DELETE FROM ticker WHERE automatic = 1 AND from_stamp = '".$note['stamp_for']."'");
                          $stamps[] = $note['stamp_for'];
                       }
                       dbquery("INSERT INTO ticker (automatic, value, from_stamp, to_stamp, `order`) SELECT 1, '".filter($note['content'])."', '".$note['stamp_for']."', '". gmmktime(23,59,59, date("n",$note['stamp_for']), date("j",$note['stamp_for']), date("Y",$note['stamp_for']))."', COALESCE(MAX(`order`),0)+1 FROM ticker");
                }
            }

            $others = Array('t'=>$this->teachers, 'g'=>$this->grades, 's'=>$this->school, 'r'=>$this->rooms, 'a'=>$this->supervisor, 'n'=>$this->notes[1]);

            $stamps = Array();

            foreach($others as $type=>$val) {
              foreach($val as $datas) {

                foreach($datas as $data) {
                        if(!in_array($data['stamp_for'], $stamps)) {
                           dbquery("DELETE FROM others WHERE timestamp = '".$data['stamp_for']."'");
                           $stamps[] = $data['stamp_for'];
                        }

                        dbquery("INSERT INTO others (type, name, lesson, comment, timestamp, timestamp_update, addition) VALUES ('".$type."', '".filter($data['teacher'])."', '".$data['lesson']."', '".filter($data['reason'])."', '".$data['stamp_for']."','".$data['stamp_update']."', '".$data['addition']."')");
                }
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
                 return gmmktime(0,0,0, substr($data,4,2), substr($data,6,2), substr($data,0,4));
       }
}
?>