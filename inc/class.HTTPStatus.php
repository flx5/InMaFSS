<?php

class HTTPStatus
{
    /*
     * 1xx Informational
     */

    const _CONTINUE = 100;
    const _SWITCHING_PROTOCOLS = 101;

    /*
     * 2xx Success
     */
    const _OK = 200;
    const _CREATED = 201;
    const _ACCEPTED = 202;
    const _NON_AUTHORITATIVE_INFORMATION = 203;
    const _NO_CONTENT = 204;
    const _RESET_CONTENT = 205;
    const _PARTIAL_CONTENT = 206;

    /*
     * 3xx Redirection
     */
    const _MULTIPLE_CHOICES = 300;
    const _MOVED_PERMANENTLY = 301;
    const _FOUND = 302;
    const _SEE_OTHER = 303;
    const _NOT_MODIFIED = 304;
    const _USE_PROXY = 305;
    // 306 is specified as unused
    const _TEMPORARY_REDIRECT = 307;

    /*
     * 4xx Client Error
     */
    const _BAD_REQUEST = 400;
    const _UNAUTHORIZED = 401;
    const _PAYMENT_REQUIRED = 402;
    const _FORBIDDEN = 403;
    const _NOT_FOUND = 404;
    const _METHOD_NOT_ALLOWED = 405;
    const _NOT_ACCEPTABLE = 406;
    const _PROXY_AUTHENTICATION_REQUIRED = 407;
    const _REQUEST_TIMEOUT = 408;
    const _CONFLICT = 409;
    const _GONE = 410;
    const _LENGTH_REQUIRED = 411;
    const _PRECONDITION_FAILED = 412;
    const _REQUEST_ENTITY_TOO_LARGE = 413;
    const _REQUEST_URI_TOO_LONG = 414;
    const _UNSUPPORTED_MEDIA_TYPE = 415;
    const _REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const _EXPECTATION_FAILED = 417;

    /*
     * 5xx Server error
     */
    const _INTERNAL_SERVER_ERROR = 500;
    const _NOT_IMPLEMENTED = 501;
    const _BAD_GATEWAY = 502;
    const _SERVICE_UNAVAILABLE = 503;
    const _GATEWAY_TIMEOUT = 504;
    const _HTTP_VERSION_NOT_SUPPORTED = 505;

    private static $messages = Array(
        self::_CONTINUE => 'Continue',
        self::_SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::_OK => 'OK',
        self::_CREATED => 'Created',
        self::_ACCEPTED => 'Accepted',
        self::_NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::_NO_CONTENT => 'No Content',
        self::_RESET_CONTENT => 'Reset Content',
        self::_PARTIAL_CONTENT => 'Partial Content',
        self::_MULTIPLE_CHOICES => 'Multiple Choices',
        self::_MOVED_PERMANENTLY => 'Moved Permanently',
        self::_FOUND => 'Found',
        self::_SEE_OTHER => 'See Other',
        self::_NOT_MODIFIED => 'Not Modified',
        self::_USE_PROXY => 'Use Proxy',
        self::_TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::_BAD_REQUEST => 'Bad Request',
        self::_UNAUTHORIZED => 'Unauthorized',
        self::_PAYMENT_REQUIRED => 'Payment Required',
        self::_FORBIDDEN => 'Forbidden',
        self::_NOT_FOUND => 'Not Found',
        self::_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::_NOT_ACCEPTABLE => 'Not Acceptable',
        self::_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::_REQUEST_TIMEOUT => 'Request Timeout',
        self::_CONFLICT => 'Conflict',
        self::_GONE => 'Gone',
        self::_LENGTH_REQUIRED => 'Length Required',
        self::_PRECONDITION_FAILED => 'Precondition Failed',
        self::_REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
        self::_REQUEST_URI_TOO_LONG => 'Request-URI Too Long',
        self::_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::_REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested Range Not Satisfiable',
        self::_EXPECTATION_FAILED => 'Expectation Failed',
        self::_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::_NOT_IMPLEMENTED => 'Not Implemented',
        self::_BAD_GATEWAY => 'Bad Gateway',
        self::_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::_GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::_HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported'
    );

    /**
     * This function returns the appropriate message for the status code
     * 
     * @param  int $code
     * @return string
     */
    public static function GetMessage($code) 
    { 
        return self::$messages[$code];
    }

    /**
     * This function returns a String containing the HTTP Status that can be sent with the header function
     * 
     * @param  int $code
     * @return string
     */
    public static function GetHeader($code) 
    {
        return 'HTTP/1.1 ' . $code . ' ' . self::GetMessage($code);
    }

}

?>
