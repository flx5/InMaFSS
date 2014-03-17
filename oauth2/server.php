<?php 
require_once(realpath(dirname(__FILE__))."/../global.php");
require_once(INC.'libs/oauth2-server/src/OAuth2/Autoloader.php');

// Register Autoloader before loading Storage!
OAuth2_Autoloader::register();

require_once (INC.'libs/Oauth2Storage.php');
require_once(INC.'class.scope.php');

$storage = new OAuth2_Storage_InMaFSS(getVar("sql"));
// Overwriting the token_bearer_header_name as our test client only sends OAuth2 as Auth-Header and not the specified one
$server = new OAuth2_Server($storage, Array('token_bearer_header_name'=>'OAuth2'));
$server->addGrantType(new OAuth2_GrantType_ClientCredentials($storage));
$server->addGrantType(new OAuth2_GrantType_AuthorizationCode($storage));
$server->addGrantType(new OAuth2_GrantType_UserCredentials($storage));
$server->addGrantType(new OAuth2_GrantType_RefreshToken($storage));

$scopeUtil = new Scope();
$server->setScopeUtil($scopeUtil);
?>