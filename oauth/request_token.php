<?php
require_once 'global.php';
$server = new OAuthServer();
$token = $server->requestToken();
echo $token;
?>