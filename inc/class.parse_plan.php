<?php
class parsePlan implements ParseInterface {

    var $replacements = Array(0 => Array(), 1 => Array());
    var $notes = Array(0 => Array(), 1 => Array());
    var $teachers = Array();
    var $grades = Array();
    var $school = Array();
    var $rooms = Array();
    var $supervisor = Array();

    public function parse($content) {

        if ($content == "")
            return false;

        $content = html_entity_decode($content);
        $content = preg_replace("#\r\n#", "\n", $content);

        require_once(CWD . DS . "inc" . DS . "parse" . DS . "plan" . DS . config("system") . ".php");
        $parser = "PARSE_PLAN_" . strtoupper(config("system"));
        $parser = new $parser();

        $data = $parser->Parse($content);

        if($data === false)
            return false;
        
        if (isset($data['not_available'])) {
            $this->teachers[] = $data['not_available'];
        }
        if (isset($data['school'])) {
            $this->school[] = $data['school'];
        }
        if (isset($data['rooms'])) {
            $this->rooms[] = $data['rooms'];
        }
        if (isset($data['aufsicht'])) {
            $this->supervisor[] = $data['aufsicht'];
        }
        if (isset($data['replacements'])) {
            $this->replacements[$data['type']][] = $data['replacements'];
        }
        if (isset($data['notes'])) {
            $this->notes[$data['type']][] = $data['notes'];
        }
        if (isset($data['grades'])) {
            $this->grades[] = $data['grades'];
        }
        
        return true;
    }

    public function CleanDatabase() {
        $stamp = gmmktime(0, 0, 0);
        dbquery("DELETE FROM others WHERE timestamp<" . $stamp);
        dbquery("DELETE FROM replacements WHERE timestamp<" . $stamp);
        dbquery("DELETE FROM teacher_substitude WHERE timestamp<" . $stamp);
        dbquery("DELETE FROM ticker WHERE to_stamp<" . $stamp);
    }

    public function UpdateDatabase() {

        $this->CleanDatabase();

        foreach ($this->replacements[0] as $replacements) {

            $stamps = Array();

            foreach ($replacements as $k => $grade) {
                foreach ($grade as $data) {
                    $gradeData = Core::String2Grade($k);

                    if (!in_array($data['stamp_for'], $stamps)) {
                        dbquery("DELETE FROM replacements WHERE timestamp = '" . $data['stamp_for'] . "'");
                    }

                    $stamps[] = $data['stamp_for'];

                    dbquery("INSERT INTO replacements (grade_pre, grade, grade_last, lesson, teacher, replacement, room, comment, timestamp, timestamp_update, addition) VALUES ('" . $gradeData['prefix'] . "', '" . $gradeData['num'] . "','" . $gradeData['suffix'] . "','" . $data['lesson'] . "','" . filter($data['teacher']) . "','" . filter($data['replacement']) . "', '" . filter($data['room']) . "', '" . filter($data['hint']) . "', '" . $data['stamp_for'] . "','" . $data['stamp_update'] . "', '" . $data['addition'] . "')");
                }
            }
        }

        foreach ($this->replacements[1] as $replacements) {

            $stamps = Array();

            foreach ($replacements as $k => $teacher) {
                foreach ($teacher as $data) {

                    if (!in_array($data['stamp_for'], $stamps)) {
                        dbquery("DELETE FROM teacher_substitude WHERE timestamp = '" . $data['stamp_for'] . "'");
                    }

                    $stamps[] = $data['stamp_for'];

                    dbquery("INSERT INTO teacher_substitude (short, lesson, teacher, grade, room, comment, timestamp, timestamp_update, addition) VALUES ('" . $k . "','" . $data['lesson'] . "','" . filter($data['teacher']) . "', '" . $data['grade'] . "' , '" . filter($data['room']) . "', '" . filter($data['hint']) . "', '" . $data['stamp_for'] . "','" . $data['stamp_update'] . "', '" . $data['addition'] . "')");
                }
            }
        }

        foreach ($this->notes[0] as $notes) {

            $stamps = Array();

            foreach ($notes as $note) {
                if (!in_array($note['stamp_for'], $stamps)) {
                    dbquery("DELETE FROM ticker WHERE automatic = 1 AND from_stamp = '" . $note['stamp_for'] . "'");
                    $stamps[] = $note['stamp_for'];
                }
                dbquery("INSERT INTO ticker (automatic, value, from_stamp, to_stamp, `order`) SELECT 1, '" . filter($note['content']) . "', '" . $note['stamp_for'] . "', '" . gmmktime(23, 59, 59, date("n", $note['stamp_for']), date("j", $note['stamp_for']), date("Y", $note['stamp_for'])) . "', COALESCE(MAX(`order`),0)+1 FROM ticker");
            }
        }

        $others = Array('t' => $this->teachers, 'g' => $this->grades, 's' => $this->school, 'r' => $this->rooms, 'a' => $this->supervisor, 'n' => $this->notes[1]);

        $stamps = Array();

        foreach ($others as $type => $val) {
            foreach ($val as $datas) {

                foreach ($datas as $data) {
                    if (!in_array($data['stamp_for'], $stamps)) {
                        dbquery("DELETE FROM others WHERE timestamp = '" . $data['stamp_for'] . "'");
                        $stamps[] = $data['stamp_for'];
                    }

                    dbquery("INSERT INTO others (type, name, lesson, comment, timestamp, timestamp_update, addition) VALUES ('" . $type . "', '" . filter($data['teacher']) . "', '" . $data['lesson'] . "', '" . filter($data['reason']) . "', '" . $data['stamp_for'] . "','" . $data['stamp_update'] . "', '" . $data['addition'] . "')");
                }
            }
        }
    }

}
?>
