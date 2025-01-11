<?php


require_once __DIR__."/../functions.php";
require_once __DIR__."/../utils/RouteTable.php";


class UserService{
   
    

    public function getAll(){
        return getAllUsers();
    }

    function refreshToken($requestBody){
        return refreshTokenMcqBuddy($requestBody);
    }

    public function save($requestBody){
        if(
            !isset($requestBody->username) ||
            !isset($requestBody->name) ||
            !isset($requestBody->email) ||
            !isset($requestBody->password)
        )
        {
            sendResponse(false, 400, 'Missing required parameters.');
        }

        //assert getting string in request body
        if(gettype($requestBody->username) != 'string' || gettype($requestBody->name) != 'string' || gettype($requestBody->email) != 'string' || gettype($requestBody->password) != 'string'){
            sendResponse(false, 400, "Invalid Body Provided.");
        }

        if( strlen($requestBody->username) < 4 || strlen($requestBody->name) < 4 || strlen($requestBody->email) < 8 || strlen($requestBody->password) < 4){
            sendResponse(false, 400, "Invalid Request. Username, name and password should be greater than 3 characters. Length of email should be greater than 8.");
        }

        return createUserAccount($requestBody);
        

    }

    public function performLogin($requestBody){
        if(
            !isset($requestBody->username) ||
            !isset($requestBody->password)
        )
        {
            sendResponse(false, 400, 'Missing required parameters.');
        }

        if( strlen($requestBody->username) < 1 || strlen($requestBody->password) < 1){
            sendResponse(false, 400, "Invalid Request. Username, name and password should be greater than 3 characters. Length of email should be greater than 8.");
        }

        return performMcqbuddyLogin($requestBody);
    }

    public function performLogout($requestBody){
        $token = getTokenFromRequest();
        if($token == null){
            sendResponse(false, 500, "Invalid Request.");
        }
        return performMcqBuddyLogout($token);
    }

    public function getByUsernameOrEmailOrNameLike($val){
        return getUsersWhereNameEmailUsernameLike($val);
    }

    public function getByEmail($email){
        $data = getUsersByEmail($email);
        if($data == null && $data->userId != null){
            sendResponse(false, 404, "No user found.");
        }else{
            return $data;
        }
    }

    public function getByPagination($page){
        return getAllUsersByPagination($page);
    }

    public function getById($id){
        return getUserById($id);
    }


    public function myData(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $loggedInUser;
        }
        else{
            sendResponse(false, 404, 'Data not found.');
        }
    }

    
}