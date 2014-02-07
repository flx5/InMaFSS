<?php
// include our OAuth2 Server object
require_once __DIR__.'/server.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
if (!$server->verifyResourceRequest(OAuth2_Request::createFromGlobals(), new OAuth2_Response(), Scope::BASIC)) {
    $server->getResponse()->send();
    die;
}
echo json_encode(array('success' => true, 'message' => 'Hello World'));
?>