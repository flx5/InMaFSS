<table width="100%">
    <?php
    $spalten = config('spalten');
    lang()->add('home');
    echo '<tr><th width="' . $spalten[2] . '">' . lang()->loc('lesson.short', false) . '</th><th width="' . $spalten[1] . '">' . lang()->loc('teacher.short', false) . '</th><th width="' . $spalten[3] . '">' . lang()->loc('replaced.by', false) . '</th><th width="' . $spalten[4] . '">' . lang()->loc('room', false) . '</th><th width="' . $spalten[5] . '">' . lang()->loc('comment', false) . '</th></tr>';

    $view = new view('left', 800);
    $view->AddRepacements();

    $gradeFilter = '12q';

    foreach ($view->replacements as $page) {
        foreach ($page as $grade => $val) {

            $pos = findFirstLetter($grade);

            $gN = substr($grade, 0, $pos);
            $gS = substr($grade, $pos);

            if (strlen($gS) > 1 && $pos > 0) {
                $gS = substr($gS, 0, 1);
            }

            $_grade = $gN . $gS;

            if (!isset($gradeFilter) || $_grade == $gradeFilter ||
                    ($gradeFilter == "11q" && ($_grade == "Q11" || $gN == "11")) ||
                    ($gradeFilter == "12q" && ($_grade == "Q12" || $gN == "12")) ||
                    (strlen($gN) == 0 && $_grade != "Q11" && $_grade != "Q12")
            ) {
                foreach ($val as $entry) {
                    echo '<tr>';
                    echo '<td>' . $entry['lesson'] . '</td>';
                    echo '<td>' . $entry['teacher'] . '</td>';
                    echo '<td>' . $entry['replacement'] . '</td>';
                    echo '<td>' . $entry['room'] . '</td>';
                    echo '<td>' . $entry['comment'] . '</td>';
                    echo '</tr>';
                }
            }
        }
    }
    ?>
</table>