<?php
/*=================================================================================*\
|* This file is part of InMaFSS                                                    *|
|* InMaFSS - INformation MAnagement for School Systems - Keep yourself up to date! *|
|* ############################################################################### *|
|* Copyright (C) flx5                                                              *|
|* E-Mail: me@flx5.com                                                             *|
|* ############################################################################### *|
|* InMaFSS is free software; you can redistribute it and/or modify                 *|
|* it under the terms of the GNU Affero General Public License as published by     *|
|* the Free Software Foundation; either version 3 of the License,                  *|
|* or (at your option) any later version.                                          *|
|* ############################################################################### *|
|* InMaFSS is distributed in the hope that it will be useful,                      *|
|* but WITHOUT ANY WARRANTY; without even the implied warranty of                  *|
|* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                            *|
|* See the GNU Affero General Public License for more details.                     *|
|* ############################################################################### *|
|* You should have received a copy of the GNU Affero General Public License        *|
|* along with InMaFSS; if not, see http://www.gnu.org/licenses/.                   *|
\*=================================================================================*/

require_once 'global.php';

if(!LOGGED_IN) {
    echo "REQUIRE_LOGIN";
    exit;
}

getVar('tpl')->Init("TEST");

try
{
    // Check if there is a valid request token in the current request
    // Returns an array with the consumer key, consumer secret, token, token secret and token type.
    $rs = $server->authorizeVerify();

    if (isset($_POST['allow']))
    {
        // See if the user clicked the 'allow' submit button (or whatever you choose)
        $authorized = true;

        // Set the request token to be authorized or not authorized
        // When there was a oauth_callback then this will redirect to the consumer
        $server->authorizeFinish($authorized, USER_ID);

        // No oauth_callback, show the user the result of the authorization
        // ** your code here **
   }
}
catch (OAuthException2 $e)
{
    echo $e->getMessage();
    // No token to be verified in the request, show a page where the user can enter the token to be verified
    // **your code here**
}

getVar('tpl')->AddTemplate('oauth/authorize');
getVar('tpl')->Output();
?>