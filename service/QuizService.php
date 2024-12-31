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


        //if we get space in request body we will do space quiz mapping
        if(isset($requestBody->spaces)){
            //get the latest quiz created by this user
            $loggedInUser = getLoggedInUserInfo();
            $loggedInUserData = json_decode($loggedInUser);
            $quiz = $this->quizRepo->getTopByUserId($loggedInUserData->userId);

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