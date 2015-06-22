<?php
require_once 'global.php';
Authorization::GenerateInstance('LDAP')->Logout();
header('Location: index.php');
exit;
?>
