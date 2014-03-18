<?php
class OAuth2_Storage_InMaFSS implements OAuth2_Storage_AuthorizationCodeInterface, OAuth2_Storage_AccessTokenInterface, OAuth2_Storage_ClientCredentialsInterface, OAuth2_Storage_UserCredentialsInterface, OAuth2_Storage_RefreshTokenInterface, OAuth2_Storage_JWTBearerInterface {

    protected $db;

    public function __construct($connection) {
        if (!$connection instanceof SQL) {
            throw new InvalidArgumentException('First argument to OAuth2_Storage_InMaFSS must be an instance of SQL');
        }
        $this->db = $connection;

        $this->config = array(
            'client_table' => 'oauth_clients',
            'access_token_table' => 'oauth_access_tokens',
            'refresh_token_table' => 'oauth_refresh_tokens',
            'code_table' => 'oauth_authorization_codes',
            'user_table' => 'oauth_users',
            'jwt_table' => 'oauth_jwt',
        );
    }

    private function filter($text) {
        return $this->db->real_escape_string($text);
    }
    
    /* OAuth2_Storage_ClientCredentialsInterface */

    public function checkClientCredentials($client_id, $client_secret = null) {
        $stmt = $this->db->DoQuery('SELECT * FROM '.$this->config['client_table'].' WHERE client_id = "'.$this->filter($client_id).'"');
        $result = $stmt->fetchAssoc();

        // make this extensible
        return $result['client_secret'] == $client_secret;
    }

    public function getClientDetails($client_id) {
        $stmt = $this->db->DoQuery(sprintf('SELECT * from %s where client_id = "'.$this->filter($client_id).'"', $this->config['client_table']));
        $data = $stmt->fetchAssoc();
        $data['grant_types'] = explode(' ', $data['grant_types']);
        return $data;
    }
 
    public function checkRestrictedGrantType($client_id, $grant_type) {
        /*
         * Available grant types: 
         * authorization_code, client_credentials, refresh_token, password, urn:ietf:params:oauth:grant-type:jwt-bearer
         * 
         * Setup and tested in InMaFSS: authorization_code, refresh_token, password, client_credentials
         */
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) { 
            return in_array($grant_type, (array) $details['grant_types']);
        }

        // if grant_types are not defined, then none are restricted
        return true;
    }

    /* OAuth2_Storage_AccessTokenInterface */

    public function getAccessToken($access_token) {
        $stmt = $this->db->DoQuery(sprintf('SELECT * from %s where access_token = "'.$this->filter($access_token).'"', $this->config['access_token_table']));
       
        $token = $stmt->fetchAssoc();
                
        if ($token) {
            // convert date string back to timestamp
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null) {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAccessToken($access_token)) {
            $stmt = $this->db->DoQuery(sprintf('UPDATE %s SET client_id="'.$this->filter($client_id).'", expires="'.$this->filter($expires).'", user_id="'.$this->filter($user_id).'", scope="'.$this->filter($scope).'" where access_token="'.$this->filter($access_token).'"', $this->config['access_token_table']));
        } else {
            $stmt = $this->db->DoQuery(sprintf('INSERT INTO %s (access_token, client_id, expires, user_id, scope) VALUES ("'.$this->filter($access_token).'", "'.$this->filter($client_id).'", "'.$this->filter($expires).'", "'.$this->filter($user_id).'", "'.$this->filter($scope).'")', $this->config['access_token_table']));
        }
        return ($this->db->affected_rows() == 1);
    }

    /* OAuth2_Storage_AuthorizationCodeInterface */

    public function getAuthorizationCode($code) {
        $stmt = $this->db->DoQuery(sprintf('SELECT * from %s where authorization_code = "'.$this->filter($code).'"', $this->config['code_table']));

        if ($code = $stmt->fetchAssoc()) {
            // convert date string back to timestamp
            $code['expires'] = strtotime($code['expires']);
        }

        return $code;
    }

    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null) {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            $stmt = $this->db->DoQuery(sprintf('UPDATE %s SET client_id="'.$this->filter($client_id).'", user_id="'.$this->filter($user_id).'", redirect_uri="'.$this->filter($redirect_uri).'", expires="'.$this->filter($expires).'", scope="'.$this->filter($scope).'" where authorization_code="'.$this->filter($code).'"', $this->config['code_table']));
        } else {
            $stmt = $this->db->DoQuery(sprintf('INSERT INTO %s (authorization_code, client_id, user_id, redirect_uri, expires, scope) VALUES ("'.$this->filter($code).'", "'.$this->filter($client_id).'", "'.$this->filter($user_id).'", "'.$this->filter($redirect_uri).'", "'.$this->filter($expires).'", "'.$this->filter($scope).'")', $this->config['code_table']));
        }
        return ($this->db->affected_rows() == 1);
    }

    public function expireAuthorizationCode($code) {
        $stmt = $this->db->DoQuery(sprintf('DELETE FROM %s WHERE authorization_code = "'.$this->filter($code).'"', $this->config['code_table']));

        return ($this->db->affected_rows() > 0);
    }

    /* OAuth2_Storage_UserCredentialsInterface */

    public function checkUserCredentials($username, $password) {
            return $this->checkPassword($username, $password);
    }

    public function getUserDetails($username) {
        return $this->getUser($username);
    }

    /* OAuth2_Storage_RefreshTokenInterface */

    public function getRefreshToken($refresh_token) {
        $stmt = $this->db->DoQuery(sprintf('SELECT * FROM %s WHERE refresh_token = "'.$this->filter($refresh_token).'"', $this->config['refresh_token_table']));

        if ($token = $stmt->fetchAssoc()) {
            // convert expires to epoch time
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null) {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        $stmt = $this->db->DoQuery(sprintf('INSERT INTO %s (refresh_token, client_id, user_id, expires, scope) VALUES ("'.$this->filter($refresh_token).'", "'.$this->filter($client_id).'", "'.$this->filter($user_id).'", "'.$this->filter($expires).'", "'.$this->filter($scope).'")', $this->config['refresh_token_table']));

        return ($this->db->affected_rows() == 1);
    }

    public function unsetRefreshToken($refresh_token) {
        $stmt = $this->db->DoQuery(sprintf('DELETE FROM %s WHERE refresh_token = "'.$this->filter($refresh_token).'"', $this->config['refresh_token_table']));

        return ($this->db->affected_rows() > 0);
    }

    protected function checkPassword($user, $password) {
        return Authorization::GenerateInstance("LDAP")->Login($user, $password);
    }

    public function getUser($username) {
         $user = Authorization::GenerateInstance("LDAP")->getUserDataByName($username);
         $user['user_id'] = $user['id'];
         return $user;
    }

    public function setUser($username, $password, $firstName = null, $lastName = null) {
        // Can't do this with LDAP
        return true;
    }

    /* OAuth2_Storage_JWTBearerInterface */

    public function getClientKey($client_id, $subject) {
        $stmt = $this->db->DoQuery(sprintf('SELECT public_key from %s where client_id="'.$this->filter($client_id).'" AND subject="'.$this->filter($subject).'"', $this->config['jwt_table']));
        return $stmt->fetch();
    }

}
