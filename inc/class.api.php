<?php

class APIErrorCodes
{

    const OK = 0;
    const HTTP_METHOD_NOT_ALLOWED = 1;
    const PARAM_DAY_MISSING = 2;
    const OAUTH_UNAUTHORIZED = 3;
    const HTTP_X_METHOD_INVALID = 4;
    const INVALID_ENDPOINT = 5;
    const PARAM_DAY_INVALID = 6;
    const UNKNOWN_USER_TYPE = 7;
    const INVALID_GROUP = 8;
    const MISSING_FILE = 9;
    const UPLOAD_FAILED = 10;
    const OAUTH_MISSING_USER = 11;

    private static $messages = Array(
        self::OK => '',
        self::HTTP_METHOD_NOT_ALLOWED => 'HTTP Method not allowed',
        self::PARAM_DAY_MISSING => 'Missing day parameter',
        self::OAUTH_UNAUTHORIZED => 'Bad Authentication data',
        self::HTTP_X_METHOD_INVALID => 'Invalid Method (HTTP_X_HTTP_METHOD_OVERRIDE)',
        self::INVALID_ENDPOINT => 'Invalid Endpoint',
        self::PARAM_DAY_INVALID => 'Invalid value for day parameter. Possible values are today|tomorrow|numeric',
        self::UNKNOWN_USER_TYPE => 'Unknown user type',
        self::INVALID_GROUP => 'Unknown group',
        self::MISSING_FILE => 'Missing file',
        self::UPLOAD_FAILED => 'Upload of file failed',
        self::OAUTH_MISSING_USER => 'No user could be found. Probably you are using a wrong grant type?!'
    );
    private static $httpStatus = Array(
        self::OK => HTTPStatus::_OK,
        self::HTTP_METHOD_NOT_ALLOWED => HTTPStatus::_METHOD_NOT_ALLOWED,
        self::PARAM_DAY_MISSING => HTTPStatus::_BAD_REQUEST,
        self::OAUTH_UNAUTHORIZED => HTTPStatus::_UNAUTHORIZED,
        self::HTTP_X_METHOD_INVALID => HTTPStatus::_METHOD_NOT_ALLOWED,
        self::INVALID_ENDPOINT => HTTPStatus::_NOT_FOUND,
        self::PARAM_DAY_INVALID => HTTPStatus::_BAD_REQUEST,
        self::UNKNOWN_USER_TYPE => HTTPStatus::_BAD_REQUEST,
        self::INVALID_GROUP => HTTPStatus::_BAD_REQUEST,
        self::MISSING_FILE => HTTPStatus::_BAD_REQUEST,
        self::UPLOAD_FAILED => HTTPStatus::_BAD_REQUEST,
        self::OAUTH_MISSING_USER => HTTPStatus::_UNAUTHORIZED
    );

    public static function GetError($ID) 
    {
        return Array('code' => $ID, 'message' => self::$messages[$ID]);
    }

    public static function GetStatus($ID) 
    {
        return self::$httpStatus[$ID];
    }

}

?>
