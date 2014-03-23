<?php
// include our OAuth2 Server object
require_once dirname(__FILE__).'/server.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
$response = new OAuth2_Response();
$server->handleTokenRequest(OAuth2_Request::createFromGlobals(), $response)->send();
?>