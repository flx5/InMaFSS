<div class="login">
    <h2><?php lang()->loc('title'); ?></h2>
    %error%
    <form method="post" action="">
        <table style="margin:auto;">
            <tr>
                <td><label for="usr"><?php lang()->loc('username'); ?>:</label></td><td><input type="text" id="usr" name="usr"></td>
            </tr>
            <tr>
                <td><label for="pwd"><?php lang()->loc('password'); ?>:</label></td><td><input type="password" id="pwd" name="pwd"></td>
            </tr>
        </table>
        <br><br>
        <input type="submit" value="<?php lang()->loc('login'); ?>" style="background-color:#C0C0C0; border:1px solid #999999; width:200px; height:50px; font-size:2em;">
    </form>
</div>
