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
<h2><?php lang()->loc('title'); ?></h2>
<?php
  if((!isset($_GET['id']) || !is_numeric($_GET['id'])) && !isset($_GET['new'])) {
          header("Location: users.php");
          exit;
  }

  $id = -1;

  if(!isset($_GET['new'])) {
    $id = filter($_GET['id']);
  }

  if(isset($_POST['name']) && isset($_POST['apikey']))  {
        $name = filter($_POST['name']);
        $apikey = filter($_POST['apikey']);
        $permissions = "replacements";

        $error = Array();

        if(strlen($name) < 3) {
              $error[] = lang()->loc('name.too.short',false);
        }

        if(strlen($apikey) < 12) {
              $error[] = lang()->loc('api.too.short',false);
        }

        if(count($error) == 0) {
          if(isset($_GET['new'])) {
              dbquery("INSERT INTO api (name, apikey, permissions) VALUES ('".$name."', '".$apikey."', '".$permissions."')");
              $_SESSION['api_add_success'] = true;
              header("Location: ?id=".mysql_insert_id());
              exit;
          }

          echo lang()->loc('saved');
          dbquery("UPDATE api SET name = '".$name."', apikey = '".$apikey."', permissions = '".$permissions."' WHERE id = ".$id);
        } else {
           foreach($error as $err) {
              echo $err.'<br>';
           }
        }
  }

  if(isset($_GET['new']) && !isset($_POST['caption'])) {
         $data = Array('name'=>'', 'apikey'=>GenerateAPI());
  }  else {
         $sql = dbquery("SELECT * FROM api WHERE id = ".$id);
         $data = mysql_fetch_assoc($sql);
  }

  if(isset($_SESSION['api_add_success']) && $_SESSION['api_add_success']) {
       lang()->loc('saved');
       $_SESSION['api_add_success'] = false;
  }

  function GenerateAPI() {
       $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
       $key = ""; 
       for($i = 0; $i < 12; $i++) {
             $key .= $chars[mt_rand(0, strlen($chars)-1)];
       }
       return $key;
  }
?>
<form method="post">
<table width="100%">
<tr><td><?php lang()->loc('name'); ?>:</td><td><input type="text" name="name" size="100"  value="<?php echo $data['name']; ?>"></td></tr>
<tr><td><?php lang()->loc('key'); ?>:</td><td><input type="text" name="apikey" size="100"  value="<?php echo $data['apikey']; ?>" readonly="readonly" style="background-color:#C0C0C0"></td></tr>
<tr><td></td><td><input type="submit" value="<?php lang()->loc('save'); ?>"></td></tr>
</table>
</form>
</div></div>