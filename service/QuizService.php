<?php

require_once __DIR__."/../repo/QuizRepo.php";
require_once __DIR__."/../service/SpaceQuizMappingService.php";
require_once __DIR__."/../models/Quiz.php";
require_once __DIR__."/../models/SpaceQuizMapping.php";
require_once __DIR__."/../functions.php";

class QuizService{

    public $quizRepo, $spaceQuizMappingService;

    public function __construct(){
        $this->quizRepo = new QuizRepo();
        $this->spaceQuizMappingService = new SpaceQuizMappingService;
    }

    public function getAll(){
        return $this->quizRepo->getAll();
    }

    public function save($requestBody){
        $model = new Quiz();
        if(!isset($requestBody->quizName) || !isset($requestBody->quizDescription) || !isset($requestBody->quizVisibility) || !isset($requestBody->userId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizName = $requestBody->quizName;
        $model->quizDescription = $requestBody->quizDescription;
        $model->quizVisibility = $requestBody->quizVisibility;
        $model->userId = $requestBody->userId;
        $model->quizJoinCode = generateSpaceJoinCode();


        //if we get space in request body we will do space quiz mapping
        if(isset($requestBody->spaces)){
            //get the latest quiz created by this user
            $loggedInUser = getLoggedInUserInfo();
            $quiz = $this->quizRepo->getTopByUserId($loggedInUser->userId);

            $spaces = $requestBody->spaces;
            if(count($spaces)>0){
                foreach($spaces as $space){
                    $spaceQuizMappingModel = new SpaceQuizMapping();
                    $spaceQuizMappingModel->quizId = $quiz['quizId'];
                    $spaceQuizMappingModel->spaceId = $space;
                    $this->spaceQuizMappingService->save($spaceQuizMappingModel);
                }
            }
        }

        if($this->quizRepo->save($model)){
            return true;
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Something went wrong.");
        }

    }


    public function myQuizzes(){
        $quizList = [];
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            $quizList = $this->getAllByUserId($loggedInUser->userId);
        }
        return $quizList;
    }
    public function getAllByUserId($userId){
        return $this->quizRepo->getAllByUserId($userId);
    }

    public function getPublicQuiz(){
        return $this->quizRepo->getAllByVisibility(1);
    }

    public function getBySpaceNotMappedData($spaceId){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->quizRepo->getAllQuizzesWhichAreNotMappedToSpaceIdAndUserId($spaceId, $loggedInUser->userId);
        }
    }

    public function getPrivateQuiz(){
        return $this->quizRepo->getAllByVisibility(0);
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
                    return $this->quizRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }

            }
        }
    }

    public function getById($id){
        $result = $this->quizRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Data not found for this id.");
        }
    }

    public function getByJoinCode($code){
        $result = $this->quizRepo->getByJoinCode($code);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Quiz not found for this code.");
        }
    }

    public function softDelete($id){
        if($id != null){
            if($this->getById($id) != null){
                $loggedInUser = getLoggedInUserInfo();
                if($loggedInUser != null){
                    if($this->quizRepo->softDelete($id, $loggedInUser->userId)){
                        sendResponse(true, 200, "Deleted successfully");
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