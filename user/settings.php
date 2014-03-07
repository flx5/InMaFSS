<?php
require_once("global.php");
require_once(CWD."oauth2".DS."server.php");

if(Authorization::IsLoggedIn('LDAP') === null)
{
    header("Location: index.php");
    exit;
}

lang()->add('user');

getVar('tpl')->Init(lang()->loc('title', false));
getVar('tpl')->AddStandards("user");
getVar('tpl')->addTemplate('user/header');
$tmpl = getVar('tpl')->getTemplate('user/settings');
$tmpl->SetVar('storage', $storage);
getVar('tpl')->AddTemplateClass($tmpl);
getVar('tpl')->addTemplate('user/footer');
getVar('tpl')->Output();
?>
