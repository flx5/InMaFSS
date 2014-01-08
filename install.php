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


   if(!isset($_GET['step']) || !is_numeric($_GET['step'])) {
       $step = 1;
   }  else {
       $step =  intval($_GET['step']);
   }

   $next = 'Next';
   $lang = '';
   $continue = true;
   $end = false;

   if(isset($_REQUEST['lang']) && strlen($_REQUEST['lang']) == 2) {
         $lang = $_REQUEST['lang'];
         if(!file_exists("./inc/lang/".$lang.".php")) {
                header("Location: install.php?step=1");
                exit;
         }
         $key = 'install';
         include("./inc/lang/".$lang.".php");
         $next = $loc['next'];
   }

   $title = '&nbsp;';
   $size = 300;
   switch($step) {
       case 1:
          $title = 'Choose your language';
       break;
       case 2:
          $title = $loc['license'];
          $size = 755;
          $next = $loc['accept'];
       break;
       case 3:
       case 4:
       case 5:
           $title = $loc['db.settings'];
       break;
   }

   echo '<div style="width:70%; margin:0px auto 0px;  background-color:#0000FF; text-align:center; color:#fff;"><h1 style="margin:0px">'.$title.'</h1></div>';
   echo '<div style="height:'.$size.'px; width:70%; margin:0px auto 0px; background-color:#00CECE; text-align:center;">';
   echo '<form method="post" action="install.php?step='.($step+1).'&lang='.$lang.'">&nbsp;';

   getPage();

   echo '<div style="position:absolute; width:68%; top:'.$size.'px; border-top:2px solid black; padding:5px;">';

   if($step != 1) {
      echo '<div style="float:left; margin-bottom:2px;"><a href="install.php?step='.($step-1).'&lang='.$lang.'">'.$loc['back'].'</a></div>';
   }

   if($continue && !$end) {
      echo '<div style="float:right;"><input type="submit" value="'.$next.'"></div>';
   } elseif($end) {
      echo '<div style="float:right;"><a href="./index.php">'.$loc['finish'].'</a></div>';
   }

   echo '</form></div></div>';


function getPage() {
   global $step, $loc, $continue, $end;
   switch($step) {
        case 1:
            echo '<select name="lang">';
                    if ($fh = opendir("./inc/lang/")) {
                       while (($file = readdir($fh)) !== false) {
                            if(!is_dir($file)) {
                                  include("./inc/lang/".$file);
                                  $lang = $info['language'];

                                  echo '<option value="'.substr($file,0,-4).'" >'.$lang.'</option>';
                            }
                       }
                    closedir($fh);
                    }
           echo '</select>&nbsp;';
        break;

        case 2:
           echo '<div style="width:90%; background-color:#fff; margin:auto; height:360px; overflow:auto;"><code>';
             echo nl2br(preg_replace("/ /", "&nbsp;", file_get_contents("README.txt")));
           echo '</code></div><br>';
           echo '<div style="width:90%; background-color:#fff; margin:auto; height:300px; overflow:auto;"><code>';
             echo nl2br(preg_replace('/</', "&lt;" ,file_get_contents("LICENSE.txt")));
           echo '</code></div>';
        break;

        case 3:
           echo '<table  align="center" cellspacing="7px;" >';
           echo '<tr><td>'.$loc['dbhost'].':</td><td><input type="text" value="localhost" name="host"></td></tr>';
           echo '<tr><td>'.$loc['username'].':</td><td><input type="text" value="root" name="username"></td></tr>';
           echo '<tr><td>'.$loc['password'].':</td><td><input type="password" value="" name="password"></td></tr>';
           echo '<tr><td>'.$loc['database'].':</td><td><input type="text" value="inmafss" name="database"></td></tr>';
           echo '</table>';

        break;

        case 4:
           if(!isset($_POST['host']) || !isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['database'])) {
                header("Location: install.php?step=".($step-1).'&lang='.$lang);
                exit;
           }
           $link = @mysql_connect($_POST['host'], $_POST['username'], $_POST['password']);
           if(!$link) {
                echo $loc['db.connect.err'].': '.mysql_error();
                $continue = false;
                return;
           }

           $db = @mysql_select_db($_POST['database']);
           if(!$db) {
                 echo $loc['db.select.err'].': '.mysql_error();
                 $continue = false;
                 return;
           }
           global $lang;
           file_put_contents("./inc/config.php",getConfig($_POST['host'], $_POST['username'], $_POST['password'], $_POST['database'], $lang));
           echo $loc['config.saved'];

        break;
        case 5:
           if(!file_exists("./inc/config.php")) {
               header("Location = install.php?step=3");
               exit;
           }

           include("./inc/config.php");

           $link = @mysql_connect($dbhost, $dbusr, $dbpass);
           if(!$link) {
                echo $loc['db.connect.err'].': '.mysql_error();
                $continue = false;
                return;
           }

           $db = @mysql_select_db($dbname);
           if(!$db) {
                 echo $loc['db.select.err'].': '.mysql_error();
                 $continue = false;
                 return;
           }

           foreach(getSql() as $cmd) {
                 mysql_query($cmd);
           }

           $tomorrow = mktime(date("H"), date("i"), date("s"), date("m"), date("d")+1);

           mysql_query("INSERT INTO pages (order_num, title, content, timestamp_from, timestamp_end) VALUES ('1', '".$loc['welcome']."', '".$loc['firstpage']."', '".time()."', '".$tomorrow."')");

           echo '<b>'.$loc['db.set.up'].'</b>';

        break;

        case 6:
           echo $loc['create.admin'].'<br>';
           echo '<table  align="center" cellspacing="7px;" >';
           echo '<tr><td>'.$loc['username'].':</td><td><input type="text" value="Admin" name="username"></td></tr>';
           echo '<tr><td>'.$loc['password'].':</td><td><input type="password" value="" name="password"></td></tr>';
           echo '</table>';
        break;

        case 7:
           if(!isset($_POST['username']) || !isset($_POST['password'])) {
               header("Location = install.php?step=6");
               exit;
           }

           if(!file_exists("./inc/config.php")) {
               header("Location = install.php?step=3");
               exit;
           }

           include("./inc/config.php");

           $link = @mysql_connect($dbhost, $dbusr, $dbpass);
           if(!$link) {
                echo $loc['db.connect.err'].': '.mysql_error();
                $continue = false;
                return;
           }

           $db = @mysql_select_db($dbname);
           if(!$db) {
                 echo $loc['db.select.err'].': '.mysql_error();
                 $continue = false;
                 return;
           }

           $pw = sha1($salt.md5($_POST['password'].$salt.$_POST['username']));
           mysql_query("INSERT INTO users (username, password) VALUES ('".$_POST['username']."', '".$pw."')");
           echo $loc['acc.created'];
        break;

        case 8:
            echo '<h1>'.$loc['finished'].'</h1>';
            $end = true;
        break;
   }
}

