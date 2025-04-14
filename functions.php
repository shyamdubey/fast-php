<?php
require_once __DIR__."/assets/dbconn.php";
require_once __DIR__."/models/FileUpload.php";
require_once __DIR__."/service/FileUploadService.php";
foreach (glob(__DIR__ . "/models/*.php") as $file) {
    require_once $file;
}


function getUUID()
{
    return uniqid("familytree");
}

function getLogFileNameForToday()
{
    $today = date("d-M-Y", time());
    $dir = __DIR__ . "/logs";
    $fileName = $dir . "/" . $today . "_log.log";
    return $fileName;
}


function getRequestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

function assertRequestPost()
{
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if ($requestMethod != 'POST') {
        echo sendResponse(false, 405, "Only POST method allowed.");
    }
}

function assertRequestGet()
{
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if ($requestMethod != 'GET') {
        echo sendResponse(false, 405, "Only GET method allowed.");
    }
}

function assertRequestDelete()
{
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if ($requestMethod != 'DELETE') {
        echo sendResponse(false, 405, "Only DELETE method allowed.");
    }
}


function makeCurlRequest($url, $requestType, $data)
{

    //prepare the header
    $headers = ['Authorization:  "Bearer '.getTokenFromRequest()];
    $curlHandle = curl_init($url);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
    if ($requestType == 'POST') {
        curl_setopt($curlHandle, CURLOPT_POST, true);
    }
    if ($requestType == 'GET') {
        curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
    }

    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

    $curlResponse = curl_exec($curlHandle);
    curl_close($curlHandle);
    return $curlResponse;
}



function verifyToken($token)
{
    if ($token != null && $token != "") {
        return makeCurlRequest(AppConstants::MCQBUDDY_VERIFY_TOKEN_API, 'POST', json_encode(["token" => $token]));
    } else {
        return "token is null or empty";
    }
}

