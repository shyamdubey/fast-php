<?php

require_once __DIR__."/../repo/QuestionRepo.php";
require_once __DIR__."/../models/Question.php";
require_once __DIR__."/../functions.php";

class QuestionService{

    public $questionRepo;

    public function __construct(){
        $this->questionRepo = new QuestionRepo();
    }

    public function getAll(){
        return $this->questionRepo->getAll();
    }

    public function save($requestBody){
        $model = new Question();
        if(!isset($requestBody->question) 
        || !isset($requestBody->option1) 
    || !isset($requestBody->option2) 
    || !isset($requestBody->option3) 
    || !isset($requestBody->option4) 
    || !isset($requestBody->correctAns)
    || !isset($requestBody->categoryId)
    ){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->question = $requestBody->question;
        $model->option1 = $requestBody->option1;
        $model->option2 = $requestBody->option2;
        $model->option3 = $requestBody->option3;
        $model->option4 = $requestBody->option4;
        $model->correctAns = $requestBody->correctAns;
        $model->marks = $requestBody->marks;
        $model->categoryId = $requestBody->categoryId;
        $model->userId = $requestBody->userId;

        //check whether have images
        if(isset($requestBody->haveImages)){
            $model->haveImages = $requestBody->haveImages;
        }

        
        $this->questionRepo->save($model);
        

    }


    public function getAllByUserId($userId){
        return $this->questionRepo->getAllByUserId($userId);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->questionRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->questionRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }



}