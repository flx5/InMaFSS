<?php

// include our OAuth2 Server object
require_once dirname(__FILE__) . '/server.php';

if (!Authorization::IsLoggedIn("LDAP")) {   
    $auth = Authorization::GenerateInstance("LDAP");
    /* @var $auth LDAP_Auth */

    if (isset($_POST['usr']) && isset($_POST['pwd'])) {
        $auth->Login($_POST['usr'], $_POST['pwd']);
    }

    if (!$auth->IsLoggedIn("LDAP")) {
        header("Location: ../user/?goto=oauth2/authorize.php".urlencode("?".  http_build_query($_GET)));
        exit;
    }
}

if(isset($_SESSION['auth_request'])) {
    unset($_SESSION['auth_request']); 
}

$request = OAuth2_Request::createFromGlobals();
$response = new OAuth2_Response();

$data = $server->validateAuthorizeRequest($request, $response);

// validate the authorize request
if ($data === false) {
    $response->send();
    die;
}
// display an authorization form
if (!isset($_POST['allow']) && !isset($_POST['forbid'])) {
    lang()->add('scopes');
    getVar('tpl')->Init(lang()->loc('authorize_title', false));
    getVar('tpl')->addCSS(WWW . '/oauth2/css/oauth.css');
    getVar('tpl')->AddTemplate('oauth/header');
    $tpl = getVar('tpl')->getTemplate('oauth/authorize');
    $tpl->setVar('data', $data);
    getVar('tpl')->addTemplateClass($tpl);
    getVar('tpl')->AddTemplate('oauth/footer');
    getVar('tpl')->Output();
    exit;
}

// print the authorization code if the user has authorized your client
$is_authorized = isset($_POST['allow']);
$server->handleAuthorizeRequest($request, $response, $is_authorized, Authorization::GetUserID('LDAP'));
$response->send();
?>