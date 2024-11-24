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
        $loggedInUser = json_decode($loggedInUser);
        if($loggedInUser != null){
            return $this->getAllByUserId($loggedInUser->user_id);
        }
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->categoryRepo->deleteById($id);
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