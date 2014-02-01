<?php
class Controller_Hello extends RestController {
        public function isAuthorized() {
            return true;
        }
    
        public function GET() {
                $this->response = array('TestResponse' => 'I am GET response. Variables sent are - ' . http_build_query($this->args));
                $this->responseStatus = 200;
        }
}
?>