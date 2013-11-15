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
?>

<div class="content">
    <div style="width:90%; border:2px solid black; margin:5px auto; margin-top:20px;">
        <h2><?php lang()->loc('title'); ?></h2>
        <?php
        if (isset($_POST['id']) && isset($_POST['value']) && isset($_POST['time_from']) && isset($_POST['time_end']) && is_numeric($_POST['id'])) {
            $error = Array();
            $id = intval(filter($_POST['id']));
            $value = filter($_POST['value']);
            $time_from = filter($_POST['time_from']);
            $time_end = filter($_POST['time_end']);
            $order = filter($_POST['order']);

            if ($value == "") {
                $error[] = lang()->loc('content.empty', false);
            }

            if (!is_numeric($order)) {
                $error[] = lang()->loc('order.string', false);
            }

            $time_from = explode(".", $time_from);
            $time_end = explode(".", $time_end);

            $time = true;

            if (count($time_from) != 3) {
                $error[] = lang()->loc('err.startdate', false);
                $time = false;
            }

            if (count($time_end) != 3) {
                $error[] = lang()->loc('err.enddate', false);
                $time = false;
            }

            if ($time) {

                $time_from = gmmktime(0, 0, 0, $time_from[1], $time_from[0], $time_from[2]);
                $time_end = gmmktime(23, 59, 59, $time_end[1], $time_end[0], $time_end[2]);

                if ($time_from > $time_end) {
                    $error[] = lang()->loc('err.end.before.start', false);
                }
            }

            if (count($error) == 0) {
                if ($id != -1) {
                    dbquery("UPDATE ticker SET value = '" . $value . "', from_stamp = '" . $time_from . "', to_stamp = '" . $time_end . "', `order` = '" . $order . "' WHERE id = '" . $id . "'");
                } else {
                    dbquery("INSERT INTO ticker (value, from_stamp, to_stamp, `order`) SELECT '" . $value . "', '" . $time_from . "', '" . $time_end . "', COALESCE(MAX(`order`),0)+1 FROM ticker");
                }
            } else {
                foreach ($error as $err) {
                    echo $err . "<br>";
                }
            }
        }

        if (isset($_GET['del']) && (!isset($_GET['do']) || $_GET['do'] != 'del')) {
            echo '<font size="+2" color="#FF0000">' . lang()->loc('del.rly', false) . ' <a href="?del=' . $_GET['del'] . '&do=del">' . lang()->loc('delete', false) . '</a>&nbsp;|&nbsp;<a href="ticker.php">' . lang()->loc('abort', false) . '</a></font>';
        }

        if (isset($_GET['del']) && isset($_GET['do']) && $_GET['do'] == 'del' && is_numeric($_GET['del'])) {
            dbquery("DELETE FROM ticker WHERE id = " . $_GET['del']);
            echo '<font size="+2" color="#00ff00">' . lang()->loc('deleted', false) . '</font>';
        }
        ?>
        <table width="100%" border="1">
            <tr><th><?php lang()->loc('id'); ?></th><th><?php lang()->loc('ordernum'); ?></th><th><?php lang()->loc('text'); ?></th><th><?php lang()->loc('from'); ?></th><th><?php lang()->loc('until'); ?></th><th colspan="2" ><?php lang()->loc('options'); ?></th></tr>
            <?php
            $sql = dbquery("SELECT * FROM ticker ORDER BY `order`");

            while ($tick = $sql->fetchAssoc()) {
                echo '<tr><form method="post"><td><input type="text" value="' . $tick['id'] . '" name="id" style="display:none;">' . $tick['id'] . '</td><td>&nbsp;<input type="text" value="' . $tick['order'] . '" name="order" size="3" ></td><td>&nbsp;<input type="text" name="value" style="width:95%" value="' . $tick['value'] . '">&nbsp;</td><td><input type="text" style="" class="tcal" name="time_from" value="' . date(lang()->info('date.format', false), $tick['from_stamp']) . '"></td><td><input type="text" style="" class="tcal" name="time_end" value="' . date(lang()->info('date.format', false), $tick['to_stamp']) . '"></td><td><input type="submit" value="' . lang()->loc('save', false) . '"></td><td><a href="?del=' . $tick['id'] . '">' . lang()->loc('delete', false) . '</a></td></form></tr>';
            }
            ?>
            <tr><form method="post"><td><input type="text" value="-1" name="id" style="display:none;"></td><td><input type="text" value="-1" name="order" style="display:none;"></td><td>&nbsp;<input type="text" name="value" style="width:95%" value="">&nbsp;</td><td><input type="text" style="" class="tcal" name="time_from"></td><td><input type="text" style="" class="tcal" name="time_end" ></td><td><input type="submit" value="<?php lang()->loc('add'); ?>"></td><td></td></form></tr>
        </table>
    </div></div>