<?php

class ReplacementsTypes {

    const PUPIL = 0;
    const TEACHER = 1;

}

require_once(INC . 'class.time_helper.php');

class Replacements {

    private $type;
    private $day;
    private $tfrom;
    private $tend;

    public function __construct($type, $day) {
        $this->type = $type;
        $this->day = $day;

        $this->tfrom = $this->GetTFrom();
        $this->tend = $this->tfrom + 24 * 3600 - 1;
    }

    public function GetTickers() {
        $sql = dbquery("SELECT * FROM ticker WHERE from_stamp < '" . $this->tend . "' AND to_stamp > '" . $this->tfrom . "' ORDER BY to_stamp, `order`");
        $tickers = Array();
        while ($ticker = $sql->fetchObject()) {
            $tickers[] = Array('day' => (int) $ticker->from_stamp, 'content' => $ticker->value, 'automatic' => (bool) $ticker->automatic);
        }

        return $tickers;
    }

    public function MergeTickers($day1, $day2) {
        $tickers = array_merge($day1, $day2);
        $tickers = array_unique($tickers, SORT_REGULAR);
        usort($tickers, Array($this, 'SortTickers'));
        return $tickers;
    }

    private function SortTickers($a, $b) {
        if ($a['day'] == $b['day'])
            return 0;

        return (($a['day'] < $b['day']) ? -1 : 1);
    }

    private function GetTFrom() {
        return TimeHelper::GetTFrom($this->day);
    }

    public function GetOthers() {
        $content = Array();

        $where = "timestamp >= " . $this->tfrom . " AND timestamp <= " . $this->tend;

        $sql = dbquery("SElECT * FROM others WHERE " . $where . " ORDER BY type, id");

        while ($data = $sql->fetchAssoc()) {
            $content[$data['type']][] = $data;
        }
        return $content;
    }

    private function GetTeacherReplacements($where, $filter) {
        $content = Array();

        $filterQuery = Array();

        if ($filter !== null) {
            $s = Array('/ae/', '/oe/', '/ue/', '/Ae/', '/Ue/', '/Oe/', '/ß/');
            $r = Array('&auml;', '&ouml;', '&uuml;', '&Auml;', '&Uuml;', '&Ouml;', '&szlig;');

            foreach ($filter as $k => $teacher) {
                switch ($teacher['type']) {
                    case 'fullname':
                        $fullname = preg_replace($s, $r, $teacher['value']);
                        $lastname = substr($fullname, strrpos($fullname, " ") + 1);
                        $prename = substr($fullname, 0, strrpos($fullname, " "));

                        $name = $lastname . ' ' . $prename;
                        $name_abbrv = $lastname . ' ' . substr($prename, 0, 1) . '.';

                        // Sometimes the names are written with a ss instead of a ß, but there may also be names with ss, so do this optional
                        $alternative_name = preg_replace("/ss/", '&szlig;', $name);
                        $alternative_name_abbrv = preg_replace("/ss/", '&szlig;', $name_abbrv);

                        $filterQuery[] = "(teacher = '" . filter($name) . "' OR teacher = '" . filter($name_abbrv) . "' OR teacher = '" . filter($alternative_name) . "' OR teacher = '" . filter($alternative_name_abbrv) . "')";
                        break;
                }
            }

            if (count($filterQuery) != 0)
                $where .= " AND " . implode(" OR ", $filterQuery);
        }

        $sql = dbquery("SElECT * FROM teacher_substitude WHERE " . $where . " ORDER BY id");

        while ($data = $sql->fetchAssoc()) {
            $content[$data['short']][] = $data;
        }

        return $content;
    }

    private function GetPupilReplacements($where, $filterGrades) {
        $content = Array();

        $gradeFilter = Array();
        $whereFilter = "";

        // Using explicit comparisation as an empty Array would be equals null?!
        if ($filterGrades !== null) {
            foreach ($filterGrades as $grade) {
                $grade = core::String2Grade($grade);

                if ($grade['prefix'] == 'Q' && ($grade['num'] == 11 || $grade['num'] == 12)) {
                    $gradeFilter[] = "grade = " . filter($grade['num']);
                } else {
                    $gradeFilter[] = "(grade = " . filter($grade['num']) . " AND grade_pre = '" . filter($grade['prefix']) . "' AND grade_last LIKE  '" . filter($grade['suffix']) . "%')";
                }
            }

            // Deliver nothing if we've got an empty filter (that was not set to null)
            if (count($gradeFilter) == 0)
                return Array();
            
            $filterString = implode(" OR ", $gradeFilter);
            $whereFilter = " AND " . $filterString;
        }



        $sql = dbquery("SElECT * FROM replacements WHERE " . $where.$whereFilter . " AND grade != 0 ORDER BY CAST(grade AS SIGNED) , grade_pre, grade_last, lesson");
        // Add additional lessons marked as WU.
        $sql2 = dbquery("SElECT * FROM replacements WHERE " . $where . " AND grade = 0 ORDER BY grade_pre, grade_last, lesson");

        while ($data = $sql->fetchAssoc()) {
            $content[$data['grade_pre'] . $data['grade'] . $data['grade_last']][] = $data;
        }
        while ($data = $sql2->fetchAssoc()) {
            $content[$data['grade_pre'] . $data['grade_last']][] = $data;
        }

        foreach ($content as $k => $lessons) {
            usort($lessons, Array($this, 'my_sort'));
            $content[$k] = $lessons;
        }

        return $content;
    }

    public function GetReplacements($filter = null) {
        $where = "timestamp >= " . $this->tfrom . " AND timestamp <= " . $this->tend;

        if ($this->type == ReplacementsTypes::TEACHER)
            return $this->GetTeacherReplacements($where, $filter);

        return $this->GetPupilReplacements($where, $filter);
    }

    public function GetPages() {
        $where = "timestamp_from <= " . $this->tfrom . " AND timestamp_end >= " . $this->tend;

        if ($this->type == ReplacementsTypes::TEACHER) {
            $where .= " AND teachers = 1";
        } elseif ($this->type == ReplacementsTypes::PUPIL) {
            $where .= " AND pupils = 1";
        }

        $sql = dbquery("SELECT * FROM pages WHERE " . $where . " ORDER BY order_num,id ASC");

        $pages = Array();

        while ($page = $sql->fetchArray()) {
            $pages[] = $page;
        }

        return $pages;
    }

    private function my_sort($a, $b) {

        if ($a['lesson'] == $b['lesson']) {
            return 0;
        }

        if ($a['lesson'] == 'M' && $b['lesson'] > 6) {
            return -1;
        }

        if ($b['lesson'] == 'M' && $a['lesson'] > 6) {
            return 1;
        }

        if ($a['lesson'] == 'M' && $b['lesson'] <= 6) {
            return 1;
        }

        if ($b['lesson'] == 'M' && $a['lesson'] <= 6) {
            return -1;
        }
        return ($a['lesson'] < $b['lesson']) ? -1 : 1;
    }

    public function GetLastUpdate($replacements) {
        $last_update = 0;
        foreach ($replacements as $grade) {
            foreach ($grade as $val) {
                if ($last_update < $val['timestamp_update']) {
                    $last_update = $val['timestamp_update'];
                }
            }
        }

        return $last_update;
    }

    public function GetDate() {
        return $this->tfrom;
    }

}

?>
