<?php
require_once __DIR__."/functions.php";
require_once __DIR__."/cors.php";
require_once __DIR__."/utils/RouteTable.php";



//include all servicem,
foreach(glob("service/*.php") as $file){
    require_once $file;
}




$routeTable = new RouteTable();

$headers = apache_request_headers();

//is request authorized.
if(!isset($headers['Authorization'])){
    echo sendResponse(false,  401, "Unauthorized access.");
    die();
}
else{
    $token = $headers['Authorization'];
    $token = explode("Bearer ", $token)[1];
    if($token == null){
        echo sendResponse(false,  401, "Unauthorized access.");
    }

    if(!isTokenValid($token)){
        echo sendResponse(false, 401, "Unauthorized access.");
    }
    else{
        $userDetails = getUserFromToken($token);
        $userDetails = json_decode($userDetails);
    }
}

$routesControllerMap = $routeTable->getRoutes();

$routes = [];
$controllersArray =  [];
$allowedMethodsArray = [];
foreach($routesControllerMap as $map){
    array_push($routes, $map['route']);
    array_push($controllersArray, $map['controller']);
    array_push($allowedMethodsArray, $map['allowedMethods']);
}


$uri = $_SERVER['REQUEST_URI'];
$uri_arr = explode("api/", $uri);
$spliced_arr = explode("/", $uri_arr[1]);


switch (count($spliced_arr)){
    
    case 1:
        if($spliced_arr[0] == null){
            defaultFunction();
        }
        else{
            case1();
        }
        break;
    case 2:
        if($spliced_arr[1] == null){
            case1();
        }
        else{
            case2();
        }
        break;
    case 3:
        if($spliced_arr[2] == null){
            case2();
        }
        else{
            case3();
        }
        break;
    default:
    defaultFunction();
        break;            
}



function case1(){
    global $spliced_arr, $routes, $controllersArray, $allowedMethodsArray, $userDetails;
    $route = $spliced_arr[0];
    $requestBody = getRequestBody();
    if(in_array($route, $routes)){
        $requiredService = $controllersArray[array_search($route, $routes)];
        $allowedMethods = $allowedMethodsArray[array_search($route, $routes)];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if(!in_array($requestMethod, $allowedMethods)){
            echo sendResponse(false, 405, "Method Not Allowed");
        }
        $service = new $requiredService;
        switch ($requestMethod){
            case 'POST':
                $requestBody->userId = $userDetails->user_id;
                $service->save($requestBody);
                echo sendResponse(true, 201, "Saved Successfully.");
                break;
            case 'GET':
                echo sendResponse(true, 200, $service->getAll());
                break;
            default:
                echo sendResponse(false, 404, 'Invalid Request');

        }

    }
    else{
        sendResponse(false, 404, "Not Found");
    }
}

function case2(){
    global $spliced_arr, $routes, $controllersArray, $allowedMethodsArray;
    $firstRoute = $spliced_arr[0];
    $secondRoute = $spliced_arr[1];
    if(strlen($firstRoute) < 1 || strlen($secondRoute) < 1){
        echo sendResponse(false, 404, "Page Not Found");

    }
    $route = $firstRoute."/".$secondRoute;
    if(in_array($route, $routes)){
        $requiredService = $controllersArray[array_search($route, $routes)];
        $allowedMethods = $allowedMethodsArray[array_search($route, $routes)];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if(!in_array($requestMethod, $allowedMethods)){
            echo sendResponse(false, 405, "Method Not Allowed");
        }
        $service = explode("::", $requiredService)[0];
        $service = new $service;
        $requiredMethod = explode("::", $requiredService)[1];
        $requestBody = getRequestBody();
        switch ($requestMethod){
            case 'POST':
                echo sendResponse(true, 200, $service->$requiredMethod($requestBody));
                break;
            case 'GET':
                echo sendResponse(true, 200, $service->$requiredMethod());
                break;
            case 'PUT':
                echo sendResponse(true, 200, $service->$requiredMethod($requestBody));
                break;
            default:
                echo sendResponse(false, 404, 'Invalid Request');

        }

    }
    else{
        echo sendResponse(false, 404, "Not Found");
    }

}


function case3(){
    global $spliced_arr, $routes, $controllersArray, $allowedMethodsArray;
    $requestBody = getRequestBody();
    $firstRoute = $spliced_arr[0];
    $secondRoute = $spliced_arr[1];
    $thirdRoute = $spliced_arr[2];
    if(strlen($firstRoute) < 1 || strlen($secondRoute) < 1 || strlen($thirdRoute) < 1){
        echo sendResponse(false, 404, "Page Not Found");

    }
    $route = $firstRoute."/".$secondRoute."/{val}";
    if(in_array($route, $routes)){
        $requiredService = $controllersArray[array_search($route, $routes)];
        $allowedMethods = $allowedMethodsArray[array_search($route, $routes)];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if(!in_array($requestMethod, $allowedMethods)){
            echo sendResponse(false, 405, "Method Not Allowed");
        }
        $service = explode("::", $requiredService)[0];
        $service = new $service;
        $requiredMethod = explode("::", $requiredService)[1];
        $thirdValue = $thirdRoute;

        switch ($requestMethod){
            case 'GET':
                echo sendResponse(true, 200, $service->$requiredMethod($thirdValue));
                break;
            case 'DELETE':
                echo sendResponse(true, 200, $service->$requiredMethod($thirdValue));
            default:
                echo sendResponse(false, 404, '404 Not Found');

        }

    }
    else{
        echo sendResponse(false, 404, "404 Not Found");
    }

}

function defaultFunction(){
    echo sendResponse(true, 200, "UP");


}

