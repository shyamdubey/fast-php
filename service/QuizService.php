<?php

require_once __DIR__."/../repo/QuizRepo.php";
require_once __DIR__."/../models/Quiz.php";
require_once __DIR__."/../functions.php";

class QuizService{

    public $quizRepo;

    public function __construct(){
        $this->quizRepo = new QuizRepo();
    }

    public function getAll(){
        return $this->quizRepo->getAll();
    }

    public function save($requestBody){
        $model = new Quiz();
        if(!isset($requestBody->quizName) || !isset($requestBody->quizDescription) || !isset($requestBody->quizVisibility)|| !isset($requestBody->noOfQuestions) || !isset($requestBody->quizCategory)|| !isset($requestBody->userId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizName = $requestBody->quizName;
        $model->quizDescription = $requestBody->quizDescription;
        $model->quizVisibility = $requestBody->quizVisibility;
        $model->noOfQuestions = $requestBody->noOfQuestions;
        $model->userId = $requestBody->userId;
        $model->quizCategory = $requestBody->quizCategory;

        return $this->quizRepo->save($model);

    }


    public function getAllByUserId($userId){
        return $this->quizRepo->getAllByUserId($userId);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->quizRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->quizRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }



}