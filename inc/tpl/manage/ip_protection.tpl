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
    <div class="round" style="width:90%; margin:5px auto; margin-top:20px;">
        <h2><?php lang()->loc('title'); ?></h2>
        <div class="inner">
            <?php
            if (isset($_GET['del']) && is_numeric($_GET['del']) && (!isset($_GET['do']) || $_GET['do'] != 'del')) {
                $sql = dbquery("SELECT ip_range FROM ip_protection WHERE id = " . filter($_GET['del']));
                if ($sql->count() == 0) {
                    echo '<font size="+2" color="#FF0000">' . lang()->loc('not.found', false) . '</font>';
                } else {
                    echo '<font size="+2" color="#FF0000">' . lang()->loc('del.rly', false) . ' <a href="?del=' . $_GET['del'] . '&do=del">' . lang()->loc('delete', false) . '</a>&nbsp;|&nbsp;<a href="?">' . lang()->loc('abort', false) . '</a></font>';
                }
            }

            if (isset($_GET['del']) && isset($_GET['do']) && $_GET['do'] == 'del' && is_numeric($_GET['del'])) {
                dbquery("DELETE FROM ip_protection WHERE id = " . filter($_GET['del']));
                echo '<font size="+2" color="#00ff00">' . lang()->loc('deleted', false) . '</font>';
            }

            if (isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['range'])) {
                $error = Array();

                if (empty($_POST['range']))
                    $error[] = lang()->loc('empty.range');

                require_once(INC . 'libs/ip_in_range/ip_in_range.php');

                try {
                    ip_in_range("127.0.0.1", $_POST['range']);
                } catch (Exception $e) {
                    $error[] = lang()->loc('invalid.range', false);
                }

                if (count($error) == 0) {
                    if($_POST['id'] == -1) 
                        dbquery("INSERT INTO ip_protection (ip_range) VALUES ('".filter($_POST['range'])."')");
                    else
                        dbquery("UPDATE ip_protection SET ip_range = '".filter($_POST['range'])."' WHERE id = ".filter($_POST['id']));
                } else {
                    foreach ($error as $err) {
                        echo $err . "<br>";
                    }
                }
            }
            ?>
            <table width="100%" border="1">
                <tr><th><?php lang()->loc('id'); ?></th><th><?php lang()->loc('range'); ?></th><th colspan="2">Optionen</th></tr>
                <?php
                $users = dbquery("SELECT id,ip_range FROM ip_protection");
                while ($range = $users->fetchArray()) {
                    echo '<tr><form method="post">';
                    echo '<td><input type="hidden" value="' . $range['id'] . '" name="id">' . $range['id'] . '</td>';
                    echo '<td><input type="text" name="range" value="' . $range['ip_range'] . '" style="width:95%"></td>';
                    echo '<td><input type="submit" value="' . lang()->loc('save', false) . '"></td><td><a href="?del=' . $range['id'] . '">' . lang()->loc('delete', false) . '</a></td>';
                    echo '</form></tr>';
                }
                ?>
                <tr><form method="post"><td><input type="hidden" value="-1" name="id"></td><td><input type="text" name="range" value="" style="width:95%"></td><td colspan="2"><input type="submit" value="<?php lang()->loc('add'); ?>"></td></form></tr>
            </table>
        </div>
    </div>
</div>
