<?php

class Controller_Hello extends RestController {

    public function GET() {
        $this->response = array('response' => 'Hello World!');
        $this->responseStatus = 200;
    }

    public function RequireUser($method) {
        return false;
    }
}
?>