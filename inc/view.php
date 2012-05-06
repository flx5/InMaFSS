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
              foreach($this->replacements  as $ti=>$table) {
                        $output = '<table width="95%" align="center" border="0" cellspacing="1" >';
                        $output .= $this->CreateTableHeader();

                        foreach($table as $k=>$grade) {
                            $first = true;

                            foreach($grade as $val) {
                               $output .= '<tr class="'.(($val['addition']) ? 'update' : '').'">';
                               if($first) {
                                  $output .= '<th rowspan="'.count($grade).'">'.(($this->type == 0) ? $k : $val['teacher']).'</th>';
                               }

                               $output .= '<td>'.$val['teacher'].'</td><td>'.$val['lesson'].'</td><td align="left">&nbsp;'.$val['replacement'].'</td><td>'.$val['room'].'</td><td align="left">&nbsp;'.$val['hint'].'</td></tr>';
                               $first = false;
                            }
                        }
                    $output .= '</table>';

                    $keys = array_keys($table);
                    if(count($keys) != 1) {
                      $title = $keys[0].'-'.$keys[count($keys)-1];
                    } else {
                      $title = $keys[0];
                    }
                    $this->AddPage($title,$output);
              }
       }

       private function CreateTableHeader() {
             $output  = '<tr><th colspan="6" >';
             $output .= '<span style="float:left;" >'.lang()->loc(strtolower(substr(date("D",$this->GetTFrom()),0,2)),false).', '.date("d.m.Y",$this->GetTFrom()).'</span>';
             $output .= '<span style="float:right;" >'.preg_replace("/%update%/", date("d.m. - H:i",$this->GetLastUpdate()), lang()->loc('last.update',false)).'</span>';
             $output .= '</th></tr>';
             $output .= '<tr><th width="75px">'.lang()->loc('grade',false).'</th><th width="75px">'.lang()->loc('teacher.short',false).'</th><th width="60px">'.lang()->loc('lesson.short',false).'</th><th width="180px">'.lang()->loc('replaced.by',false).'</th><th width="75px">'.lang()->loc('room',false).'</th><th width="*">'.lang()->loc('comment',false).'</th></tr>';
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

       public function GetReplacements() {
                 $where = "timestamp >= ".$this->GetTFrom()." AND timestamp <= ".mktime(23,59,59, date("n", $this->GetTFrom()), date("j", $this->GetTFrom()));

                 $content = Array();

                 if($this->type == 1) {
                        $sql = dbquery("SElECT * FROM replacements WHERE ".$where." ORDER BY teacher");

                        while($data = mysql_fetch_assoc($sql)) {
                           $content[$data['grade_pre'] . $data['grade'] . $data['grade_last']][] = $data;
                        }
                        return $content;
                 }

                 $sql = dbquery("SElECT * FROM replacements WHERE ".$where." AND grade != 0 ORDER BY grade, grade_pre, grade_last, lesson");
                 $sql2 = dbquery("SElECT * FROM replacements WHERE ".$where." AND grade = 0 ORDER BY grade, grade_pre, grade_last, lesson");

                  while($data = mysql_fetch_assoc($sql)) {
                           $content[$data['grade_pre'] . $data['grade'] . $data['grade_last']][] = $data;
                  }
                  while($data = mysql_fetch_assoc($sql2)) {
                           $content[$data['grade_pre'] . $data['grade_last']][] = $data;
                  }
                  return $content;
       }

       public function GetPages() {
                 $where = "timestamp_from <= ".$this->GetTFrom()." AND timestamp_end >= ".mktime(23,59,59, date("n", $this->GetTFrom()), date("j", $this->GetTFrom()));
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
             $sql = dbquery("SELECT * FROM ticker WHERE from_stamp < '".mktime(23,59,59, date("n", $this->GetTFrom()), date("j", $this->GetTFrom()))."' AND to_stamp > '".$this->GetTFrom()."' ORDER BY to_stamp");
             $tickers = Array();
             while($ticker = mysql_fetch_object($sql)) {
                  $tickers[] = Array('day'=>$ticker->from_stamp, 'content'=>$ticker->value, 'automatic'=>$ticker->automatic);
             }
             return $tickers;
       }
  }
?>