<?php
require_once(INC.'class.scope_data.php');

class Scope implements OAuth2_ScopeInterface {
    public function checkScope($required_scope, $available_scope) {
        $required_scope = explode(' ', trim($required_scope));
        $available_scope = explode(' ', trim($available_scope));
        return (count(array_diff($required_scope, $available_scope)) == 0);
    }

    public function getDefaultScope() {
        return ScopeData::BASIC;
    }

    public function getScopeFromRequest(OAuth2_RequestInterface $request) {
        // "scope" is valid if passed in either POST or QUERY
        $scope = $request->request('scope', $request->query('scope'));

        /*
         * "The authorization server MAY fully or partially ignore the scope requested by the client" 
         * (http://tools.ietf.org/html/rfc6749#section-3.3)
         */
        /*
         * Not working, would crash token.php;
         * TokenController.php Line 124 
         * $this->scopeUtil->checkScope($requestedScope, $availableScope) would be false
         */
        //$scope = $this->filterScope($scope);

        return $scope;
    }

    public function scopeExists($scopes, $client_id = null) {
        $scopes = explode(" ", $scopes);

        $sql = dbquery("SELECT scope FROM oauth_clients WHERE client_id = '".filter($client_id)."'");
        
        if($sql->count() != 1)
            return false;
        
        $scopesAvail = explode(" ", $sql->result());
        
        $type = Authorization::GetUserType('LDAP');
    
        if($type != null) {
            foreach(ScopeData::$scopesSpecial as $scope) {
                $key = array_search($scope, $scopesAvail);
                if($key !== false) 
                    unset($scopesAvail[$key]);
                    
            }
        }
        
        if($type == ReplacementsTypes::TEACHER) {
            foreach(ScopeData::$scopesTeacher as $scope) {
                $key = array_search($scope, $scopesAvail);
                if($key !== false) 
                    unset($scopesAvail[$key]);
                    
            }
        }

        return (count(array_diff($scopes, $scopesAvail)) == 0);
    }
/*
    private function filterScope($scope) {

        $scopes = explode(" ", $scope);

        if (!in_array(ScopeData::BASIC, $scopes))
            $scopes[] = ScopeData::BASIC;

        return implode(' ', $scopes);
    }*/
}

?>
