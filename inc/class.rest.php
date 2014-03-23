<?php

require_once(realpath(dirname(__FILE__)) . "/class.HTTPStatus.php");
require_once(realpath(dirname(__FILE__)) . "/../oauth2/server.php");
require_once(INC . "libs/Array2XML.php");
require_once(INC . "class.api.php");
require_once(INC . "libs/OAuthResponse.php");
require_once(INC. "class.rest_util.php");

class Rest {

    const FORMAT_JSON = "json";
    const FORMAT_XML = "xml";

    /**
     * The default return type
     */
    const DEFAULT_RESPONSE_FORMAT = self::FORMAT_JSON;

    /**
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     * 
     * @access protected
     * @var string
     */
    protected $method = '';

    /**
     * The Model requested in the URI. eg: /files
     * 
     * @access protected
     * @var string
     */
    protected $endpoint = '';

    /**
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     * 
     * @access protected
     * @var string
     */
    protected $verb = '';

    /**
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     * 
     * @access protected
     * @var array
     */
    protected $args = Array();

    /**
     * Stores the input of the PUT request
     * 
     * @access protected
     * @var array
     */
    protected $file = Null;

    /**
     * Stores the path to the directory containing the controllers
     * 
     * @var string 
     */
    protected $controller_dir = '';
    private $format = self::DEFAULT_RESPONSE_FORMAT;

    /**
     * Allow for CORS, assemble and pre-process the data
     * 
     * @param string The redirected part of the url (eg. /files)
     * @param string The path to the directory containing the controllers
     */
    public function __construct($request, $controller_dir) {
        $this->controller_dir = rtrim($controller_dir, "/\\");

        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);

        if ($this->method == 'POST') {
            $x_http_method = null;

            /*
             * HTTP METHOD OVERRIDE AS SPECIFIED BY MICROSOFT 
             * http://msdn.microsoft.com/en-us/library/dd541471.aspx
             */
            if (array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
                $x_http_method = $_SERVER['HTTP_X_HTTP_METHOD'];

            /*
             * HTTP METHOD OVERRIDE AS SPECIFIED BY GOOGLE 
             * https://developers.google.com/gdata/docs/2.0/basics?hl=de&csw=1#UpdatingEntry
             */
            if (array_key_exists('HTTP_X_HTTP_METHOD_OVERRIDE', $_SERVER))
                $x_http_method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];

            if ($x_http_method != null) {
                switch ($x_http_method) {
                    case 'DELETE':
                    case 'PUT':
                        $this->method = $x_http_method;
                        break;
                    default:
                        $this->Error(APIErrorCodes::HTTP_X_METHOD_INVALID);
                }
            }
        }

        switch ($this->method) {
            case 'DELETE':
            case 'POST':
                $this->request = $_POST;
                break;
            case 'GET':
                $this->request = $_GET;
                break;
            case 'PUT':
                $this->request = $_GET;
                $this->file = file_get_contents("php://input");
                break;
            default:
                $this->Error(APIErrorCodes::HTTP_METHOD_NOT_ALLOWED);
                break;
        }

        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = strtolower(array_shift($this->args));

        $pointPos = strrpos($this->endpoint, ".");
        if ($pointPos !== false) {
            $extension = substr($this->endpoint, $pointPos + 1);

            $known_ext = false;

            switch ($extension) {
                case self::FORMAT_JSON:
                case self::FORMAT_XML:
                    $this->format = $extension;
                    $this->endpoint = substr($this->endpoint, 0, $pointPos);
                    break;
            }
        }

