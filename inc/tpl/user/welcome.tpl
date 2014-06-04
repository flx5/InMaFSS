<?php
require_once(INC . 'class.replacements.php');
lang()->loc('welcome');
$tickers = Array();
?>
<div class="row" id="row1">
    <div style="width:33%;" class="space_wrapper">
        <div class="row_entry_wrapper" >
            <div id="plan_today" class="row_entry">
                <?php GenerateTable($tickers, "today"); ?>
            </div>
        </div>
    </div>
    <div style="width:33%;" class="space_wrapper">
        <div class="row_entry_wrapper">
            <div id="plan_tomorrow" class="row_entry">
                <?php GenerateTable($tickers, "tomorrow"); ?>
            </div>
        </div>
    </div>
    <div style="width:33%;" class="space_wrapper">
        <div class="row_entry_wrapper">
            <div id="information" class="row_entry">
                <h2><?php lang()->loc('information'); ?></h2>
                <table width="100%">
                    <?php
                    $odd_even = true;
                    foreach ($tickers as $ticker) {
                        echo '<tr style="background-color:#' . ($odd_even ? 'ccc' : 'bbb') . ';">';
                        echo '<td>' . $ticker['content'] . '</td>';
                        echo '</tr>';

                        $odd_even = !$odd_even;
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row" id="row2">
    <div style="width:50%;" class="space_wrapper">
        <div class="row_entry_wrapper">
            <div id="mensa" class="row_entry" > 
                <h2><?php lang()->loc('mensa'); ?></h2>
                <?php
                // Defining gmmktime with date command to ensure the usage of the correct date (Could be a problem between 0 and 1 o'clock at UTC+1 i.e.
                $sql = dbquery("SELECT * FROM mensa WHERE day >= " . gmmktime(0, 0, 0, date("n"), date("j"), date("Y")) . " AND day <= " . (gmmktime(0, 0, 0, date("n"), date("j"), date("Y")) + 7 * 24 * 3600));
                while ($day = $sql->fetchAssoc()) {
                    if ($day['menu2'] == "")
                        $day['menu2'] = '&nbsp;';

                    if ($day['menu1'] == "")
                        $day['menu1'] = '&nbsp;';

                    echo '<div class="mensa_entry">';
                    echo '<div class="day_header">' . core::GetDay($day['day']). ', '. gmdate("d.M.Y", $day['day']) . '</div>';
                    echo '<div class="menu_entry_left"><h3>' . lang()->loc('menu', false) . ' 1</h3>' . $day['menu1'] . '</div>';
                    echo '<div class="menu_entry_right"><h3>' . lang()->loc('menu', false) . ' 2</h3>' . $day['menu2'] . '</div>';
                    echo '</div><br>';
                }
                ?>
            </div>
        </div>
    </div>
    <div style="width:50%;" class="space_wrapper">
        <div class="row_entry_wrapper">    
            <div id="appointments" class="row_entry">
                <h2><?php lang()->loc('appointments'); ?></h2>
                <?php
                $sql = dbquery("SELECT * FROM events WHERE startdate >= " . gmmktime(0, 0, 0, date("n"), date("j"), date("Y")) . " AND startdate <= " . (gmmktime(0, 0, 0, date("n"), date("j"), date("Y")) + 7 * 24 * 3600));
                while ($day = $sql->fetchAssoc()) {
                    echo '<div class="appointments_entry">';
                    echo '<div class="day_header">' . $day['title'].'</div>';
                    echo '<div class="appointments_data">';
                    echo '<table width="100%">';
                    echo '<tr><td><b>'.lang()->loc('begin', false).':</b></td><td>' . core::GetDay($day['startdate']). ', '. gmdate("d.M.Y H:i", $day['startdate']) . '</td></tr>';
                    echo '<tr><td><b>'.lang()->loc('end', false).':</b></td><td>' . core::GetDay($day['end']). ', '. gmdate("d.M.Y H:i", $day['end']) . '</td></tr>';
                    
                    if($day['content'] != "") {
                        echo '<tr><td><b>'.lang()->loc('desc', false).':</b></td><td>' . $day['content'] . '</td></tr>';
                    }
                    
                    echo '</table>';
                    echo '</div>';
                    echo '</div><br>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php

function GenerateTable(&$tickers, $day) {
    $replacements = new Replacements(Authorization::GetUserType('LDAP'), $day);
    $tickers = $replacements->MergeTickers($tickers, $replacements->GetTickers());

    echo '<h2>' .  core::GetDay($replacements->GetDate()). ', '. gmdate("d.M.Y", $replacements->GetDate()). '</h2>';

    if (Authorization::GetUserType('LDAP') == ReplacementsTypes::TEACHER) {
        $data = $replacements->GetReplacements(
                Array(
                    Array('type' => 'fullname', 'value' => Authorization::GetDisplayName('LDAP'))
                )
        );
        if (count($data) > 1) {
            echo '<font color="#ff0000">ACHTUNG: Ihr Account wurde nicht eindeutig erkannt!<br>Es ist möglich, dass sie falsche Daten angezeigt bekommen.<br>';
            echo 'Es wurden diese K&uuml;rzel erkannt: ' . implode(", ", array_keys($data)) . '</font>';
        }
    } else {
        $data = $replacements->GetReplacements(Authorization::GetClasses('LDAP'));
    }
    ?>

    <table width = "100%">
        <?php
        if (Authorization::GetUserType('LDAP') == ReplacementsTypes::TEACHER) {
            echo '<tr><th>Std.</th><th>Klasse</th><th>Raum</th><th>Bemerkung</th></tr>';
        } else {
            echo '<tr><th>Klasse</th><th>Std.</th><th>Lehrer</th><th>vertreten durch</th><th>Raum</th><th>Bemerkung</th></tr>';
        }

        foreach ($data as $k=>$grade) {
            $first = true;
            foreach ($grade as $replacement) {
                echo '<tr>';
                if($first && Authorization::GetUserType('LDAP') == ReplacementsTypes::PUPIL)
                    echo '<td rowspan='.count($grade).'>' . $k.'</td>';
                
                echo '<td>' . $replacement['lesson'] . '</td>';
                if (Authorization::GetUserType('LDAP') == ReplacementsTypes::TEACHER) {
                    echo '<td>' . $replacement['grade'] . '</td>';
                } else {
                    echo '<td>' . $replacement['teacher'] . '</td>';
                    echo '<td>' . $replacement['replacement'] . '</td>';
                }
                echo '<td>' . $replacement['room'] . '</td>';
                echo '<td>' . $replacement['comment'] . '</td>';
                echo '</tr>';
                $first = false;
            }
        }
        ?>
    </table>
<?php } ?>