function isTokenValid($token)
{
    if ($token != null || $token != "") {
        $tokenJson = verifyToken($token);
        if($tokenJson != null){
            $tokenJson = json_decode($tokenJson);
            try {
                $response = $tokenJson->statusCode;
                if ($response == "200") {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }
        
        
    } 
    return false;
    
}


function getUserFromToken($token)
{
    if ($token != null) {
        $jsonData = makeCurlRequest(AppConstants::MCQBUDDY_GET_USER_BY_TOKEN, 'POST', json_encode(["token" => $token]));
        $user = json_decode($jsonData)->data;
        return $user;
    }
}

function performMcqbuddyLogin($requestBody){
    $response = makeCurlRequest(AppConstants::MCQBUDDY_LOGIN_API, 'POST', json_encode($requestBody));
    if($response != null){
        $response = json_decode($response);
        if($response == null){
            sendResponse(false, 500, "Internal Server Error");
        }
        if($response->statusCode == 200){
            return $response->data;
        }
        else{
            sendResponse(false, 500, $response->data);
        }
    }
    else{
        sendResponse(false, 500, "Something went wrong.");
    }
    
}

function performMcqbuddyLogout($token){
    $object = new stdClass();
    $object->token = $token;
    $data = json_encode($object);
    $response = makeCurlRequest(AppConstants::MCQBUDDY_LOGOUT_API, 'POST', $data);
    if($response != null){
        $response = json_decode($response);
            sendResponse($response->status, $response->statusCode, $response->data);
    }
    return false;

}

function refreshTokenMcqbuddy($requestBody){
    $response = makeCurlRequest(AppConstants::MCQBUDDY_REFRESH_TOKEN, 'POST', json_encode($requestBody));
    if($response != null){
        $response = json_decode($response);
        sendResponse($response->status, $response->statusCode, $response->data);
    }
    else{
        sendResponse(false, 500, "Something went wrong.");
    }
}

function getUserIdFromToken($token)
{
    if ($token != null && $token != "") {
        $jsonData = getUserFromToken($token);
        $json = json_decode($jsonData);
        if ($json->jwtTokenCreatedFor == 'Admin') {
            return $json->super_users_id;
        } else {
            return $json->user_id;
        }
    } else {
        return 1;
    }
}


function getRequestBody()
{
    $requestBody = json_decode(file_get_contents("php://input"));
    if($requestBody == null){
        $requestBody = new stdClass();
    }
    return $requestBody;
}

function assertRequestPut()
{
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if ($requestMethod != 'PUT') {
        echo sendResponse(false, 405, "Only PUT method allowed.");
    }
}


function sendResponse($status, $statusCode, $data)
{
    http_response_code($statusCode);
    echo json_encode(['status' => $status, "statusCode" => $statusCode, 'data' => $data]);
    die();
    exit();
}

function deleteLogFile($fileName) {}

function createLogFile()
{
    $fileName = getLogFileNameForToday();
    $fp = fopen($fileName, "w");
    return $fp;
}


function logMessage($message)
{
    $now = date("d-M-Y H:i:s", time());
    $fileName = getLogFileNameForToday();
    if (file_exists($fileName)) {
        $fp = fopen($fileName, "a");
    } else {
        $fp = createLogFile();
    }
    fwrite($fp, "\n" . $message . "--------" . $now . "\n ---------------------------------------------------------");
}

function readLogFileContent($fileName)
{
    $logFile = __DIR__ . "/logs/" . $fileName;
    $str = '';
    if (file_exists($logFile)) {
        $fp = fopen($logFile, "r");
        $str .= '<pre>' . file_get_contents($logFile) . '</pre>';
    } else {
        $str .= "File does not exist.";
    }

    return $str;
}


function getTokenFromRequest()
{
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $bearerToken = $headers['Authorization'];
        $bearerTokenArr = explode("Bearer ", $bearerToken); 
        return $bearerTokenArr[1];
    }
    return null;
}


function getLoggedInUserInfo(){
    $token = getTokenFromRequest();
    if($token != null){
       return getUserFromToken($token);
    }
    return null;
}

function verifyModel($model, $jsonObject)
{
    $model = new $model();
    $classVarsList = get_class_vars(get_class($model));
    foreach ($classVarsList as $key => $value) {
        if (!property_exists($jsonObject, $key)) {
            echo sendResponse(false, 400, $key . " is required property.");
        }
    }

    return $model;
}



function generateSpaceJoinCode(){
    return strtoupper(uniqid());
}


function getUserById($userId){
    $jsonResponse = makeCurlRequest(AppConstants::MCQBUDDY_GET_USER_BY_ID.$userId, 'GET', null);
    if($jsonResponse != null){
        $jsonData = json_decode($jsonResponse);
        if($jsonData->statusCode == 200){
            return $jsonData->data;
        }
        else if($jsonData->statusCode == 404){
            sendResponse($jsonData->status, 404, $jsonData->data);
        }
    }
    else{
        sendResponse(false, 500, "Error while getting user details from MCQ Buddy");
    }
}


function createUserAccount($requestBody){
    $jsonResponse = makeCurlRequest(AppConstants::MCQBUDDY_CREATE_ACCOUNT, 'POST', json_encode($requestBody));
    if($jsonResponse != null){
        $jsonData = json_decode($jsonResponse);
        if($jsonData->statusCode == 201){
            return $jsonData->data;
        }
        else{
            echo sendResponse($jsonData->status, $jsonData->statusCode, $jsonData->data);
            die();
        }
    }
    else{
        echo sendResponse(false, 500, 'Internal Server Error. Something went wrong.');
    }
}

function getUsersWhereNameEmailUsernameLike($value){
    $jsonResponse = makeCurlRequest(AppConstants::MCQBUDDY_GET_USERS_WHERE_NAME_LIKE.$value, 'GET', null);
    if($jsonResponse != null){
        $jsonData = json_decode($jsonResponse);
        return $jsonData->data;
    }
    return null;
}

function saveNotification($notificationOwnerId, $userId, $content, $redirectUrl){
    $model = new stdClass();
    $model->notification_owner_id = $notificationOwnerId;
    $model->user_id = $userId;
    $model->content = $content;
    $model->redirect_url = $redirectUrl;

    $response = makeCurlRequest(AppConstants::MCQBUDDY_SAVE_NOTIFICATION, 'POST', json_encode($model));
}

function getUsersByEmail($value){
    $jsonResponse = makeCurlRequest(AppConstants::MCQBUDDY_GET_USERS_BY_EMAIL.$value, 'GET', null);
    if($jsonResponse != null){
        $jsonData = json_decode($jsonResponse);
        if($jsonData->statusCode == 200){
            return $jsonData->data;

        }
        else{
            sendResponse(false, $jsonData->statusCode, $jsonData->data);
        }
    }
    return null;
}

function getAllUsers(){
    $jsonResponse = makeCurlRequest(AppConstants::MCQBUDDY_GET_ALL_USERS, 'GET', null);
    if($jsonResponse != null){
        $jsonData = json_decode($jsonResponse);
        if($jsonData->statusCode == 200){
            return $jsonData->data;
        }
    }
    return null;
}


function getAllUsersByPagination($page){
    $jsonResponse = makeCurlRequest(AppConstants::MCQBUDDY_GET_ALL_USERS_BY_PAGINATION.$page, 'GET', null);
    if($jsonResponse != null){
        $jsonData = json_decode($jsonResponse);
        if($jsonData->statusCode == 200){
            return $jsonData->data;
        }
    }
    return null;
}


function makeRequestBodySafe($requestBody){
    global $conn;
    if($requestBody != null){
        $array = get_mangled_object_vars($requestBody);
        $arrayKeys = array_keys($array);
        for($i = 0; $i < count($arrayKeys); $i++){
            $key = $arrayKeys[$i];
            if($array[$key] != null && gettype($array[$key]) == "string" && strlen($array[$key])>0){
                $requestBody->$key = mysqli_real_escape_string($conn, $array[$key]);
            }
        }
    }
    
    return $requestBody;
}


function uploadFile($file, $purpose, $userId){
    if($purpose == "questions"){
        $target_dir = AppConstants::QUESTIONS_IMAGE_DIR;
    }
    else {
        $target_dir = AppConstants::FILES_DIR;
    }

   
    $fileName = basename($file["name"]);
    $generatedFileName = str_replace(" ", "_", getUUID().$fileName);


    $target_file = $target_dir.$generatedFileName;
     //get file extension
     $fileInfo = pathinfo($target_file);
    $fileExtension = $fileInfo['extension'];
    if($purpose == "questions"){
        if(!in_array($fileExtension, AppConstants::EXTENSIONS_FOR_IMAGES)){
            echo sendResponse(false, 400, "File should be any of the ".implode(", ",AppConstants::EXTENSIONS_FOR_IMAGES));
        }
    }
    else if($purpose == "files"){
        if(!in_array($fileExtension, AppConstants::EXTENSIONS_FOR_FILES)){
            echo sendResponse(false, 400, "File should be any of the ".implode(", ",AppConstants::EXTENSIONS_FOR_FILES));
        }
    }



    //get the file size
    $fileSize = filesize($file['tmp_name']);

    if($fileSize > AppConstants::MAX_FILE_SIZE){
        echo sendResponse(false, 400, "File size should be less than ".AppConstants::MAX_FILE_SIZE / (1024*1024). "Mb" );
    }
    $tmpName = $file['tmp_name'];


    try{
        if(move_uploaded_file($tmpName, $target_file)){
            //save the file details in database
            $model = new FileUpload();
            $model->fileUrl = AppConstants::BASE_URL."/"."images/".$purpose."/".$generatedFileName;
            $model->userId = $userId;
            $model->purpose = $purpose;
            $model->isPublic = 0;

            //file upload service
            $fileUploadService = new FileUploadService();
            $fileUploadService->save($model);
        }
        else{
            echo sendResponse(false, 500, "Internal Server Occured. Please Try again. Or inform to administrator");
        }

    }
    catch(Exception $e){
        echo sendResponse(false, 500, $e->getMessage());
    }
}