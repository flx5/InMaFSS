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
?>

<div class="content">
<div style="width:90%; border:2px solid black; margin:5px auto; margin-top:20px; text-align:center;">
<h2><?php lang()->loc('page'); ?></h2>
<?php
  if((!isset($_GET['id']) || !is_numeric($_GET['id'])) && !isset($_GET['new'])) {
          header("Location: pages.php");
          exit;
  }

  $id = -1;

  if(!isset($_GET['new'])) {
    $id = filter($_GET['id']);
  }

  if(isset($_POST['caption']) && isset($_POST['content']) && isset($_POST['time_from']) && isset($_POST['time_end']))  {
        $caption = filter($_POST['caption']);
        $content = filter($_POST['content']);
        $time_from = filter($_POST['time_from']);
        $time_end = filter($_POST['time_end']);

        $time_from = explode(".",$time_from);
        $time_end = explode(".",$time_end);

        $error = Array();

        if($caption == "") {
              $error[] = lang()->loc('caption.empty',false);
        }

        if($content == "") {
              $error[] = lang()->loc('content.empty',false);
        }

        $time = true;

        if(count($time_from) != 3) {
            $error[] = lang()->loc('err.startdate',false);
            $time = false;
        }

        if(count($time_end) != 3) {
            $error[] = lang()->loc('err.enddate',false);
            $time = false;
        }

        if($time) {

           $time_from = mktime(0,0,0, $time_from[1], $time_from[0], $time_from[2]);
           $time_end = mktime(23,59,59, $time_end[1], $time_end[0], $time_end[2]);

           if($time_from >= $time_end) {
                  $error[] = lang()->loc('err.end.before.start',false);
           }
        }

        if(count($error) == 0) {
          if(isset($_GET['new'])) {
              dbquery("INSERT INTO pages (title, content, timestamp_from, timestamp_end) VALUES ('".$caption."', '".$content."', '".$time_from."', '".$time_end."')");
              header("Location: ?id=".mysql_insert_id());
              exit;
          }
         dbquery("UPDATE pages SET title = '".$caption."', content = '".$content."', timestamp_from = '".$time_from."', timestamp_end = '".$time_end."' WHERE id = ".$id);
        } else {
           foreach($error as $err) {
              echo $err.'<br>';
           }
        }
  }
  $sql = dbquery("SELECT * FROM pages WHERE id = ".$id);
  $data = mysql_fetch_assoc($sql);
?>
<form method="post">
<table width="100%">
<tr><td><?php lang()->loc('caption'); ?>:</td><td><input type="text" name="caption" size="100"  value="<?php echo $data['title']; ?>"></td></tr>
<tr><td><?php lang()->loc('from'); ?>:</td><td><input type="text" style="" class="tcal" name="time_from" value="<?php echo date("d.m.Y", $data['timestamp_from']); ?>"></td></tr>
<tr><td><?php lang()->loc('until'); ?>:</td><td><input type="text" style="" class="tcal" name="time_end" value="<?php echo date("d.m.Y", $data['timestamp_end']); ?>"></td></tr>
<tr><td><?php lang()->loc('content'); ?>:</td><td><textarea name="content"><?php echo $data['content']; ?></textarea></td></tr>
<tr><td></td><td><input type="submit" value="<?php lang()->loc('save'); ?>"></td></tr>
</table>
</form>
</div></div>
