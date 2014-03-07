<?php

require_once("global.php");

lang()->add('user');

$error = '';

if (isset($_POST['usr']) && isset($_POST['pwd'])) {
    $auth = Authorization::GenerateInstance('LDAP');
    /* @var $auth LDAP_Auth */
    if($auth->Login($_POST['usr'], $_POST['pwd'])) {
        header("Location: index.php");
        exit;
    } else {
        $error = lang()->loc('wrong', false);
    }
}

getVar('tpl')->Init(lang()->loc('title', false));
getVar('tpl')->AddStandards("user");
getVar('tpl')->addTemplate('user/header');

if (Authorization::IsLoggedIn('LDAP') == null) {
    getVar('tpl')->setParam('error', $error);
    getVar('tpl')->addTemplate('user/login');
} else {
    getVar('tpl')->addTemplate('user/welcome');
}

getVar('tpl')->addTemplate('user/footer');
getVar('tpl')->Output();
?>