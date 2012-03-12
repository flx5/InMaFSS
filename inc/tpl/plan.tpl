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


   lang()->add('date');
   $size = $_GET['size'];
   $limit = floor(($size-55)/26)-1;

   if($site == 'left') {
      $tfrom = mktime(0,0,0);
      $where = "timestamp >= ".$tfrom." AND timestamp <= ".mktime(23,59,59, date("n"), date("j"));
   } else {
      $tfrom = mktime(0,0,0, date("n"), date("j")+1);
      $where = "timestamp >= ".$tfrom." AND timestamp <= ".mktime(23,59,59, date("n"), date("j")+1);
   }

   $sql = dbquery("SElECT * FROM replacements WHERE ".$where." AND grade != 0 ORDER BY grade, grade_pre, grade_last");
   $sql2 = dbquery("SElECT * FROM replacements WHERE ".$where." AND grade = 0 ORDER BY grade, grade_pre, grade_last");
   $sql3 = dbquery("SElECT * FROM notes WHERE ".$where." ORDER BY id");

    $content = Array();
    while($data = mysql_fetch_assoc($sql)) {
             $content[$data['grade_pre'] . $data['grade'] . $data['grade_last']][] = $data;
    }

    while($data = mysql_fetch_assoc($sql2)) {
             $content[$data['grade_pre'] . $data['grade_last']][] = $data;
    }

   $i = 0;
   $ti = 0;

    $output = "";
    $grades = "";
    $prek = "";
    $last_update = 0;

    foreach($content as $k=>$grade) {

       if(($i+count($grade))>$limit || $i == 0) {
              if($i != 0) {
                $output .= '</table>';
                $grades .= $prek.'</span> * ';
              }
              $output .= '<table width="95%" align="center" border="0" cellspacing="1" id="plan_'.$site.'_'.$ti.'" style="'.(($i != 0) ? 'display:none;' : '').'"><th>Klasse</th><th>Lehrer</th><th>Stunde</th><th>vertreten durch</th><th>Raum</th><th>Bemerkung</th>';
              if($i == 0) {
                $grades .= '<span id="info_'.$site.'_'.$ti.'" style="color:#004488">'. $k . '-';
              } else {
                $grades .= '<span id="info_'.$site.'_'.$ti.'" style="color:#C0C0C0">'. $k . '-';
              }
              $ti++;
              $i = 1;
       }


       $last_grade_pre = "";
       $last_grade = "";
       $last_grade_last = "";

       foreach($grade as $val) {
          $i++;


          if(($val['timestamp_update'] > $tfrom && config('auto_addition')) || $val['addition']) {
               $output .= '<tr style="color:#ff0000;">';
          } else {
               $output .= '<tr >';
          }

          if($last_grade_last != $val['grade_last'] || $last_grade != $val['grade'] || $last_grade_pre != $val['grade_pre']) {
                 $last_grade_pre = $val['grade_pre'];
                 $last_grade = $val['grade'];
                 $last_grade_last = $val['grade_last'];
                 $output .= '<th rowspan="'.count($grade).'">'.$k.'</th>';
          }
          $output .= '<td>'.$val['teacher'].'</td><td>'.$val['lesson'].'</td><td>'.$val['replacement'].'</td><td>'.$val['room'].'</td><td>'.$val['hint'].'</td></tr>';
          if($last_update < $val['timestamp_update']) {
                $last_update = $val['timestamp_update'];
          }
       }
       $prek = $k;
    }

$grades .= $prek;
$pi = 1;

$notes_count = mysql_num_rows($sql3);

if($notes_count != 0 && ($i+$notes_count+1 > $limit || $ti == 0)) {
     if($output != "") { $output .= '</table>';  }

     if($grades != "") {
        $grades .= ' * ';
     }
     $output .= '<table width="95%" align="center" border="0" cellspacing="1" id="plan_'.$site.'_'.$ti.'" style="'.(($i != 0) ? 'display:none;' : '').'">';
     $grades .= '<span id="info_'.$site.'_'.$ti.'" style="color:#'.(($grades != "") ? 'C0C0C0' : '004488').';">'.lang()->loc('page',false).' '.$pi.'</span>';
     $pi++;
     $ti++;
} else {
     $output .= '<tr><td colspan="6" style="background-color:transparent;"></td></tr>';
}

while($note = mysql_fetch_assoc($sql3)) {
     $output .= '<tr><th colspan="6" >'.$note['content'].'</th></tr>';
}

if($notes_count != 0 && $output != "" && ($i+$notes_count+1 < $limit || $ti == 1)) { $output .= '</table>';  }




if($site == 'left') {
      $where = "timestamp_end >= ".mktime(0,0,0)." AND timestamp_from <= ".mktime(0,0,0, date("n"), date("j")+1);
   } else {
      $where = "timestamp_end >= ".mktime(0,0,0, date("n"), date("j")+1)." AND timestamp_from <= ".mktime(0,0,0, date("n"), date("j")+2);
   }

$sql = dbquery("SELECT * FROM pages WHERE ".$where." ORDER BY order_num");



while($page = mysql_fetch_assoc($sql)) {
    $output .=  '<div id="plan_'.$site.'_'.$ti.'" style="'.(($grades != "") ? 'display:none;' : '').'">'.$page['content'].'</div>';
    if($grades != "") {
        $grades .= ' * ';
    }
    $grades .= '<span id="info_'.$site.'_'.$ti.'" style="color:#'.(($grades != "") ? 'C0C0C0' : '004488').';">'.lang()->loc('page',false).' '.$pi.'</span>';
    $pi++;
    $ti++;
}

if($pi == 1 && $ti == 0) {
    echo '<div style="height:80px; width:90%; background-color:#C0C0C0; padding:5px; margin:auto; margin-top:40%;"><h1>'.lang()->loc('empty', false).'</h1></div>';
}

if($site == 'left') {
    $date = time();
}  else {
    $date = mktime(0,0,0, date("n"), date("j")+1);
}

$plan_for = lang()->loc('plan.for',false);
$plan_for = preg_replace("/%day%/", lang()->loc(strtolower(substr(date("D",$date),0,2)),false), $plan_for);
$plan_for = preg_replace("/%date%/", date("d.m.Y",$date), $plan_for);
$plan_for = preg_replace("/%time%/", date("d.m. - H:i",$last_update), $plan_for);

if($output != "") {
echo '<div style="height:55px; font-size:20px;">';
if(count($content) != 0) { echo $plan_for.'<br>'; }

echo $grades;
echo '</div>';
}

echo $output;
?>
