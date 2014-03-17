<?php
class InMaFSS_OAuthResponse extends OAuth2_Response {
    public function getResponseBody($format = 'json')
    {
        $data = Array();
        
        if($this->statusCode != 200) {
            if(isset($this->parameters['error']) && isset($this->parameters['error_description']))
                $data['oauth_error'] = (object)Array('error'=>$this->parameters['error'], 'error_description'=>$this->parameters['error_description']);
            elseif($this->statusCode == 401)
                $data['oauth_error'] = (object)Array('error'=>'missing_authorization', 'error_description'=>'Auth header is missing');
            
        }
        
        switch ($format) {
            case 'json':
                return json_encode($data);
            case 'xml':
                // this only works for single-level arrays
                $xml = new SimpleXMLElement('<response/>');
                array_walk($data, array($xml, 'addChild'));
                return $xml->asXML();
        }

        throw new InvalidArgumentException(sprintf('The format %s is not supported'));

    }
}
?>
