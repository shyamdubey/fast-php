<?php


foreach(glob(__DIR__."/models/*.php") as $file){
    require_once $file;
}


function getUUID(){
    return uniqid("quizbuddy");
}

function getLogFileNameForToday(){
    $today = date("d-M-Y", time());
    $dir = __DIR__."/logs";
    $fileName = $dir."/".$today."_log.log";
    return $fileName;
}


function getRequestMethod(){
    return $_SERVER['REQUEST_METHOD'];
}

function assertRequestPost(){
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if($requestMethod != 'POST'){
        echo sendResponse(false, 405, "Only POST method allowed.");
    }
}

function assertRequestGet(){
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if($requestMethod != 'GET'){
        echo sendResponse(false, 405, "Only GET method allowed.");
    }
}

function assertRequestDelete(){
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if($requestMethod != 'DELETE'){
        echo sendResponse(false, 405, "Only DELETE method allowed.");
    }
}


function getRequestBody(){
    return file_get_contents("php://input");
}

function assertRequestPut(){
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if($requestMethod != 'PUT'){
        echo sendResponse(false, 405, "Only PUT method allowed.");
    }
}


function sendResponse($status, $statusCode, $data){
    http_response_code($statusCode);
    echo json_encode(['status'=>$status, "statusCode"=>$statusCode, 'data'=>$data]);
    die();
    exit();
}

function deleteLogFile($fileName){

}

function createLogFile(){
   $fileName = getLogFileNameForToday();
    $fp = fopen($fileName, "w");
    return $fp;
}


function logMessage($message){
    $now = date("d-M-Y H:i:s", time());
    $fileName = getLogFileNameForToday();
    if(file_exists($fileName)){
        $fp = fopen($fileName, "a");
    }
    else{
        $fp = createLogFile();
    }
    fwrite($fp, "\n".$message."--------".$now."\n ---------------------------------------------------------");

}

function readLogFileContent($fileName){
    $logFile = __DIR__."/logs/".$fileName;
    $str = '';
    if(file_exists($logFile)){
        $fp = fopen($logFile, "r");
        $str.= '<pre>'.file_get_contents($logFile).'</pre>';
    }
    else{
        $str.= "File does not exist.";
    }

    return $str;
}

function convertJsonToModel($model, $jsonObject){
    $model = new $model();
    $classVarsList = get_class_vars(get_class($model));
    foreach($classVarsList as $key => $value){
        print_r($jsonObject);
        try{
            if(property_exists($jsonObject, $key)){
                $model->key = $jsonObject[$key];
            }
        }
        catch(Exception $ex){
            echo sendResponse(false, 400, $ex);
        }
    }

    return $model;

}