        $this->LoadEndpoint();
    }

    private function Error($errorCode) {
        $this->Response(Array('errors' => APIErrorCodes::GetError($errorCode)), APIErrorCodes::GetStatus($errorCode));
    }

    private function LoadEndpoint() {
        // Ensure that the endpoint doesn't contain any special chars that could harm the filesystem
        $this->endpoint = str_replace(".", "_", $this->endpoint);
        $preg = preg_replace("/([a-z]|_)/", "", $this->endpoint);
        if ($preg !== "")
            $this->Error(APIErrorCodes::INVALID_ENDPOINT);

        if ($this->endpoint == "")
            $this->Error(APIErrorCodes::INVALID_ENDPOINT);

        $file = $this->controller_dir . "/" . $this->endpoint . ".php";

        if (!file_exists($file))
            $this->Error(APIErrorCodes::INVALID_ENDPOINT);

        require_once($file);

        $this->endpoint[0] = strtoupper($this->endpoint[0]);

        $reflection = new ReflectionClass('Controller_' . $this->endpoint);

        $user = $this->GetUserID(); 
        
        $controller = $reflection->newInstance($this->args, $this->GetUserID(), $this->file);
        /* @var $controller RestController */

        if($controller->RequireUser($this->method) && (empty($user) || $user == null))
        {
            $this->Error(APIErrorCodes::OAUTH_MISSING_USER);
        }
    
        if ($controller->RequiresVerb()) {
            if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
                $this->verb = array_shift($this->args);
            }
        }

        if (!$this->isAuthorized($controller->RequiredScope($this->method))) {
            $this->Error(APIErrorCodes::OAUTH_UNAUTHORIZED);
        }

        $reflection->getMethod($this->method)->invoke($controller);

        $this->Response($controller->GetResponse(), $controller->GetStatus());
    }

    private function GetUserID() { 
        global $server; 
        $token = $server->getAccessTokenData(OAuth2_Request::createFromGlobals(), new OAuth2_Response()); 
        if($token == null)
            return null;
        
        return $token['user_id'];
    }
    
    private function isAuthorized($scope = null) {
        return $this->AuthorizeOAUTH2 ($scope);
    }
    
    private function AuthorizeOAUTH2($scope = null) {
        if($scope == null)
            $scope = Scope::BASIC;
        
        global $server;

        if (!$server->verifyResourceRequest(OAuth2_Request::createFromGlobals(), new InMaFSS_OAuthResponse(), $scope)) {

            if ($this->format != 'json' && $this->format != 'xml')
                $this->format = 'json';

            $server->getResponse()->send($this->format);
            die();
        }

        return true;
    }
    
    protected function Response($data, $status) {
        $this->CORS();
        header(HTTPStatus::GetHeader($status));

        switch ($this->format) {
            case self::FORMAT_JSON:
                header("Content-Type: application/json");
                $data = json_encode($data);
                break;
            case self::FORMAT_XML:
                header("Content-Type: text/xml");
                $xml = Array2XML::createXML('result', $data);
                $data = $xml->saveXML();
                break;
        }
        echo $data;
        exit;
    }

    /**
     * Send Cross-Origin Resource Sharing Headers
     */
    protected function CORS() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, DELETE");
        header("Allow: HEAD, GET, POST, PUT, DELETE");
    }

}

abstract class RestController {

    protected $args;
    protected $response;
    protected $responseStatus;
    protected $errors;
    protected $file;
    protected $user;
    protected $meta;

    public function __construct($args, $user, $file) {
        $this->args = $args;
        $this->response = Array();
        $this->responseStatus = HTTPStatus::_OK;
        $this->errors = Array();
        $this->file = $file; 
        $this->user = $user;
        $this->meta = Array();
    }

    public final function GetResponse() {
        return Array('meta'=> $this->meta,'response' => $this->response, 'errors' => $this->errors);
    }

    public final function GetStatus() {
        return $this->responseStatus;
    }

    public function RequiresVerb() {
        return false;
    }

    protected final function AddError($errorCode) {
        $this->errors[] = APIErrorCodes::GetError($errorCode);
        $this->responseStatus = APIErrorCodes::GetStatus($errorCode);
    }

    protected function UnsupportedHTTPMethod() {
        $this->responseStatus = HTTPStatus::_METHOD_NOT_ALLOWED;
        $this->AddError(APIErrorCodes::HTTP_METHOD_NOT_ALLOWED);
    }

    public function RequiredScope($method) {
        return ScopeData::BASIC;
    }
    
    public function RequireUser($method) {
        return true;
    }

    public function GET() {
        $this->UnsupportedHTTPMethod();
    }

    public function POST() {
        $this->UnsupportedHTTPMethod();
    }

    public function PUT() {
        $this->UnsupportedHTTPMethod();
    }

    public function DELETE() {
        $this->UnsupportedHTTPMethod();
    }

}

?>
