<?php
require_once __DIR__."/functions.php";
require_once __DIR__."/cors.php";
require_once __DIR__."/utils/RouteTable.php";



//include all controllers and models

foreach(glob("service/*.php") as $file){
    require_once $file;
}




$routeTable = new RouteTable();

$routesControllerMap = $routeTable->getRoutes();

$routes = [];
$controllersArray =  [];
foreach($routesControllerMap as $map){
    array_push($routes, $map['route']);
    array_push($controllersArray, $map['controller']);
}


$uri = $_SERVER['REQUEST_URI'];
$uri_arr = explode("/", $uri);

$spliced_arr = array_slice($uri_arr, 3, count($uri_arr));

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
    global $spliced_arr, $routes, $controllersArray;
    $route = $spliced_arr[0];
    $requestBody = getRequestBody();
    if(in_array($route, $routes)){
        $requiredService = $controllersArray[array_search($route, $routes)];
        $service = new $requiredService;
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        switch ($requestMethod){
            case 'POST':
                echo "Save Request";
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
    global $spliced_arr, $routes, $controllersArray;
    $firstRoute = $spliced_arr[0];
    $secondRoute = $spliced_arr[1];
    if(strlen($firstRoute) < 1 || strlen($secondRoute) < 1){
        echo sendResponse(false, 404, "Page Not Found");

    }
    $route = $firstRoute."/".$secondRoute;
    if(in_array($route, $routes)){
        $requiredService = $controllersArray[array_search($route, $routes)];
        $service = explode("::", $requiredService)[0];
        $service = new $service;
        $requiredMethod = explode("::", $requiredService)[1];
        $requestBody = getRequestBody();
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        switch ($requestMethod){
            case 'POST':
                echo "Save Request";
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
        sendResponse(false, 404, "Not Found");
    }

}


function case3(){
    global $spliced_arr, $routes, $controllersArray;
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
        $service = explode("::", $requiredService)[0];
        $service = new $service;
        $requiredMethod = explode("::", $requiredService)[1];
        $thirdValue = $thirdRoute;

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        switch ($requestMethod){
            case 'GET':
                echo sendResponse(true, 200, $service->$requiredMethod($thirdValue));
                break;
            case 'DELETE':
                echo sendResponse(true, 200, $service->$requiredMethod($thirdValue));
            default:
                echo sendResponse(false, 404, 'Invalid Request');

        }

    }
    else{
        sendResponse(false, 404, "Not Found");
    }

}

function defaultFunction(){
    echo "Not a valid api endpoint";

}

