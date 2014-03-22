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

class core {

    public static function GetVersion() {
        if (file_exists(INC . "version.php")) {
            include(INC . "version.php");
            if (isset($version)) {
                return $version;
            }
        }

        return null;
    }

    public static function CompareVersion($oldVersion, $newVersion) {
        $oldVersion = self::ParseVersionString($oldVersion);
        $newVersion = self::ParseVersionString($newVersion);

        if ($newVersion['major'] > $oldVersion['major'])
            return 1;
        if ($newVersion['major'] < $oldVersion['major'])
            return -1;

        if ($newVersion['minor'] > $oldVersion['minor'])
            return 1;
        if ($newVersion['minor'] < $oldVersion['minor'])
            return -1;

        if ($newVersion['revision'] > $oldVersion['revision'])
            return 1;
        if ($newVersion['revision'] < $oldVersion['revision'])
            return -1;

        switch ($newVersion['type']) {
            case 'STABLE':
                if ($oldVersion['type'] != 'STABLE')
                    return 1;
                break;
            case 'PREALPHA':
                break;
            case 'ALPHA':
                switch ($oldVersion['type']) {
                    case 'RC':
                    case 'BETA':
                    case 'STABLE':
                        return -1;
                    case 'PREALPHA':
                        return 1;
                }
                break;
            case 'BETA':
                switch ($oldVersion['type']) {
                    case 'RC':
                    case 'STABLE':
                        return -1;
                    case 'PREALPHA':
                    case 'ALPHA':
                        return 1;
                }
                break;
            case 'RC':
                switch ($oldVersion['type']) {
                    case 'STABLE':
                        return -1;
                    case 'BETA':
                    case 'PREALPHA':
                    case 'ALPHA':
                        return 1;
                }
                break;
        }

        if ($newVersion['typeNo'] == $oldVersion['typeNo'])
            return 0;

        return ($newVersion['typeNo'] > $oldVersion['typeNo']) ? 1 : -1;
    }

    public static function ParseVersionString($version) {
        $version = explode("-", $version, 2);

        $prerelease = "stable";

        if (isset($version[1]))
            $prerelease = $version[1];

        $version = explode(".", $version[0]);

        $majorVersion = $version[0];
        if (strpos($majorVersion, "v") === 0)
            $majorVersion = substr($majorVersion, 1);

        $minorVersion = 0;
        if (isset($version[1]))
            $minorVersion = $version[1];

        $revision = 0;
        if (isset($version[2]))
            $revision = $version[2];

        $prerelease = explode(".", $prerelease, 2);
        
        $preReleaseVersion = 0;
        if (isset($prerelease[1]))
            $preReleaseVersion = $prerelease[1];

        $prerelease = $prerelease[0];

        return Array('major' => $majorVersion, 'minor' => $minorVersion, 'revision' => $revision, 'type' => strtoupper($prerelease), 'typeNo' => $preReleaseVersion);
    }

    public static function GetIP() {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

            if (strpos($ip, ',') !== false) {
                $ip = substr($ip, 0, strpos($ip, ","));
                $ip = trim($ip);
            }

            return $ip;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];

        if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];

        if (isset($_SERVER['HTTP_FORWARDED_FOR']) && !empty($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];

        if (isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];

        return $_SERVER['REMOTE_ADDR'];
    }

    public static function GetDay($timestamp) {
        $engDay = gmdate("D", $timestamp);
        $engDay = strtolower(substr($engDay, 0, 2));
        lang()->add('date');
        $localDay = lang()->loc($engDay, false);
        return $localDay;
    }

    public static function SystemError($title, $text) {

        if (!in_array("Content-Type: text/html", headers_list())) {
            echo $title . " " . $text;
            return;
        }

        echo '<div style="width: 80%; padding: 15px 15px 15px 15px; margin: 50px auto; background-color: #F6CECE; font-family: arial; font-size: 12px; color: #000000; border: 1px solid #FF0000;">';
        echo '<img src="' . WWW . '/images/exclamation.png" style="float: left;" title="Error">&nbsp;';
        echo '<b>' . $title . '</b><br />';
        echo '&nbsp;' . $text;
        echo '<hr size="1" style="width: 100%; margin: 15px 0px 15px 0px;" />';
        echo 'Script execution was aborted. We apoligize for the possible inconvenience. If this problem is persistant, please contact an Administrator.';
        echo '</div>';
        exit;
    }

    public static function SuccessMessage($text) {
        echo '<div class="status_ok">' . $text . '</div>';
    }

    public function generatePW($username, $password) {
        return sha1(config("salt") . md5($password . config("salt") . $username));
    }

    public function filter($input) {
        if (ini_get("magic_quotes_gpc"))
            $input = stripslashes($input);

        return getVar("sql")->real_escape_string($input);
    }

    public static function MagicQuotesCompability() {
        if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
            $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
            while (list($key, $val) = each($process)) {
                foreach ($val as $k => $v) {
                    unset($process[$key][$k]);
                    if (is_array($v)) {
                        $process[$key][stripslashes($k)] = $v;
                        $process[] = &$process[$key][stripslashes($k)];
                    } else {
                        $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                }
            }
            unset($process);
        }
    }

    public static function UploadCodeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_OK:
                return null;
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    public static function String2Grade($gradeString) {
        $grade = Array('prefix' => '', 'num' => '', 'suffix' => '');

        for ($i = 0; $i < strlen($gradeString); $i++) {
            if ($grade['suffix'] == '' && is_numeric($gradeString[$i])) {
                $grade['num'] .= $gradeString[$i];
            } else {
                if ($grade['num'] == "") {
                    $grade['prefix'] .= $gradeString[$i];
                } else {
                    $grade['suffix'] .= $gradeString[$i];
                }
            }
        }

        $grade['num'] = (int) $grade['num'];
        return $grade;
    }

    function FormatJson($json) {

        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '  ';
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

}

?>