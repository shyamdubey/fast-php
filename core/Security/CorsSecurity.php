<?php


namespace Core\Security;

use App\Settings;
use Core\Enums\HttpStatus;
use Core\Http\Response;
use ReflectionClass;

class CorsSecurity
{


    public static function init()
    {
       $reflection = new ReflectionClass(Settings::class);
       $constants = $reflection->getConstants();
        if (in_array(Settings::CORS_DOMAINS, $constants)) {
            $cors_domains = Settings::CORS_DOMAINS;

            // Allow from any origin
            header("Access-Control-Allow-Origin: *");

            // Allow specific methods
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

            // Allow specific headers
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

            // Handle preflight requests
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                http_response_code(HttpStatus::OK->value);
                exit;
            }
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "";
           
            if ($cors_domains != "*") {
                if (gettype($cors_domains) == "array") {
                    if ( !in_array($host, $cors_domains)) {
                        Response::json(HttpStatus::FORBIDDEN, ["message" => "Invalid Domain. Add this domain in Settings under variable CORS_DOMAINS"]);
                    }
                }
                else if ($cors_domains != $host) {
                    Response::json(HttpStatus::FORBIDDEN, ["message" => "Invalid Domain. Add this domain in Settings under variable CORS_DOMAINS"]);
                }
            } 
        }
    }
}
