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
        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            //get the saved data
            $data = $this->getById($id);
            if($data != null){
                if($loggedInUser->userId == $data['userId']){
                    return $this->quizStudentMappingRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }

            }
        }
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


    public function bulkMapByEmail($requestBody){
        if(
            !isset($requestBody->studentsList) || !isset($requestBody->quizId) && count($requestBody->studentsList) < 1
        )
        {
            echo sendResponse(false, 400, 'Missing Required Parameters.');
        }
        $mappedCount = 0;
        $unMappedCount = 0;
        $studentsList = $requestBody->studentsList;
        foreach($studentsList as $student){
            $model = new QuizStudentMapping();
            $model->quizId = $requestBody->quizId;
            $model->studentId = $student->userId;
            $model->userId = $requestBody->userId;
            if($this->getByQuizIdAndStudentId($model->quizId, $model->studentId) == null){
                if($this->quizStudentMappingRepo->save($model)){
                    $mappedCount = $mappedCount + 1;
                }
            }
        }
        $unMappedCount = count($studentsList) - $mappedCount;
        if($unMappedCount == 0){
            sendResponse(true, 200, "Mapped successfully.");
        }
        else{
            sendResponse(true, 200, $mappedCount." Student(s) mapped successfully. Found ".$unMappedCount." student(s) already mapped with this space.");
        }
        
    }


    public function getByQuizIdAndStudentId($quizId, $userId){
        if($userId != null && $userId > 0 && $quizId != null){
            $this->quizStudentMappingRepo->getByQuizIdAndStudentId($quizId, $userId);
        }
        else{
            return null;
        }
    }

    public function softDelete($id){
        if($id != null){
            if($this->getById($id) != null){
                $loggedInUser = getLoggedInUserInfo();
                if($loggedInUser != null){
                    if($this->quizStudentMappingRepo->softDelete($id, $loggedInUser->userId)){
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