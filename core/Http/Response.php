<?php

namespace Core\Http;
use Core\Enums\HttpStatus;

class Response{

    public static function json(HttpStatus $statusCode, $data){
        header("Content-type:application/json");
        http_response_code($statusCode->value);
        echo json_encode($data);
        die();

    }
}