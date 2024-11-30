<?php
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../repo/QuizAttemptRepo.php";
require_once __DIR__."/../repo/QuizAttemptDetailedInfoRepo.php";
require_once __DIR__."/../models/QuizAttempt.php";
require_once __DIR__."/../functions.php";
require_once __DIR__."/QuestionService.php";
require_once __DIR__."/../models/QuizAttemptDetailedInfo.php";

class QuizAttemptService{

    public $quizAttemptRepo;
    public $questionService;
    public $quizAttemptDetailedInfoRepo;
    public $now;

    public function __construct(){
        global $now;
        $this->quizAttemptRepo = new QuizAttemptRepo();
        $this->questionService = new QuestionService();
        $this->quizAttemptDetailedInfoRepo = new QuizAttemptDetailedInfoRepo();
        $this->now = $now;
    }

    public function getAll(){
        return $this->quizAttemptRepo->getAll();
    }

    public function save($requestBody){
        $model = new QuizAttempt();
        if(!isset($requestBody->quizId) || !isset($requestBody->startTime)
        || !isset($requestBody->endTime)
        || !isset($requestBody->attemptedQuestions)
        || !isset($requestBody->userId)
        || !isset($requestBody->marks)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizId = $requestBody->quizId;
        $model->marks = $requestBody->marks;
        $model->endTime = $requestBody->endTime;
        $model->startTime = $requestBody->startTime;
        $model->attemptedQuestions = $requestBody->attemptedQuestions;
        $model->userId = $requestBody->userId;
        $this->quizAttemptRepo->save($model);
        

    }


    public function getByQuizId($quizId){
        return $this->quizAttemptRepo->getAllByQuizId($quizId);
    }

    public function getByUserId($userId){
        return $this->quizAttemptRepo->getAllByUserId($userId);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->quizAttemptRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->quizAttemptRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }


    public function getByToken(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser == null){
            echo sendResponse(false, 401, "Unauthorized access.");
        }

        $loggedInUser = json_decode($loggedInUser);
        return $this->getByUserId($loggedInUser->userId);

    }


    public function calculateQuizAttempt($requestBody){
        $marks = 0;
        $quizId = $requestBody->quizId;
        $quizAttemptId = $requestBody->quizAttemptId;
        $attemptedQuestions = count($requestBody->attemptData);

        //get logged in user
        $loggedInUser =  getLoggedInUserInfo();
        foreach($requestBody->attemptData as $rd){

            $isCorrect = 0;
            if(!isset($rd->qid) || !isset($rd->selectedOption)){
                echo sendResponse(false, 400, "Missing Required parameters.");
            }

            $question = $this->questionService->getById($rd->qid);
            if($question['correctAns'] == $rd->selectedOption){
                $marks = $marks + $question['marks'];
                $isCorrect = 1;
            }

            //Save the detailed information
            $questionAttemptDetailedInfoModel = new QuizAttemptDetailedInfo();
            $questionAttemptDetailedInfoModel->questionId = $rd->qid;
            $questionAttemptDetailedInfoModel->quizId = $quizId;
            $questionAttemptDetailedInfoModel->userSelectedOption = $rd->selectedOption;
            $questionAttemptDetailedInfoModel->isCorrect = $isCorrect;
            $questionAttemptDetailedInfoModel->quizAttemptId = $quizAttemptId;
            if($loggedInUser != null){
                $loggedInUserData = json_decode($loggedInUser);
                $questionAttemptDetailedInfoModel->userId = $loggedInUserData->userId;
            }
            else{
                echo sendResponse(false, 400, "Internal Server Error. Could not load user data.");
            }
            $this->quizAttemptDetailedInfoRepo->save($questionAttemptDetailedInfoModel);

        }

        $quizAttemptAlreadySavedData = $this->getById($quizAttemptId);
        if($quizAttemptAlreadySavedData != null){
            //convert to model
            $quizAttempt = new QuizAttempt();
            $quizAttempt->quizAttemptId = $quizAttemptId;
            $quizAttempt->marks = $marks;
            $quizAttempt->attemptedQuestions = $attemptedQuestions;
            $quizAttempt->endTime = $this->now;
            if($this->quizAttemptRepo->update($quizAttempt)){
                echo sendResponse(true, 200, "Quiz Attempt Data Saved.");
            }
            else{
                echo sendResponse(false, 500, "Internal Server Error.");

            }
            
        }
    }


    function startQuizAttempt($requestBody){

        if(!isset($requestBody->quizId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }
        $quizAttempt = new QuizAttempt();
        $quizAttempt->startTime = $this->now;
        $quizAttempt->marks = 0;
        $quizAttempt->attemptedQuestions = 0;
        $quizAttempt->endTime = '';
        $quizAttempt->quizId = $requestBody->quizId;
        $userData = getLoggedInUserInfo();
        if($userData != null){
            $userData =  json_decode($userData);
            $quizAttempt->userId = $userData->userId;
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Could not load user data.");
        }

        if($this->quizAttemptRepo->save($quizAttempt)){
            echo sendResponse(true, 201, "Quiz Attempt Started.");

        }
        else{
            echo sendResponse(true, 500, "Quiz Attempt failed to start. Internal Server Error.");
        }

        
    }



}