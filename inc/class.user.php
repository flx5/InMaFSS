<?php
class User {
    public static function GeneratePW($username, $password) {
        return sha1(config("salt") . md5($password . config("salt") . $username));
    }
}
?>
