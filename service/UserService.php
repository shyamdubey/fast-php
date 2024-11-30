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
            echo sendResponse(false, 400, 'Missing required parameters.');
        }

        return createUserAccount($requestBody);
        

    }

    public function getByUsernameOrEmailOrNameLike($val){
        return getUsersWhereNameEmailUsernameLike($val);
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
            $user = json_decode($loggedInUser);
            return $user;
        }
        else{
            echo sendResponse(false, 404, 'Data not found.');
        }
    }

    
}