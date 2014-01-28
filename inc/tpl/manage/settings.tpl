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
    <div class="round" style="width:90%; border:2px solid black; margin:5px auto; margin-top:20px;">
        <h2><?php lang()->loc('title'); ?></h2>
        <div class="inner">
            <?php
            if (isset($_POST['update'])) {
                $fields = getVar("sql")->getFieldsInfo('settings');

                foreach ($fields as $field) {
                    $fieldName = $field['name'];

                    switch (strtolower($field['type'])) {
                        case 'varchar':
                        case 'text':
                            if (isset($_POST[$fieldName])) {
                                dbquery("UPDATE settings SET " . filter($fieldName) . " = '" . filter($_POST[$fieldName]) . "'");
                            }
                            break;
                        case 'int':
                            if (isset($_POST[$fieldName]) && ctype_digit(filter($_POST[$fieldName]))) {
                                dbquery("UPDATE settings SET " . filter($fieldName) . " = " . intval(filter($_POST[$fieldName])));
                            }
                            break;
                        case 'enum':
                            if (isset($_POST[$fieldName]) && in_array($_POST[$fieldName], $field['enum'])) {
                                dbquery("UPDATE settings SET " . filter($fieldName) . " = '" . filter($_POST[$fieldName]) . "'");
                            }
                            break;
                        case 'tinyint':
                            if (isset($_POST[$fieldName])) {
                                dbquery("UPDATE settings SET " . filter($fieldName) . " = 1");
                            } else {
                                dbquery("UPDATE settings SET " . filter($fieldName) . " = 0");
                            }
                            break;
                    }
                }

                $_SESSION['showOK'] = true;
                header("Location: settings.php");
                exit;
            }

            if (isset($_SESSION['showOK']) && $_SESSION['showOK']) {
                unset($_SESSION['showOK']);
                core::SuccessMessage(lang()->loc('saved', false));
            }
            ?>
            <form method="post" action="">
                <table width="100%" border="1">
                    <tr><th width="50%"><?php lang()->loc('settings.name'); ?></th><th><?php lang()->loc('value'); ?></th></tr>

                    <input type="hidden" name="update" value="1">
                    <?php
                    $sql = dbquery("SELECT * FROM settings LIMIT 1");
                    $fields = getVar('sql')->getFieldsInfo('settings');

                    if ($sql->count() == 0) {
                        lang()->loc('no.settings.found');
                    } else {
                        $data = $sql->fetchAssoc();

                        foreach ($fields as $field) {
                            echo '<tr><td>' . lang()->loc($field['name'], false, true) . '</td><td>';

                            switch ($field['type']) {
                                default:
                                    echo "UNKOWN: " . $field['type'];
                                    break;
                                case 'varchar':
                                case 'text':
                                case 'int':
                                    echo '<input style="width:90%; background-color:#ddd;" type="text" name="' . $field['name'] . '" value="' . $data[$field['name']] . '">';
                                    break;
                                case 'tinyint':
                                    echo '<input type="checkbox" name="' . $field['name'] . '" ' . (($data[$field['name']] == 1) ? 'checked' : '' ) . '>'; // TODO
                                    break;
                                case 'enum':
                                    echo '<select name="' . $field['name'] . '">';
                                    foreach ($field['enum'] as $option) {
                                        echo '<option ' . (($data[$field['name']] == $option) ? 'selected' : '' ) . '>' . lang()->loc($option, false, true) . '</option>';
                                    }
                                    echo '</select>';
                                    break;
                            }

                            echo '</td></tr>';
                        }
                    }
                    ?>


                </table> 
                <center>
                    <input type="submit" value="<?php lang()->loc('save'); ?>">
                </center>
            </form>
        </div>
    </div>
</div>
