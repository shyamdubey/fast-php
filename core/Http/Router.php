<?php

namespace Core\Http;

use BadMethodCallException;
use Core\Enums\HttpStatus;
use Core\Exception\ClassNotFoundException;
use Core\Exception\InvalidCallbackException;
use Core\Exception\NotAnInstanceException;
use Core\Http\RequestType;
use Core\Middleware\MiddlewareInterface;
use LDAP\Result;

class Router{

    private static $middlewaresQueue = [];
    private static $routes = [];


    public static function get($url, $callback, $middlewares = []){
        $url = $url;
        $callback = $callback;
       
        $route["url"]=$url;
        $route["callback"] = $callback;
        $route["method"] = 'GET';
        $route["middlewares"] = $middlewares;

        array_push(self::$routes, $route);
        // self::handle($url, $callback, $middlewares);
    }

    public static function post($url, $callback, $middlewares = []){
        $url = $url;
        $callback = $callback;
       
        $route["url"]=$url;
        $route["callback"] = $callback;
        $route["method"] = 'POST';
        $route["middlewares"] = $middlewares;

        array_push(self::$routes, $route);
        // self::handle($url, $callback, $middlewares);
    }

    public static function delete($url, $callback, $middlewares = []){
        $url = $url;
        $callback = $callback;
       
        $route["url"]=$url;
        $route["callback"] = $callback;
        $route["method"] = 'DELETE';
        $route["middlewares"] = $middlewares;

        array_push(self::$routes, $route);
        // self::handle($url, $callback, $middlewares);
    }

    public static function put($url, $callback, $middlewares = []){
        $url = $url;
        $callback = $callback;
       
        $route["url"]=$url;
        $route["callback"] = $callback;
        $route["method"] = 'PUT';
        $route["middlewares"] = $middlewares;

        array_push(self::$routes, $route);
        // self::handle($url, $callback, $middlewares);
    }

    public static function merge($url, $callback, $middlewares = []){
        $url = $url;
        $callback = $callback;
       
        $route["url"]=$url;
        $route["callback"] = $callback;
        $route["method"] = 'MERGE';
        $route["middlewares"] = $middlewares;

        array_push(self::$routes, $route);
        // self::handle($url, $callback, $middlewares);
    }


    private static function handle($url, $callback, $requestMethod, $params=[], $middlewares = []){

        if($_SERVER['REQUEST_METHOD'] != $requestMethod){
            throw new BadMethodCallException("Method not allowed.");
        }

        $params["request_body"] = Request::get_body();

        $request = new Request();
        if(count($middlewares) > 0){
            self::$middlewaresQueue = array_merge($middlewares);
        }

        foreach(self::$middlewaresQueue as $middleware){
            $m = new $middleware;
            if(!$m instanceof MiddlewareInterface){
                throw new NotAnInstanceException($m::class. " is not instance of ".MiddlewareInterface::class);
            }
            $request = $m->handle($request);            
            Request::update($request);
        }

        if(!stripos($callback, "@")){
            throw new InvalidCallbackException("Invalid Callback Function Provided. Please ensure your call back function consists @ symbol to separate the controller and fucntion.");
        }
        $arr = explode("@", $callback);
        $controller = $arr[0];
        $controller_method = $arr[1];

        $full_class = "App\\services\\$controller";

        if(class_exists($full_class) && method_exists($full_class, $controller_method)){
            $instance = new $full_class;
            $instance->$controller_method($params);
        }
        else{
            throw new ClassNotFoundException($full_class. " Class Not Found.");
        }


        
    }


    public static function init(){
        
        $uri = $_SERVER['REQUEST_URI'];
        $uri_arr = explode("api", $uri);
        $routeUri = $uri_arr[1];

        if(count(self::$routes)>0){
            foreach(self::$routes as $route){
                    $url = trim($routeUri, '/');
                    
                    // foreach ($route as $pattern => $callback) {
                        $patternRegex = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', trim($route['url'], '/'));
                        $patternRegex = "@^" . $patternRegex . "$@";
                
                        if (preg_match($patternRegex, $url, $matches)) {
                            // Filter only named captures
                            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                            self::handle($route['url'], $route['callback'], $route['method'], $params, $middleware = $route['middlewares']);
                        }
                    // }
                
            }
        }
        else{
            Response::json(HttpStatus::NOT_FOUND, ["message"=>"Not Found"]);
        }
    }



}