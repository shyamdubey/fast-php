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

    public function deleteById($id){
        if($this->categoryRepo->deleteById($id)){
            echo sendResponse(true, 200, "Deleted Successfully.");
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Please Try Again.");
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