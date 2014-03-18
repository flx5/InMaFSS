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
    <div class="round" style="width:90%; border:2px solid black; margin:5px auto; margin-top:20px; text-align:center;">
        <h2><?php lang()->loc('title'); ?></h2>
        <div class="inner">
            <?php
            if (isset($_GET['del'])) {
                dbquery("DELETE FROM oauth_clients WHERE client_id = '" . filter($_GET['del']) . "'");

                if (getVar('sql')->affected_rows() == 0)
                    lang()->loc('deletion.failure');
                else {
                    // Clean the database and remove tokens that are part of this app
                    dbquery("DELETE FROM oauth_access_tokens WHERE client_id = '" . filter($_GET['del']) . "'");
                    dbquery("DELETE FROM oauth_refresh_tokens WHERE client_id = '" . filter($_GET['del']) . "'");
                    lang()->loc('deleted');
                }
            }
            ?>
            <table width="100%" border="1">
                <tr><th><?php lang()->loc('application_title'); ?></th><th colspan="2" width="30%" ><?php lang()->loc('options'); ?></th></tr>
                <?php
                $sql = dbquery("SELECT * FROM oauth_clients");
                while ($app = $sql->fetchAssoc()) {
                    echo '<tr><td>' . $app['title'] . '</td></td>';
                    echo '<td><a href="oauth_edit.php?key=' . $app['client_id'] . '">' . lang()->loc('edit', false) . '</a></td><td><a href="?del=' . $app['client_id'] . '">' . lang()->loc('delete', false) . '</a></td></tr>';
                }
                ?>
                <tr><td></td><td colspan="2"><a href="oauth_edit.php?new"><?php lang()->loc('new'); ?></a></td></tr>
            </table>
        </div>
    </div>
</div>