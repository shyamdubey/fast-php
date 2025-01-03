<?php

require_once __DIR__."/../repo/CategoryRepo.php";
require_once __DIR__."/../models/Category.php";
require_once __DIR__."/../functions.php";

class CategoryService{

    public $categoryRepo;

    public function __construct(){
        $this->categoryRepo = new CategoryRepo();
    }

    public function getAll(){
        return $this->categoryRepo->getAll();
    }

    public function save($requestBody){
        $model = new Category();
        if(!isset($requestBody->categoryName)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->categoryName = $requestBody->categoryName;
        $model->userId = $requestBody->userId;
        $this->categoryRepo->save($model);
        

    }


    public function getAllByUserId($userId){
        return $this->categoryRepo->getAllByUserId($userId);
    }

    public function myCategory(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }


    public function update($requestBody){
        if($this->categoryRepo->update($requestBody)){
            echo sendResponse(true, 200, "Category Updated Successfully.");
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Please try again.");
        }
    }


    public function softDelete($id){
        if($id != null){
            if($this->getById($id) != null){
                $loggedInUser = getLoggedInUserInfo();
                if($loggedInUser != null){
                    if($this->categoryRepo->softDelete($id, $loggedInUser->userId)){
                        sendResponse(true, 200, "Deleted successfully.");
                    }
                    else{
                        sendResponse(false, 500, "Something went wrong.");
                    }
                }
                else{
                    sendResponse(false, 500, "Could not load user data.");
                }
            }
        }
    }

    public function deleteById($id){
        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            //get the saved data
            $data = $this->getById($id);
            if($data != null){
                if($loggedInUser->userId == $data['userId']){
                    if($this->categoryRepo->deleteById($id)){
                        echo sendResponse(true, 200, "Deleted Successfully.");
                    }
                    else{
                        echo sendResponse(false, 500, "Internal Server Error. Please Try Again.");
                    }
                }
                else{
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }

            }
        }
        
    }

    public function getById($id){
        $result = $this->categoryRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }



}