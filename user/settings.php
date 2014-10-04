<?php
require_once("global.php");
require_once(INC.'class.oauth.php');

if(Authorization::IsLoggedIn('LDAP') === null)
{
    header("Location: index.php");
    exit;
}

lang()->add('user');
lang()->add('scopes');


if(isset($_POST['delOauthID'])) {
    OAuthHelper::RemoveConsumerAccess($_POST['delOauthID'], Authorization::GetUserID('LDAP'));
    header("Location: settings.php");
    exit;
}

getVar('tpl')->Init(lang()->loc('title', false));
getVar('tpl')->AddStandards("user");
getVar('tpl')->addTemplate('user/header');
getVar('tpl')->addTemplate('user/settings');
getVar('tpl')->addTemplate('user/footer');
getVar('tpl')->Output();
?>
