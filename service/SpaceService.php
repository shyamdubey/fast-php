<?php

require_once __DIR__."/../repo/SpaceRepo.php";
require_once __DIR__."/../models/Category.php";
require_once __DIR__."/../functions.php";

class SpaceService{

    public $spaceRepo;

    public function __construct(){
        $this->spaceRepo = new SpaceRepo();
    }

    public function getAll(){
        return $this->spaceRepo->getAll();
    }

    public function save($requestBody){
        $model = new Space();
        if(!isset($requestBody->spaceName)
        || !isset($requestBody->spaceDescription)
        || !isset($requestBody->spaceVisibility)
        || !isset($requestBody->spaceUrl)
        ){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        //generate space join code
        $spaceJoinCode = generateSpaceJoinCode();

        
        $model->spaceName = $requestBody->spaceName;
        $model->spaceUrl = trim(str_replace(" ","", $requestBody->spaceUrl));
        $model->spaceDescription = $requestBody->spaceDescription;
        $model->spaceJoinCode = $spaceJoinCode;
        $model->spaceVisibility = $requestBody->spaceVisibility;
        $model->userId = $requestBody->userId;
        if($this->spaceRepo->save($model)){
            echo sendResponse(true, 201, "Saved successfully.");
        }
        echo sendResponse(false, 500, "Internal Server Error. Could not save data.");
        
        

    }


    public function getAllByUserId($userId){
        return $this->spaceRepo->getAllByUserId($userId);
    }

    public function mySpaces(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->spaceRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->spaceRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "The Data for Requested Id not found.");
        }
    }


    public function getBySpaceJoinCode($code){
        return $this->spaceRepo->getBySpaceJoinCode($code);
    }


    public function getBySpaceUrl($url){
        $data = $this->spaceRepo->getBySpaceUrl($url);
        if($data != null){
            return $data;
        }
        else{
            echo sendResponse(false, 404, "Not found.");
        }
    }

    public function getPublicSpaces(){
        return $this->spaceRepo->getAllByVisibility(1);
    }


    public function updateColors($requestBody){
        if(!isset($requestBody->spaceId)
        || !isset($requestBody->spaceProfileBgColor)
        || !isset($requestBody->spaceProfileFontColor)
        || !isset($requestBody->spaceBgColor)
        || !isset($requestBody->spaceBgFontColor)
    ){
        echo sendResponse(false, 400, "Missing required parameters.");
    }


    $spaceModel = new Space();
    $spaceModel->spaceId = $requestBody->spaceId;
    $spaceModel->spaceProfileBgColor = $requestBody->spaceProfileBgColor;
    $spaceModel->spaceProfileFontColor = $requestBody->spaceProfileFontColor;
    $spaceModel->spaceBgColor = $requestBody->spaceBgColor;
    $spaceModel->spaceBgFontColor = $requestBody->spaceBgFontColor;

    if($this->spaceRepo->updateColors($spaceModel)){
        echo sendResponse(true, 200, "Colors updated successfully.");
    }
    else{
        echo sendResponse(true, 500, "Internal Server Error. Could not update colors. Please try again.");
    }

    }

}