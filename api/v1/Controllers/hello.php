<?php

class Controller_Hello extends RestController {

    public function GET() {
        $this->response = array('response' => 'Hello World!');
        $this->responseStatus = 200;
    }

    public function GetDescription() {
        return "A simple hello world endpoint";
    }
    
    public function RequireUser($method) {
        return false;
    }
}
?>