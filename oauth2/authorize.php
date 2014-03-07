<?php

// include our OAuth2 Server object
require_once __DIR__ . '/server.php';

if(!isset($_SESSION['auth_request']))
    $request = OAuth2_Request::createFromGlobals();
else 
    $request = unserialize ($_SESSION['auth_request']);

if (!Authorization::IsLoggedIn("LDAP")) {
    
    $_SESSION['auth_request'] = serialize($request);
    
    $auth = Authorization::GenerateInstance("LDAP");
    /* @var $auth LDAP_Auth */

    if (isset($_POST['usr']) && isset($_POST['pwd'])) {
        $auth->Login($_POST['usr'], $_POST['pwd']);
    }

    if (!$auth->IsLoggedIn("LDAP")) {
        echo '<form method="post">';
        echo '<label for="usr">Username:</label><input name="usr" type="text">';
        echo '<label for="pwd">Password:</label><input name="pwd" type="password">';
        echo '<input type="submit" value="Login">';
        echo '</form>';
        exit;
    }
}


if(isset($_SESSION['auth_request']))
    unset($_SESSION['auth_request']);

$response = new OAuth2_Response();

// validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}
// display an authorization form
if (!isset($_POST['authorized'])) {
    exit('
<form method="post">
  <label>Do You Authorize TestClient?</label><br />
  <input type="submit" name="authorized" value="yes">
  <input type="submit" name="authorized" value="no">
</form>');
}

// print the authorization code if the user has authorized your client
$is_authorized = ($_POST['authorized'] === 'yes');
$server->handleAuthorizeRequest($request, $response, $is_authorized, USER_ID);
$response->send();
?>