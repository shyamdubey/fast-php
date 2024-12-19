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
        return $this->questionImgMappingRepo->deleteById($id);
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



}