<?php

class Controller_Docs extends RestController
{

    const API_VERSION = '1.0.0';
    const SWAGGER_VERSION = '1.2';

    private $apis = Array();
    private $authorizations = Array(
        'oauth2' => Array(
            'type' => 'oauth2',
            'scopes' => Array(),
            'grantTypes' => Array()
        )
    );

    public function GetDescription() 
    {
        return "";
    }

    public function RequiresAuth($method) 
    {
        return false;
    }

    public function RequireUser($method) 
    {
        return false;
    }

    // Override here as we have to leave our default response format
    public function GetResponse() 
    {
        return $this->response;
    }

    private function LoadScopes() 
    {
        include_once INC . 'class.scope_data.php';
        $this->authorizations['oauth2']['scopes'];
        $scopes = ScopeData::GetScopes();
        lang()->add('scopes');
        foreach ($scopes as $scope) {
            $this->authorizations['oauth2']['scopes'][] = Array(
                'scope' => $scope,
                'description' => html_entity_decode(lang()->loc('scope_' . $scope, false, true), ENT_COMPAT, "UTF-8")
            );
        }
    }

    private function LoadGrantTypes() 
    {
        $this->authorizations['oauth2']['grantTypes'] = Array(
            'implicit' => Array(
                'loginEndpoint' => Array(
                    'url' => WWW . '/oauth2/authorize.php'
                ),
                "tokenName" => "access_token"
            ),
            'authorization_code' => Array(
                'tokenRequestEndpoint' => Array(
                    'url' => WWW . '/oauth2/authorize.php',
                    'clientIdName' => 'client_id',
                    'clientSecretName' => 'client_secret'
                ),
                'tokenEndpoint' => Array(
                    'url' => WWW . '/oauth2/token.php',
                    'tokenName' => 'access_token'
                )
            )
                // Further auth-modes not supported by Swagger-doc
                // https://github.com/wordnik/swagger-spec/blob/master/versions/1.2.md#517-grant-types-object
        );
    }

    private function LoadRessources() 
    {
        if ($handle = opendir(dirname(__FILE__))) {
            while (false !== ($file = readdir($handle))) {
                if ($file == "." || $file == ".." || $file == "docs.php") {
                    continue; 
                }

                if (strlen($file) <= 4 || substr($file, -4) != ".php") {
                    continue; 
                }

                include_once $file;
                $className = substr($file, 0, -4);
                $className[0] = strtoupper($className[0]);
                $reflection = new ReflectionClass('Controller_' . $className);
                $controller = $reflection->newInstance(null, null, null);
                $path = "/" . substr($file, 0, -4);
                $this->apis[] = Array('path' => $path, 'description' => $controller->GetDescription());
            }
            closedir($handle);
        }
    }

    public function RequiresVerb() 
    {
        return true;
    }

    public function GET() 
    {
        if ($this->verb == null) {
            $this->GetIndex(); 
        }
        else {
            $this->GetRessourceDesc(); 
        }
    }

    private function GetRessourceDesc() 
    {
        $preg = preg_replace("/([a-z]|_)/", "", $this->verb);
        if ($preg != "" || !file_exists(dirname(__FILE__) . '/' . $this->verb . '.php')) {
            $this->Get404(); 
        }

        include_once dirname(__FILE__) . '/' . $this->verb . '.php';

        $this->verb = strtoupper($this->verb);
        $reflection = new ReflectionClass('Controller_' . $this->verb);
        $controller = $reflection->newInstance(null, null, null);

        $this->response = Array(
            'apiVersion' => self::API_VERSION,
            'swaggerVersion' => self::SWAGGER_VERSION,
            'basePath' => WWW . '/api/v1/',
            'resourcePath'=>'/'.strtolower($this->verb),
            'apis' => $this->apis,
        );
    }

    private function Get404() 
    {
        $this->responseStatus = HTTPStatus::_NOT_FOUND;
    }

    private function GetIndex() 
    {
        $this->LoadScopes();
        $this->LoadGrantTypes();
        $this->LoadRessources();

        $this->response = Array(
            'apiVersion' => self::API_VERSION,
            'swaggerVersion' => self::SWAGGER_VERSION,
            'apis' => $this->apis,
            'authorizations' => $this->authorizations
        );
    }

}

?>
