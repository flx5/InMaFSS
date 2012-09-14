<?php
  class View {
       var $replacements = Array();
       var $site;
       var $limit;
       var $menu = Array();
       var $pages = Array();
       var $type = 0;  // 0 Pupils ; 1 teachers
       var $tfrom = null;

       public function View($site, $limit) {
            $this->site = $site;
            $this->limit = $limit;
       }

       public function GetLastUpdate() {
             $last_update = 0;
             foreach($this->replacements as $table) {
                    foreach($table as $grade) {
                          foreach($grade as $val) {
                               if($last_update < $val['timestamp_update']) {
                                      $last_update = $val['timestamp_update'];
                               }
                          }
                    }
             }
             return $last_update;
       }

       public function AddRepacements() {
              $p = 0;
              $i = -1;

              foreach($this->GetReplacements() as $k=>$grade) {

                   if(($i+count($grade))>$this->limit || $i == -1) {
                         $p++;
                         $i = 0;
                   }

                   $i = $i + count($grade);
                   $this->replacements[$p][$k] = $grade;

              }
       }


       public function CreateTables() {

               $spalten_t = config("spalten_t");

               foreach($this->replacements  as $ti=>$table) {

                        $t = '';

                        $output = '<table width="95%" align="center" border="0" cellspacing="1" >';

                        if($this->type == 0) {
                          $output .= $this->CreateTableHeader();
                        } else {
                          $output .= $this->CreateTeacherTableHeader();
                        }

                        $info = $this->type;

                        foreach($table as $k=>$grade) {

                            if($t != $k && $info) {
                               $t = $k;
                               switch($t) {
                                  case 't':
                                      $output .= '<tr><th colspan="6">'.lang()->loc('absent.t',false).'</th></tr>';
                                  break;
                                  case 'g':
                                      $output .= '<tr><th colspan="6">'.lang()->loc('absent.g',false).'</th></tr>';
                                  break;
                                  case 'a':
                                      $output .= '<tr><th colspan="6">'.lang()->loc('supervision',false).'</th></tr>';
                                  break;
                                  case 's':
                                      $output .= '<tr><th colspan="6">'.lang()->loc('entire.school',false).'</th></tr>';
                                  break;
                                  case 'r':
                                      $output .= '<tr><th colspan="6">'.lang()->loc('na.rooms',false).'</th></tr>';
                                  break;

                                  case 'n':
                                      $output .= '<tr><th colspan="6">zus&auml;tzliche Informationen</th></tr>';
                                  break;

                                  default:
                                      $output .= '<tr><th colspan="6">'.lang()->loc('subs',false).'</th></tr>';
                                      $output .= '<tr><th width="'.$spalten_t[0].'">'.lang()->loc('teacher.short',false).'</th><th width="'.$spalten_t[1].'">'.lang()->loc('lesson.short',false).'</th><th width="'.$spalten_t[2].'">'.lang()->loc('grade',false).'</th><th width="'.$spalten_t[3].'">'.lang()->loc('room',false).'</th><th width="'.$spalten_t[4].'">'.lang()->loc('comment',false).'</th></tr>';
                                      $info = false;
                                  break;
                               }
                            }
                            $first = true;
                            $prev_t = "";
                            $i = 0;

                            foreach($grade as $val) {

                               $output .= '<tr align="left" class="'.(($val['addition']) ? 'update' : '').'">';

                               switch($t) {
                                  case 't':
                                         if($prev_t != $val['name']) {
                                            $output = preg_replace("#_rowspan_#",$i,$output);

                                            $output .= '<th colspan="1" rowspan="_rowspan_" >&nbsp;'.$val['name'].'</th>';
                                            $prev_t = $val['name'];
                                            $i = 0;
                                         }

                                         $i++;
                                         $output .= '<td colspan="2">&nbsp;'.$val['lesson'].'</td><td colspan="2">&nbsp;'.$val['comment'].'</td></tr>';
                                  break;
                                  case 'g':
                                         $output .= '<th colspan="1">&nbsp;'.$val['name'].'</th><td colspan="4" >&nbsp;'.$val['lesson'].'</td></tr>';
                                  break;
                                  case 'a':
                                         $output .= '<th colspan="1">&nbsp;'.$val['comment'].'</th><td colspan="4">&nbsp;'.$val['name'].'</td></tr>';
                                  break;
                                  case 's':
                                         $output .= '<th colspan="1">&nbsp;'.$val['lesson'].'</th><td colspan="4">&nbsp;'.$val['comment'].'</td></tr>';
                                  break;
                                  case 'r':
                                         $output .= '<th colspan="1">&nbsp;'.$val['name'].'</th><td  colspan="4">&nbsp;'.$val['lesson'].'</td></tr>';
                                  break;

                                  case 'n':
                                      $output .= '<td  colspan="6">&nbsp;'.$val['comment'].'</td></tr>';
                                  break;

                                  default:
                                         if($first) {
                                             $output .= '<th rowspan="'.count($grade).'">&nbsp;'.(($this->type == 0) ? $k : $val['teacher']).'</th>';
                                         }

                                         if($this->type == 1) {
                                            $output .= '<td>&nbsp;'.$val['lesson'].'</td><td>&nbsp;'.$val['grade'].'</td><td>&nbsp;'.$val['room'].'</td><td>&nbsp;'.$val['comment'].'</td>';
                                         } else {
                                            $output .= '<td>&nbsp;'.$val['teacher'].'</td><td>&nbsp;'.$val['lesson'].'</td><td>'.$val['replacement'].'</td><td>&nbsp;'.$val['room'].'</td><td>'.$val['comment'].'</td>';
                                         }
                                  break;
                               }

                               $first = false;
                            }

                            $output = preg_replace("#_rowspan_#",$i,$output);

                            if($info) {
                                unset($table[$k]);
                            }
                        }
                    $output .= '</table>';

                    $keys = array_keys($table);

                    if(count($keys) == 0) {
                      $title = lang()->loc('info',false);
                    } elseif(count($keys) > 1) {
                      $title = $keys[0].'-'.$keys[count($keys)-1];
                    } else {
                      $title = $keys[0];
                    }

                    $this->AddPage($title,$output);
              }
       }

       private function CreateTableHeader() {

             $spalten = config("spalten");

             $output  = '<tr><th colspan="6" >';
             $output .= '<span style="float:left;" >'.lang()->loc(strtolower(substr(date("D",$this->GetTFrom()),0,2)),false).', '.date("d.m.Y",$this->GetTFrom()).'</span>';
             $output .= '<span style="float:right;" >'.preg_replace("/%update%/", date("d.m. - H:i",$this->GetLastUpdate()), lang()->loc('last.update',false)).'</span>';
             $output .= '</th></tr>';
             $output .= '<tr><th width="'.$spalten[0].'">'.lang()->loc('grade',false).'</th><th width="'.$spalten[1].'">'.lang()->loc('teacher.short',false).'</th><th width="'.$spalten[2].'">'.lang()->loc('lesson.short',false).'</th><th width="'.$spalten[3].'">'.lang()->loc('replaced.by',false).'</th><th width="'.$spalten[4].'">'.lang()->loc('room',false).'</th><th width="'.$spalten[5].'">'.lang()->loc('comment',false).'</th></tr>';
             return $output;
       }

       private function CreateTeacherTableHeader() {
             $output  = '<tr><th colspan="6" >';
             $output .= '<span style="float:left;" >'.lang()->loc(strtolower(substr(date("D",$this->GetTFrom()),0,2)),false).', '.date("d.m.Y",$this->GetTFrom()).'</span>';
             $output .= '<span style="float:right;" >'.preg_replace("/%update%/", date("d.m. - H:i",$this->GetLastUpdate()), lang()->loc('last.update',false)).'</span>';
             $output .= '</th></tr>';
             return $output;
       }

       public function CreateMenu() {
                $menu = "";
                foreach($this->pages as $i=>$page) {
                   $menu .= '<span id="info_'.$this->site.'_'.$i.'" style="color:#';
                   if($i == 0) {
                     $menu .= (($this->site == 'left') ? '004488' : '43886F');
                   } else {
                     $menu .= (($this->site == 'left') ? 'C0C0E0' : 'A5CDCD');
                   }

                   $menu .= '">'.$page['title'].'</span> * ';
                }

                $menu = substr($menu,0,-3);
                return $menu;
       }

       private function my_sort($a, $b)
       {

          if ($a['lesson'] == $b['lesson']) {
              return 0;
          }

          if($a['lesson'] == 'M' && $b['lesson'] > 6) {
            return -1;
          }

          if($b['lesson'] == 'M' && $a['lesson'] > 6) {
            return 1;
          }

          if($a['lesson'] == 'M' && $b['lesson'] <= 6) {
            return 1;
          }

          if($b['lesson'] == 'M' && $a['lesson'] <= 6) {
            return -1;
          }
          return ($a['lesson'] < $b['lesson']) ? -1 : 1;
       }

       public function GetReplacements() {
                 $where = "timestamp >= ".$this->GetTFrom()." AND timestamp <= ".mktime(23,59,59, date("n", $this->GetTFrom()), date("j", $this->GetTFrom()));

                 $content = Array();

                 if($this->type == 1) {

                        $sql = dbquery("SElECT * FROM others WHERE ".$where." ORDER BY type, id");

                        while($data = mysql_fetch_assoc($sql)) {
                           $content[$data['type']][] = $data;
                        }

                        $sql = dbquery("SElECT * FROM teacher_substitude WHERE ".$where." ORDER BY id");

                        while($data = mysql_fetch_assoc($sql)) {
                           $content[$data['short']][] = $data;
                        }

                        return $content;
                 }

                 $sql = dbquery("SElECT * FROM replacements WHERE ".$where." AND grade != 0 ORDER BY CAST(grade AS SIGNED) , grade_pre, grade_last, lesson");
                 $sql2 = dbquery("SElECT * FROM replacements WHERE ".$where." AND grade = 0 ORDER BY grade_pre, grade_last, lesson");

                  while($data = mysql_fetch_assoc($sql)) {
                           $content[$data['grade_pre'] . $data['grade'] . $data['grade_last']][] = $data;
                  }
                  while($data = mysql_fetch_assoc($sql2)) {
                           $content[$data['grade_pre'] . $data['grade_last']][] = $data;
                  }

                  foreach($content as $k=>$lessons) {
                     usort($lessons,Array($this,'my_sort'));
                     $content[$k] = $lessons;
                  }

                  return $content;
       }

       public function GetPages() {
                 $where = "timestamp_from <= ".$this->GetTFrom()." AND timestamp_end >= ".mktime(23,59,59, date("n", $this->GetTFrom()), date("j", $this->GetTFrom()));

                 if($this->type == 1) {
                     $where .= " AND teachers = 1";
                 }

                 if($this->type == 0) {
                     $where .= " AND pupils = 1";
                 }

                 $sql = dbquery("SELECT * FROM pages WHERE ".$where." ORDER BY order_num,id ASC");

                 while($page = mysql_fetch_array($sql)) {
                        $this->AddPage($page['title'], $page['content']);
                 }
       }

       public function GenerateContent() {
               $output = "";
               foreach($this->pages as $i=>$page) {
                      $output .= '<div id="plan_'.$this->site.'_'.$i.'" style="'.(($i != 0) ? 'display:none;' : '').'">';
                      $output .= $page['content'];
                      $output .= '</div>';
               }
               return $output;
       }

       public function AddPage($title, $content) {
              $this->pages[] = Array('title'=>$title, 'content'=>$content);
       }

       private function GetTFrom() {
               if($this->tfrom != null) {
                     return $this->tfrom;
               }

               if($this->site == 'left') {
                     $tfrom = mktime(0,0,0);
               } else {
                     $tfrom = mktime(0,0,0, date("n"), date("j")+1);
                     $tfrom = $this->RemoveWeekend($tfrom);
                     getVar("pluginManager")->ExecuteEvent("generate_tfrom_right", $tfrom);
                     $tfrom = $this->RemoveWeekend($tfrom);
               }

               $this->tfrom = $tfrom;
               return $tfrom;
       }

       private function RemoveWeekend($tfrom) {
                     if(date("w",$tfrom) == 6) {
                           $tfrom = $tfrom + 2*24*60*60;
                     }
                     elseif(date("w",$tfrom) == 0)
                     {
                           $tfrom = $tfrom + 1*24*60*60;
                     }

                     return $tfrom;
       }

       public function GetTickers() {
             $sql = dbquery("SELECT * FROM ticker WHERE from_stamp < '".mktime(23,59,59, date("n", $this->GetTFrom()), date("j", $this->GetTFrom()))."' AND to_stamp > '".$this->GetTFrom()."' ORDER BY to_stamp, `order`");
             $tickers = Array();
             while($ticker = mysql_fetch_object($sql)) {
                  $tickers[] = Array('day'=>$ticker->from_stamp, 'content'=>$ticker->value, 'automatic'=>$ticker->automatic);
             }
             return $tickers;
       }
  }
?>