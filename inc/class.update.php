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

    const BASE_URL = "https://api.github.com/repos/flx5/InMaFSS";

    private $tmpFile;
    private $tmpFileName;

    public function __construct() {
        include(CWD . "inc/version.php");
        $this->version = $version;
    }

    private function IsOK($result) {
        if (!is_object($result)) {
            return true;
        }
        if (property_exists($result, 'message') && strpos($result->message, "API rate limit") !== false) {
            return false;
        }
        return true;
    }

    public function GetLatest($cache = true) {
        if (isset($_SESSION['updates'][$this->version]) && $cache) {
            return $_SESSION['updates'][$this->version];
        }


        $releases = $this->GetFSock(self::BASE_URL . "/releases");

        if ($releases === false) {
            return false;
        }

        $releases = json_decode($releases['content']);

        if (!$this->IsOK($releases)) {
            $_SESSION['updates'][$this->version] = false;
            return false;
        }

        $latestRelease = null;

        foreach ($releases as $release) {
            if ($release->prerelease == false) {
                $latestRelease = $release;
                break;
            }
        }

        if ($latestRelease == null) {
            $_SESSION['updates'][$this->version] = false;
            return false;
        }

        $newVersion = $latestRelease->tag_name;
        $oldVersion = core::GetVersion();

        $compare = core::CompareVersion($oldVersion, $newVersion);

        if ($compare == 1) {
            $_SESSION['updates'][$this->version] = Array('name' => $newVersion, 'id' => $latestRelease->id);
        } else {
            $_SESSION['updates'][$this->version] = false;
        }

        return $_SESSION['updates'][$this->version];
    }

    private function GetURL($versionID) {
        $release = $this->GetFSock(self::BASE_URL . "/releases/" . $versionID);
        $release = json_decode($release['content']);

        if (!$this->IsOK($release)) {
            return false;
        }

        if (!isset($release->zipball_url))
            return false;

        return $release->zipball_url;
    }

    private function GetFSock($url, $followRedirect = true, $statusCallback = null, $readCallback = null) {
        set_time_limit(0);
        $url = parse_url($url);

        $scheme = "";
        $port = 80;

        if (isset($url['scheme']) && $url['scheme'] == "https") {
            $scheme = "ssl://";
            $port = 443;
        }

        if (isset($url['port']))
            $port = $url['port'];

        $fp = fsockopen($scheme . $url['host'], $port);
        if (!$fp)
            return false;

        $headers = Array(
            "GET " . $url['path'] . " HTTP/1.0",
            "Host: " . $url['host'],
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0",
            "Connection: Close",
            "Accept: application/vnd.github.v3+json"
        );

        fwrite($fp, implode("\r\n", $headers));
        fwrite($fp, "\r\n\r\n");

        $resultHeader = "";
        $resultContent = "";

        while (!feof($fp)) {
            $data = fgets($fp);

            if ($data == "\r\n")
                break;

            $resultHeader .= $data;
        }

        $resultHeader = explode("\r\n", $resultHeader);

        $httpVersion = "";
        $statusCode = 0;
        $statusMessage = "";

        if (isset($resultHeader[0])) {
            list($httpVersion, $statusCode, $statusMessage) = explode(" ", $resultHeader[0]);
            unset($resultHeader[0]);
        }

        $finalHeader = Array();
        foreach ($resultHeader as $head) {
            if ($head == "")
                continue;
            $head = explode(":", $head, 2);
            $finalHeader[$head[0]] = trim($head[1]);
        }

        if ($followRedirect && isset($finalHeader['Location'])) {
            $new_url = parse_url($finalHeader['Location']);

            if (!isset($new_url['host'])) {
                $new_url['host'] = $url['host'];
                $new_url['scheme'] = $url['scheme'];
            }

            return $this->GetFSock($new_url['scheme'] . "://" . $new_url['host'] . $new_url['path'], $followRedirect, $statusCallback, $readCallback);
        }

        $expectedLength = 0;
        if (isset($finalHeader['Content-Length']))
            $expectedLength = intval($finalHeader['Content-Length']);


        $bytesRead = 0;

        while (!feof($fp)) {
            $data = fread($fp, 128);
            $bytesRead += mb_strlen($data, '8bit');

            if ($readCallback == null)
                $resultContent .= $data;
            else
                call_user_func($readCallback, $data);

            if ($statusCallback != null)
                call_user_func($statusCallback, $expectedLength, $bytesRead);
        }
        fclose($fp);

        return Array('status' => $statusCode, 'header' => $finalHeader, 'content' => $resultContent);
    }

    public function Download($versionID) {
        $url = $this->GetURL($versionID);
        if ($url === false)
            return false;

        $this->tmpFileName = CWD . DS . "tmp" . DS . $versionID . ".zip";

        if (!file_exists($this->tmpFileName)) {
            $this->tmpFile = fopen($this->tmpFileName, "w+");

            $this->GetFSock($url, true, Array($this, 'Progress'), Array($this, 'ReadCallback'));
            fclose($this->tmpFile);
            $this->tmpFile = null;
        } else {
            $this->Progress(1, 1);
        }

        $fileName = $this->tmpFileName;
        $this->tmpFileName = null;

        return $fileName;
    }

    public function Unpack($zipFile) {
        set_time_limit(0);
        $zH = zip_open($zipFile);

        while ($file = zip_read($zH)) {
            $fileName = zip_entry_name($file);
            $fileName = substr($fileName, strpos($fileName, "/")+1);

            $fileDir = dirname($fileName);

            //Continue if its not a file
            if (substr($fileName, -1, 1) == '/')
                continue;

            if ($fileName == "inc/config.php" || $fileDir == "install")
                continue;

            if (!is_dir(CWD . $fileDir)) {
                if (mkdir(CWD . $fileDir, 0777, true))
                    $this->Write('<li>Created directory ' . $fileDir . '</li>');
                else {
                    $this->WriteError('<li>Could not create directory ' . $fileDir . '. Abort!</li>');
                    zip_close($zH);
                    return false;
                }
            }

            if (file_exists($fileName) && !is_writable($fileName)) {
                $this->WriteError('<li>Could not write file ' . $fileName . '. Not enougth permissions! Abort!</li>');
                return false;
            }

            $wrote = file_put_contents(CWD . $fileName, zip_entry_read($file, zip_entry_filesize($file)));
            if ($wrote === false) {
                $this->WriteError('<li>Could not write file ' . $fileName . '. Abort!</li>');
                zip_close($zH);
                return false;
            } else {
                $this->Write('<li>Wrote file ' . $fileName . '</li>');
            }
        }
        zip_close($zH);
        require_once(INC . "class.upgrade.php");
        Upgrade::Exec($this->version);
        return true;
    }

    private function Write($txt) {
        getVar('tpl')->Write($txt);
        getVar('tpl')->Flush();
    }

    private function WriteError($txt) {
        $this->Write('<font color="#ff0000">' . $txt . '</font>');
    }

    function __destruct() {
        if ($this->tmpFile != null) {
            fclose($this->tmpFile);
            if (file_exists($this->tmpFileName))
                unlink($this->tmpFileName);
        }
    }

    private function ReadCallback($data) {
        fwrite($this->tmpFile, $data);
    }

    private function Progress($download_size, $downloaded) {
        if ($download_size > 0) {
            getVar('tpl')->Write('<div>');
            getVar('tpl')->Write(round($downloaded / $download_size * 100, 2));
            getVar('tpl')->Write('</div>');
        }
        getVar('tpl')->Flush();
    }

}

?>