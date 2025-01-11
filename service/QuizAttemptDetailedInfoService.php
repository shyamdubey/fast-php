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
        if(!isset($requestBody->quizAttemptId) || !isset($requestBody->questionId) || !isset($requestBody->userSelectedOption) || !isset($requestBody->isCorrect)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->questionId = $requestBody->questionId;
        $model->userId = $requestBody->userId;
        $model->quizAttemptId = $requestBody->quizAttemptId;
        $model->isCorrect = $requestBody->isCorrect;
        $model->userSelectedOption = $requestBody->userSelectedOption;
        $this->quizAttemptDetailedInfoRepo->save($model);
        

    }


    public function getByQuizAttemptId($quizAttemptId){
        return $this->quizAttemptDetailedInfoRepo->getAllByQuizAttemptId($quizAttemptId);
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

    public function softDelete($id){
        if($id != null){
            $data = $this->getById($id);
            if($data != null){
                $loggedInUser = getLoggedInUserInfo();
                if($loggedInUser != null && $data['userId'] == $loggedInUser->userId){
                    if($this->quizAttemptDetailedInfoRepo->softDelete($id, $loggedInUser->userId)){
                        sendResponse(true, 200, "Deleted Successfully.");
                    }
                    else{
                        sendResponse(false, 500, "Something went wrong.");
                    }
                }
                else{
                    sendResponse(false, 403, "Access Forbidden.");
                }
            }
        }
    }






}