<?php

require_once __DIR__."/../repo/QuizAttemptDetailedInfoRepo.php";
require_once __DIR__."/../models/QuizAttemptDetailedInfo.php";
require_once __DIR__."/../functions.php";

class QuizAttemptDetailedInfoService{

    public $quizAttemptDetailedInfoRepo;

    public function __construct(){
        $this->quizAttemptDetailedInfoRepo = new QuizAttemptDetailedInfoRepo();
    }

    public function getAll(){
        return $this->quizAttemptDetailedInfoRepo->getAll();
    }

    public function save($requestBody){
        $model = new QuizAttemptDetailedInfo();
        if(!isset($requestBody->quizId) || !isset($requestBody->quizAttemptId) || !isset($requestBody->questionId) || !isset($requestBody->userSelectedOption) || !isset($requestBody->isCorrect)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizId = $requestBody->quizId;
        $model->questionId = $requestBody->questionId;
        $model->userId = $requestBody->userId;
        $this->quizAttemptDetailedInfoRepo->save($model);
        

    }


    public function getByQuizId($quizId){
        return $this->quizAttemptDetailedInfoRepo->getAllByQuizId($quizId);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->quizAttemptDetailedInfoRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->quizAttemptDetailedInfoRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }



}