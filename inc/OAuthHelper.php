<?php

class OAuthHelper {

    private $store;
    private $server;
    
    public function __construct() {
        require_once(INC . "libs/oauth-php/library/OAuthServer.php");
        
        // Create a new instance of OAuthStore and OAuthServer
        $this->store = OAuthStore::instance(config("dbtype"), array('conn' => getVar("sql")->GetLink()));
        $this->server = new OAuthServer();
    }
    
    public function GetStore() {       
        return $this->store;
    }
    
    public function GetServer() {
        return $this->server;
    }

    public function isAuthorized() {
        
    }
}

?>
