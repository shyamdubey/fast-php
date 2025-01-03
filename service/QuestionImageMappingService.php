<?php

require_once __DIR__."/../repo/QuestionImageMappingRepo.php";
require_once __DIR__."/../models/QuestionImageMapping.php";
require_once __DIR__."/../functions.php";

class QuestionImageMappingService{

    public $questionImgMappingRepo;

    public function __construct(){
        $this->questionImgMappingRepo = new QuestionImageMappingRepo();
    }

    public function getAll(){
        return $this->questionImgMappingRepo->getAll();
    }

    public function save($requestBody){
        $model = new QuestionImageMapping();
        if(
            !isset($requestBody->questionId) ||
            !isset($requestBody->imageId) ||
            !isset($requestBody->userId)
        ){
            echo sendResponse(false, 400, "Bad Request. Missing Required Parameters");
        }

        $model->imageId = $requestBody->imageId;
        $model->questionId = $requestBody->questionId;
        $model->userId = $requestBody->userId;
        $this->questionImgMappingRepo->save($model);
        

    }


    public function getAllByQuestionId($questionId){
        return $this->questionImgMappingRepo->getAllByQuestionId($questionId);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            //get the saved data
            $data = $this->getById($id);
            if($data != null){
                if($loggedInUser->userId == $data['userId']){
                    return $this->questionImgMappingRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }

            }
        }
    }

    public function getById($id){
        $result = $this->questionImgMappingRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }

    public function softDelete($id){
        if($id != null){
            if($this->getById($id) != null){
                $loggedInUser = getLoggedInUserInfo();
                if($loggedInUser != null){
                    if($this->questionImgMappingRepo->softDelete($id, $loggedInUser->userId)){
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



}