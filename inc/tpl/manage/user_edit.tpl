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
<div class="round" style="width:90%; border:2px solid black; margin:5px auto; margin-top:20px; text-align:center;">
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

  if(isset($_POST['username']) && isset($_POST['pwd']))  {
        $username = filter($_POST['username']);
        $password = filter($_POST['pwd']);

        $error = Array();

        if(strlen($username) < 3) {
              $error[] = lang()->loc('name.too.short',false);
        }

        if(strlen($password) < 5) {
              $error[] = lang()->loc('pw.too.short',false);
        }

        if(count($error) == 0) {
          if(isset($_GET['new'])) {
              dbquery("INSERT INTO users (username, password) VALUES ('".$username."', '".getVar("core")->generatePW($username, $password)."')");
              $_SESSION['user_add_success'] = true;
              header("Location: ?id=".getVar("sql")->insertID());
              exit;
          }

          if(dbquery("SELECT username FROM users WHERE id = ".$id)->result() == USERNAME) {
                   $_SESSION['user'] = $username;
          }

          echo lang()->loc('saved');
          dbquery("UPDATE users SET username = '".$username."', password = '".getVar("core")->generatePW($username, $password)."' WHERE id = ".$id);
        } else {
           foreach($error as $err) {
              echo $err.'<br>';
           }
        }
  }

  if(isset($_GET['new']) && !isset($_POST['caption'])) {
         $data = Array('username'=>'', 'password'=>'');
  }  else {
         $sql = dbquery("SELECT * FROM users WHERE id = ".$id);
         $data = $sql->fetchAssoc();
  }

  if(isset($_SESSION['user_add_success']) && $_SESSION['user_add_success']) {
       lang()->loc('saved');
       $_SESSION['user_add_success'] = false;
  }

?>
<form method="post">
<table width="100%">
<tr><td><?php lang()->loc('name'); ?>:</td><td><input type="text" name="username" size="100"  value="<?php echo $data['username']; ?>"></td></tr>
<tr><td><?php lang()->loc('new.password'); ?>:</td><td><input type="password" name="pwd" size="100"  value=""></td></tr>
<tr><td></td><td><input type="submit" value="<?php lang()->loc('save'); ?>"></td></tr>
</table>
</form>
</div></div>
