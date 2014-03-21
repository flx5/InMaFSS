<?php
require_once INC.'class.replacements.php';
class View {

    private $replacements = Array();
    private $site;
    private $limit;
    private $pages = Array();
    private $type = 0;  // 0 Pupils ; 1 teachers
    
    private $ReplacementHelper;

    public function View($site, $limit, $type = ReplacementsTypes::PUPIL) {
        $this->site = $site;
        $this->limit = $limit;
        $this->type = $type;
        
        $day = ($site == "left" ? 'today' : 'tomorrow');
        
        $this->ReplacementHelper = new Replacements($type, $day);
        $this->AddRepacements();
        $this->CreateTables();
        $this->GetPages();
    }

    private function AddRepacements() {
        $p = 0;
        $i = -1;

        $replacements = $this->ReplacementHelper->GetReplacements();
        if($this->type == ReplacementsTypes::TEACHER)
        { 
            // Adding two arrays is working thanks to overloaded operators (http://www.techfounder.net/2008/07/08/operator-overloading-in-php/)
            $replacements = $this->ReplacementHelper->GetOthers() + $replacements;
        }
      
        foreach ($replacements as $k => $grade) {
            if (($i + count($grade)) > $this->limit || $i == -1) {
                $p++;
                $i = 0;
            }

            $i = $i + count($grade);
            $this->replacements[$p][$k] = $grade;
        }
    }

    private function CreateTables() {

        lang()->add('home');

        $spalten_t = config("spalten_t");

        foreach ($this->replacements as $ti => $table) {

            $t = '';

            $output = '<table width="95%" align="center" border="0" cellspacing="1" >';

            if ($this->type == 0) {
                $output .= $this->CreateTableHeader();
            } else {
                $output .= $this->CreateTeacherTableHeader();
            }

            $info = $this->type;

            foreach ($table as $k => $grade) {

                if ($t != $k && $info) {
                    $t = $k;
                    switch ($t) {
                        case 't':
                            $output .= '<tr><th colspan="6">' . lang()->loc('absent.t', false) . '</th></tr>';
                            break;
                        case 'g':
                            $output .= '<tr><th colspan="6">' . lang()->loc('absent.g', false) . '</th></tr>';
                            break;
                        case 'a':
                            $output .= '<tr><th colspan="6">' . lang()->loc('supervision', false) . '</th></tr>';
                            break;
                        case 's':
                            $output .= '<tr><th colspan="6">' . lang()->loc('entire.school', false) . '</th></tr>';
                            break;
                        case 'r':
                            $output .= '<tr><th colspan="6">' . lang()->loc('na.rooms', false) . '</th></tr>';
                            break;

                        case 'n':
                            $output .= '<tr><th colspan="6">zus&auml;tzliche Informationen</th></tr>';
                            break;

                        default:
                            $output .= '<tr><th colspan="6">' . lang()->loc('subs', false) . '</th></tr>';
                            $output .= '<tr><th width="' . $spalten_t[0] . '">' . lang()->loc('teacher.short', false) . '</th><th width="' . $spalten_t[1] . '">' . lang()->loc('lesson.short', false) . '</th><th width="' . $spalten_t[2] . '">' . lang()->loc('grade', false) . '</th><th width="' . $spalten_t[3] . '">' . lang()->loc('room', false) . '</th><th width="' . $spalten_t[4] . '">' . lang()->loc('comment', false) . '</th></tr>';
                            $info = false;
                            break;
                    }
                }
                $first = true;
                $prev_t = "";
                $i = 0;

                foreach ($grade as $val) {

                    $output .= '<tr align="left" class="' . (($val['addition']) ? 'update' : '') . '">';

                    switch ($t) {
                        case 't':
                            if ($prev_t != $val['name']) {
                                $output = preg_replace("#_rowspan_#", $i, $output);

                                $output .= '<th colspan="1" rowspan="_rowspan_" >&nbsp;' . $val['name'] . '</th>';
                                $prev_t = $val['name'];
                                $i = 0;
                            }

                            $i++;
                            $output .= '<td colspan="2">&nbsp;' . $val['lesson'] . '</td><td colspan="2">&nbsp;' . $val['comment'] . '</td></tr>';
                            break;
                        case 'g':
                            $output .= '<th colspan="1">&nbsp;' . $val['name'] . '</th><td colspan="4" >&nbsp;' . $val['lesson'] . '</td></tr>';
                            break;
                        case 'a':
                            $output .= '<th colspan="1">&nbsp;' . $val['comment'] . '</th><td colspan="4">&nbsp;' . $val['name'] . '</td></tr>';
                            break;
                        case 's':
                            $output .= '<th colspan="1">&nbsp;' . $val['lesson'] . '</th><td colspan="4">&nbsp;' . $val['comment'] . '</td></tr>';
                            break;
                        case 'r':
                            $output .= '<th colspan="1">&nbsp;' . $val['name'] . '</th><td  colspan="4">&nbsp;' . $val['lesson'] . '</td></tr>';
                            break;

                        case 'n':
                            $output .= '<td  colspan="6">&nbsp;' . $val['comment'] . '</td></tr>';
                            break;

                        default:
                            if ($first) {
                                $output .= '<th rowspan="' . count($grade) . '">&nbsp;' . (($this->type == 0) ? $k : $val['teacher']) . '</th>';
                            }

                            if ($this->type == 1) {
                                $output .= '<td>&nbsp;' . $val['lesson'] . '</td><td>&nbsp;' . $val['grade'] . '</td><td>&nbsp;' . $val['room'] . '</td><td>&nbsp;' . $val['comment'] . '</td>';
                            } else {
                                $output .= '<td>&nbsp;' . $val['teacher'] . '</td><td>&nbsp;' . $val['lesson'] . '</td><td>' . $val['replacement'] . '</td><td>&nbsp;' . $val['room'] . '</td><td>' . $val['comment'] . '</td>';
                            }
                            break;
                    }

                    $first = false;
                }

                $output = preg_replace("#_rowspan_#", $i, $output);

                if ($info) {
                    unset($table[$k]);
                }
            }
            $output .= '</table>';

            $keys = array_keys($table);

            if (count($keys) == 0) {
                $title = lang()->loc('info', false);
            } elseif (count($keys) > 1) {
                $title = $keys[0] . '-' . $keys[count($keys) - 1];
            } else {
                $title = $keys[0];
            }

            $this->AddPage($title, $output);
        }
    }