function getSql() {
return Array('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";',
'SET time_zone = "+00:00";',

"CREATE TABLE IF NOT EXISTS `others` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('t','g','r','s','a','n') NOT NULL,
  `name` varchar(100) NOT NULL,
  `lesson` varchar(100) NOT NULL,
  `comment` varchar(100) NOT NULL,
  `timestamp` int(20) NOT NULL,
  `timestamp_update` int(20) NOT NULL,
  `addition` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;",

"CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_num` int(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `timestamp_from` int(20) NOT NULL,
  `timestamp_end` int(10) NOT NULL,
  `pupils` tinyint(1) NOT NULL DEFAULT '1',
  `teachers` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;",

"CREATE TABLE IF NOT EXISTS `plugins` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `val1` varchar(200) NOT NULL,
  `val2` varchar(200) NOT NULL,
  `plugin` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

"CREATE TABLE IF NOT EXISTS `replacements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `grade_pre` varchar(200) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `grade_last` varchar(10) NOT NULL,
  `lesson` varchar(10) NOT NULL,
  `teacher` varchar(200) NOT NULL,
  `replacement` varchar(200) NOT NULL,
  `room` varchar(10) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `timestamp_update` int(10) NOT NULL,
  `addition` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;",

"CREATE TABLE IF NOT EXISTS `teacher_substitude` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `short` varchar(200) NOT NULL,
  `teacher` varchar(200) NOT NULL,
  `lesson` varchar(100) NOT NULL,
  `grade` varchar(20) NOT NULL,
  `room` varchar(10) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `timestamp_update` int(10) NOT NULL,
  `addition` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;",

"CREATE TABLE IF NOT EXISTS `ticker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `automatic` tinyint(1) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  `from_stamp` varchar(20) NOT NULL,
  `to_stamp` varchar(20) NOT NULL,
  `order` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;",

"CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;"
);
}

function getConfig($dbhost, $usr, $passwd, $db, $lang) {

$signs = Array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','?','(',')',',','!');

$salt = "";
$key = "";

for($i = 0; $i<20; $i++) {
       $salt .= $signs[rand(0,count($signs)-1)];
       $key .= $signs[rand(0,count($signs)-1)];
}
$config =
'<?php
$dbhost = \''.$dbhost.'\';
$dbusr = \''.$usr.'\';
$dbpass = \''.$passwd.'\';
$dbname = \''.$db.'\';

$salt = \''.$salt.'\';
$apikey = \''.$key.'\';

$system = \'willi2\';
$lang = \''.$lang.'\';
$auto_addition = false;
$time_for_next_page = 5; // time to go to next page (in seconds)
$teacher_time_for_next_page = 5; // time to go to next page (in seconds)

$use_ftp = false;
$ftp[\'server\'] = \'localhost\';
$ftp[\'usr\'] = \'ftp_user\';
$ftp[\'pwd\'] = \'ftp_password\';
$ftp[\'path\'] = \'/\';
?>';
return $config;
}
?>