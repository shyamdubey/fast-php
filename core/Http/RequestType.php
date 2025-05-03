<?php


namespace Core\Http;

class RequestType{


    public static function get(){
        if(RequestType::get_request_type() == 'GET'){
            return true;
        }
        else{
            return false;
        }
    }

    public static function post(){
        if(RequestType::get_request_type() == 'POST'){
            return true;
        }
        else{
            return false;
        }
    }

    public static function delete(){
        if(RequestType::get_request_type() == 'DELETE'){
            return true;
        }
        else{
            return false;
        }
    }

    public static function put(){
        if(RequestType::get_request_type() == 'PUT'){
            return true;
        }
        else{
            return false;
        }
    }


    private static function get_request_type(){
        return $_SERVER['REQUEST_METHOD'];
    }
}