    private function CreateTableHeader() {

        $spalten = config("spalten");

        $output = '<tr><th colspan="6" >';
        $output .= '<span style="float:left;" >' . core::GetDay($this->ReplacementHelper->GetDate()). ', ' . gmdate("d.m.Y", $this->ReplacementHelper->GetDate()) . '</span>';
        $output .= '<span style="float:right;" >' . preg_replace("/%update%/", gmdate("d.m. - H:i", $this->GetLastUpdate()), lang()->loc('last.update', false)) . '</span>';
        $output .= '</th></tr>';
        $output .= '<tr><th width="' . $spalten[0] . '">' . lang()->loc('grade', false) . '</th><th width="' . $spalten[1] . '">' . lang()->loc('teacher.short', false) . '</th><th width="' . $spalten[2] . '">' . lang()->loc('lesson.short', false) . '</th><th width="' . $spalten[3] . '">' . lang()->loc('replaced.by', false) . '</th><th width="' . $spalten[4] . '">' . lang()->loc('room', false) . '</th><th width="' . $spalten[5] . '">' . lang()->loc('comment', false) . '</th></tr>';
        return $output;
    }

    private function CreateTeacherTableHeader() {
        $output = '<tr><th colspan="6" >';
        $output .= '<span style="float:left;" >' . core::GetDay($this->ReplacementHelper->GetDate()) . ', ' . gmdate("d.m.Y", $this->ReplacementHelper->GetDate()) . '</span>';
        $output .= '<span style="float:right;" >' . preg_replace("/%update%/", gmdate("d.m. - H:i", $this->GetLastUpdate()), lang()->loc('last.update', false)) . '</span>';
        $output .= '</th></tr>';
        return $output;
    }

    private function GetLastUpdate() {
        $last = -1;
        foreach($this->replacements as $replacements) {
            $tmp = $this->ReplacementHelper->GetLastUpdate($replacements);
            if($last < $tmp)
                $last = $tmp;
        }
        return $last;
    }
    
    public function GetMenu() {
        $menu = "";
        foreach ($this->pages as $i => $page) {
            $menu .= '<span id="info_' . $this->site . '_' . $i . '" class="'.(($i==0) ? 'active' : '').'">' . $page['title'] . '</span> * ';
        }

        $menu = substr($menu, 0, -3);
        return $menu;
    }

    public function GetContent() {
        $output = "";
        foreach ($this->pages as $i => $page) {
            $output .= '<div id="plan_' . $this->site . '_' . $i . '" style="' . (($i != 0) ? 'display:none;' : '') . '">';
            $output .= $page['content'];
            $output .= '</div>';
        }
        return $output;
    }
    
    private function GetPages() {
        $pages = $this->ReplacementHelper->GetPages();
        foreach($pages as $page)
            $this->AddPage($page['title'], $page['content']);
    }

    public function AddPage($title, $content) {
        $this->pages[] = Array('title' => $title, 'content' => $content);
    }
    
    public function GetTickers() {
        return $this->ReplacementHelper->GetTickers();
    }
}
?>