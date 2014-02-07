<?php
class Scope implements OAuth2_ScopeInterface {

    const BASIC = 'basic';
    const SUBSTITUTION_PLAN = 'substitutions';
    const SUBSTITUTION_PLAN_FULL = 'all_substitutions';
    const TEACHER_PLAN = 'teacher_plan';
    const TEACHER_PLAN_FULL = 'teacher_plan_full';
    
    private $scopes = Array(
        self::BASIC,
        self::SUBSTITUTION_PLAN,
        self::SUBSTITUTION_PLAN_FULL,
        self::TEACHER_PLAN,
        self::TEACHER_PLAN_FULL
    );

    public function checkScope($required_scope, $available_scope) { 
        $required_scope = explode(' ', trim($required_scope));
        $available_scope = explode(' ', trim($available_scope));
        return (count(array_diff($required_scope, $available_scope)) == 0);
    }

    public function getDefaultScope() {
        return self::BASIC;
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

    public function scopeExists($scope, $client_id = null) { 
        $scopes = explode(" ", $scope); 
        return (count(array_diff($scopes, $this->scopes)) == 0);
    }
    
    private function filterScope($scope) {
     
        $scopes = explode(" ", $scope);
      
        if(!in_array(self::BASIC, $scopes))
                $scopes[] = self::BASIC;
            
        return implode(' ', $scopes);
    }
}

?>
