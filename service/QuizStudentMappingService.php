<?php

require_once __DIR__."/../repo/QuizStudentMappingRepo.php";
require_once __DIR__."/../models/QuizStudentMapping.php";
require_once __DIR__."/../functions.php";

class QuizStudentMappingService{

    private $quizStudentMappingRepo;

    public function __construct(){
        $this->quizStudentMappingRepo = new QuizStudentMappingRepo();
    }

    public function getAll(){
        return $this->quizStudentMappingRepo->getAll();
    }

    public function save($requestBody){
        $model = new QuizStudentMapping();
        if(!isset($requestBody->quizId) || !isset($requestBody->studentId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizId = $requestBody->quizId;
        $model->studentId = $requestBody->studentId;
        $model->userId = $requestBody->userId;
        $this->quizStudentMappingRepo->save($model);
        

    }


    public function mapByEmail($requestBody){
        $model = new QuizStudentMapping();
        if(
            !isset($requestBody->quizId) ||
            !isset($requestBody->email)
        )
        {
            echo sendResponse(false, 400, 'Missing Required Parameters.');
        }
        //get user by email
        $user = getUsersByEmail($requestBody->email);
        if($user != null){
            $model->quizId = $requestBody->quizId;
            $model->studentId = $user->userId;
            $model->userId = $requestBody->userId;
            if($this->quizStudentMappingRepo->save($model)){
                echo sendResponse(true, 201, "Mapped successfully");
            }
        }
        else{
            echo sendResponse(false, 404, 'User not found.');
        }
    }

    public function getByQuizId($quizId){
        return $this->quizStudentMappingRepo->getAllByQuizId($quizId);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->quizStudentMappingRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->quizStudentMappingRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }


}