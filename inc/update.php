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

class Update {

    var $version;

    public function Init() {
        include(CWD . "inc/version.php");
        $this->version = $version;
    }

    public function GetUpdates($cache = true) {
        if (isset($_SESSION['updates'][$this->version]) && $cache) {
            return $_SESSION['updates'][$this->version];
        }
       
        $updateData = @file_get_contents("https://api.github.com/repos/flx5/InMaFSS/tags");
        
        if (!$updateData) { 
            return Array();
        }


        $updateData = json_decode($updateData);
        $versions = Array();

        foreach ($updateData as $tag) {
            $versions[$tag->name] = $tag->commit->url;
        }

        $updates = Array();

        foreach ($versions as $v => $url) {
            $ver = preg_replace("#\.#", "", $v);
            if ($ver > $this->version) {
                $updates[$v] = $url;
            }
        }
        ksort($updates, SORT_NUMERIC);

        $_SESSION['updates'][$this->version] = $updates;

        return $updates;
    }

    public function GetChanges($version) {
        set_time_limit(0);
        $updateData = file_get_contents($version);

        if (!$updateData) {
            return Array();
        }

        $updateData = json_decode($updateData);

        $files = Array();

        foreach ($updateData->files as $file) {
            $files[$file->filename] = Array('url' => $file->raw_url, 'status' => $file->status);
        }

        return $files;
    }

    public function GetAllChanges($updates) {
        $changes = Array();

        set_time_limit(0);

        foreach ($updates as $url) {
            foreach ($this->GetChanges($url) as $file => $change) {
                $changes[$file] = $change;
            }
        }
        return $changes;
    }

    public function PerformUpdate($file, $action, $url) {

        set_time_limit(0);

        if ($file == 'inc/config.php' || $file == 'install.php') {
            return;
        }
        
        $use_ftp = config("use_ftp");
        $ftp = config("ftp");

        if ($use_ftp) {
            $conn_id = ftp_connect(gethostbyname($ftp['server']), 21, 5);

            if (!$conn_id) {
                echo "Konnte keine Verbindung zum FTP Server aufbauen";
                return false;
            }

            echo "FTP Connection established<br>";
            flush();

            if (!@ftp_login($conn_id, $ftp['usr'], $ftp['pwd'])) {
                echo "Couldn't login";
                ftp_close($conn_id);
                return false;
            }

            echo "FTP: Logged in<br>";
            flush();
        }      

        switch ($action) {
            default:

                core::SystemError("Unknown Update Status", $action);
                break;

            case 'added':
            case 'modified':

                $fp = fopen($url, "r");
                if (!$fp) {
                    core::SystemError("Network Error", "Couldn't download " . $file);
                }

                $data = "";

                while (($buffer = fgets($fp, 4096)) !== false) {
                    $data .= $buffer;
                }

                if ($file == 'update.txt') {
                    eval($data);
                    return;
                }

                if ($use_ftp) {
                    if (!ftp_fput($conn_id, $ftp['path'] . $file, $fp, FTP_BINARY)) {
                        echo "FTP UPLOAD ERROR<br>";
                        ftp_close($conn_id);
                        return false;
                    }
                } else {
                    file_put_contents(CWD . $file, $data);
                }

                echo 'UPDATE: ' . $file . '<br>';
                break;

            case 'removed':
                if ($use_ftp) {
                    if (!ftp_delete($conn_id, $ftp['path'] . $file)) {
                        echo "FTP: could not delete " . $ftp['path'] . $file . "<br>";
                        ftp_close($conn_id);
                        return false;
                    }
                } else {
                    unlink(DS . $file);
                }
                echo 'REMOVED: ' . $file . '<br>';
                break;
        }

        if ($use_ftp) {
            ftp_close($conn_id);
            echo "FTP Connection closed<br>";
        }

        return true;
    }

}

?>