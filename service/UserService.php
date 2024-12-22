<?php


require_once __DIR__."/../functions.php";
require_once __DIR__."/../utils/RouteTable.php";


class UserService{
   
    

    public function getAll(){
        return getAllUsers();
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

        return createUserAccount($requestBody);
        

    }

    public function getByUsernameOrEmailOrNameLike($val){
        return getUsersWhereNameEmailUsernameLike($val);
    }

    public function getByEmail($email){
        $data = getUsersByEmail($email);
        if($data == null){
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