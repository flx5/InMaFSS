<?php
/*=================================================================================*\
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
\*=================================================================================*/


class Update {
        var $version;

        public function Update() {
              include(CWD."inc/version.php");
              $this->version = $version;
        }

        public function GetUpdates($cache = true)
        {
                 if(isset($_SESSION['updates'][$this->version]) && $cache) {
                       return $_SESSION['updates'][$this->version];
                 }

                 $updateData = @file_get_contents("https://api.github.com/repos/flx5/InMaFSS/tags");
                 if(!$updateData) {
                   return Array();
                 }


                 $updateData = json_decode($updateData);
                 $versions = Array();

                 foreach($updateData  as $tag) {
                           $versions[$tag->name] = $tag->commit->url;
                 }

                 $updates = Array();

                 foreach($versions as $v=>$url) {
                     $ver = preg_replace("#\.#","",$v);
                     if($ver > $this->version) {
                       $updates[$v] = $url;
                     }
                 }
                 ksort($updates, SORT_NUMERIC);

                 $_SESSION['updates'][$this->version] = $updates;

                 return $updates;
        }

        public function GetChanges($version) {
                $updateData = file_get_contents($version);

                if(!$updateData) {
                   return Array();
                }

                $updateData = json_decode($updateData);

                $files = Array();

                foreach($updateData->files as $file) {
                         $files[$file->filename] = Array('url'=>$file->raw_url, 'status'=>$file->status);
                }

                return $files;
        }

        public function GetAllChanges($updates) {
                 $changes = Array();

                 foreach($updates as $url) {
                      foreach($this->GetChanges($url) as $file=>$change) {
                              $changes[$file] = $change;
                      }
                 }
                 return $changes;
        }

        public function PerformUpdate($changes) {
              if(count($changes) == 0) {
                return false;
              }

              global $core, $use_ftp, $ftp;
              set_time_limit(0);

              $perfile = 90/count($changes);
              $value = 0;

              if($use_ftp) {
                    $conn_id = ftp_connect(gethostbyname($ftp['server']), 21, 5) or die("Konnte keine Verbindung zum FTP Server aufbauen");

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

              foreach($changes as $file=>$change) {
                    if($file == 'inc/config.php' || $file == 'install.php') {
                         continue;
                    }

                    switch($change['status']) {
                       default:

                         $core->SystemError("Unknown Update Status", $change['status']);
                       break;

                       case 'added':
                       case 'modified':

                          $data = file_get_contents($change['url']);
                          if(!$data) {
                               $core->SystemError("Network Error", "Couldn't download ".$file);
                          }

                          if($use_ftp) {
                              if(!ftp_put($conn_id, $ftp['path'].$file, $change['url'], FTP_ASCII)) {
                                   echo "FTP UPLOAD ERROR<br>";
                                   ftp_close($conn_id);
                                   return false;
                              }
                          } else {
                              file_put_contents(CWD.$file, $data);
                          }

                          echo 'UPDATE: '.$file.'<br>';
                       break;

                       case 'removed':
                          if($use_ftp) {
                              if (!ftp_delete($conn_id, $ftp['path'].$file)) {
                               echo "FTP: could not delete ".$ftp['path'].$file."<br>";
                               ftp_close($conn_id);
                               return false;
                              }
                          } else {
                            unlink(DS.$file);
                          }
                          echo 'REMOVED: '.$file.'<br>';
                       break;
                    }
                    $value = $value+$perfile;

                    echo '<div style="position:absolute; top:10px; left:30px; width:90%; background-color:#C0C0C0; border-color:black; height:30px;"></div>';
                    echo '<div style="position:absolute; top:10px; left:30px; width:'.$value.'%; background-color:#00FF00; border-color:black; height:30px;"></div>';
                    echo '<div style="position:absolute; top:10px; left:30px; width:90%; text-align:center; padding:5px;">'.(($value/90)*100).'%</div>';
                    flush();
              }

              if($use_ftp) {
                ftp_close($conn_id);
              }
              return true;
        }
}
?>