<?php

require_once __DIR__."/../repo/SpaceQuizMappingRepo.php";
require_once __DIR__."/../models/Quiz.php";
require_once __DIR__."/../functions.php";

class SpaceQuizMappingService{

    public $spaceQuizMapRepo;

    public function __construct(){
        $this->spaceQuizMapRepo = new SpaceQuizMappingRepo();
    }

    public function getAll(){
        return $this->spaceQuizMapRepo->getAll();
    }

    public function save($requestBody){
        $model = new Quiz();
        if(!isset($requestBody->quizId) || !isset($requestBody->spaceId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizId = $requestBody->quizId;
        $model->spaceId = $requestBody->spaceId;

        if($this->spaceQuizMapRepo->save($model)){
            return true;
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Something went wrong.");
        }

    }


    public function getAllByUserId($userId){
        return $this->spaceQuizMapRepo->getAllByUserId($userId);
    }

    public function getAllBySpaceId($spaceId){
        return $this->spaceQuizMapRepo->getAllBySpaceId($spaceId);
    }



    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->spaceQuizMapRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->spaceQuizMapRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }

    public function myData(){
        $loggedInUserData = getLoggedInUserInfo();
        if($loggedInUserData != null){
            $loggedInUser = json_decode($loggedInUserData);
            return $this->getAllByUserId($loggedInUser->userId);
        }
        else{
            echo sendResponse(false, 500, "Could not loader user data. I think token has expired or malpracticed.");
        }
    }


    public function unMap($id){
        if($this->spaceQuizMapRepo->deleteById($id)){
            return 'Unmapped successfully.';
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Could not unmap this mapping.");
        }
    }



}