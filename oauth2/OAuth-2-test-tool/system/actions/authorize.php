<?php

$params = array();
$params['client_id'] = $session->client_id;
$params['response_type'] = 'code';
$params['redirect_uri'] = $session->callback_url;
$params['state'] = md5(time());
$params['scope'] = 'basic substitutions';

// save state
$session->state = $params['state'];

redirect($session->url_authorize, $params);