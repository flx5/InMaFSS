<?php
/* =================================================================================*\
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
  \*================================================================================= */

lang()->add('info');
?>
<div class="round" style="width:60%; border:2px solid black; margin:5px auto;">
    <h2>Information</h2>
    <div class="inner">
        <?php
        require_once(INC."class.replacements.php");
        $left = new Replacements(ReplacementsTypes::PUPIL, 'today');
        $today['repl_count'] = count($left->GetReplacements());

        $right = new Replacements(ReplacementsTypes::PUPIL, 'tomorrow');
        $nextday['repl_count'] = count($right->GetReplacements());

        $nextday['pageCount'] = count($right->GetPages());
        $today['pageCount'] = count($left->GetPages());

        echo '<b>' . lang()->loc('today', false) . '</b><br>';

        if ($today['repl_count'] == 0) {
            lang()->loc('no.plan');
            echo "<br>";
        } else {
            echo $today['repl_count'] . ' ';
            lang()->loc('replacements');
            echo '<br>';
        }

        if ($today['pageCount'] == 0) {
            lang()->loc('no.page');
            echo "<br>";
        } else {
            echo $today['pageCount'] . ' ';
            lang()->loc('pages');
            echo '<br>';
        }

        echo '<br><b>' . lang()->loc('next.day', false) . '</b><br>';

        if ($nextday['repl_count'] == 0) {
            lang()->loc('no.plan');
            echo "<br>";
        } else {
            echo $nextday['repl_count'] . ' ';
            lang()->loc('replacements');
            echo '<br>';
        }

        if ($today['pageCount'] == 0) {
            lang()->loc('no.page');
            echo "<br>";
        } else {
            echo $today['pageCount'] . ' ';
            lang()->loc('pages');
            echo '<br>';
        }
        ?>
    </div>
</div>
