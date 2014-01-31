<?php
require_once(realpath(dirname(__FILE__))."/HTTPStatus.php");

class Rest {
    /**
     * The default return type
     */
    const DEFAULT_RESPONSE_FORMAT = "json";
    
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
            if(array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
                    $x_http_method = $_SERVER['HTTP_X_HTTP_METHOD'];
            
            /*
             * HTTP METHOD OVERRIDE AS SPECIFIED BY GOOGLE 
             * https://developers.google.com/gdata/docs/2.0/basics?hl=de&csw=1#UpdatingEntry
             */
            if(array_key_exists('HTTP_X_HTTP_METHOD_OVERRIDE', $_SERVER))
                    $x_http_method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
            
            if ($x_http_method == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($x_http_method == 'PUT') {
                $this->method = 'PUT';
            } else {
                $this->Response('Invalid Method', HTTPStatus::_METHOD_NOT_ALLOWED);
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
                $this->Response('Invalid Method', HTTPStatus::_METHOD_NOT_ALLOWED);
                break;
        }
      
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = strtolower(array_shift($this->args)); 

        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
     
        $this->LoadEndpoint();
    }

    private function LoadEndpoint() { 
        // Ensure that the endpoint doesn't contain any special chars that could harm the filesystem
        $this->endpoint = str_replace(".", "_", $this->endpoint);
        $preg = preg_replace("/([a-z]|_)/", "", $this->endpoint); 
        if($preg !== "") 
            $this->Response ('Invalid Endpoint', HTTPStatus::_NOT_FOUND);
        
        if($this->endpoint == "") 
            $this->Response ('Invalid Endpoint', HTTPStatus::_NOT_FOUND);
        
        $this->endpoint[0] = strtoupper($this->endpoint[0]);
        
        $file = $this->controller_dir."/".$this->endpoint.".php";
        
        if(!file_exists($file)) 
            $this->Response ('Invalid Endpoint', HTTPStatus::_NOT_FOUND);
        
        require_once($file);
        
        $reflection = new ReflectionClass('Controller_'.$this->endpoint); 
        
        
        $controller = $reflection->newInstance($this->args);
        /* @var $controller RestController */
         
        if(!$controller->isAuthorized()) {
            $this->Response('Unauthorized', HTTPStatus::_UNAUTHORIZED);
        }
        
        $reflection->getMethod($this->method)->invoke($controller);
        
        $this->Response(json_encode($controller->GetResponse()), $controller->GetStatus());
    }
    
    protected function Response($data, $status) { 
        $this->CORS(); 
        header(HTTPStatus::GetHeader($status));
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
        
        header("Content-Type: application/json");
        
    }
}

abstract class RestController {
    protected $args;
    protected $response;
    protected $responseStatus;
    protected $errors;
    
    public function __construct($args) {
        $this->args = $args;
        $this->response = Array();
        $this->responseStatus = HTTPStatus::_OK;
        $this->errors = Array();
    }
    
    public final function GetResponse() {
        if(count($this->errors) != 0)
            $this->response['errors'] = $this->errors;
        
        return $this->response;
    }
    
    public final function GetStatus() {
        return $this->responseStatus;
    }
    
    protected function AddError($error) {
        $this->errors[] = $error;
    }
    
    protected function UnsupportedHTTPMethod() {
        $this->responseStatus = HTTPStatus::_METHOD_NOT_ALLOWED;
        $this->response['errors'] = RestErrors::GetError(RestErrors::HTTP_METHOD_NOT_ALLOWED);
    }
    
    public function isAuthorized() {
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

class RestErrors {
    const HTTP_METHOD_NOT_ALLOWED = 1;
    
    private static $messages = Array(
        self::HTTP_METHOD_NOT_ALLOWED => 'HTTP Method not allowed'
    );
    
    public static function GetError($ID) {
        return Array('code'=>$ID, 'message'=>self::$messages[$ID]);
    }
}
?>
