<?php

namespace Core\Http;

class Request{

    private static $params;
    private static $headers;

    public function __construct()
    {
        self::$params = $_REQUEST;
        self::$headers = apache_request_headers();
    }

    public static function get_body(){
        return json_decode(file_get_contents("php://input"));
    }

    public static function get_headers():array{
        if(self::$headers == null){
        return apache_request_headers();
        }
        else{
            return self::$headers;
        }
    }

    public static function get_params(){
        if(self::$params == null){
            return $_REQUEST;
        }
        else{
            return self::$params;
        }
    }

    public static function update(Request $request){
        self::$params = $request::get_params();
        self::$headers = $request::get_headers();
    }


    

}