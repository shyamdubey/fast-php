<?php
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../repo/QuizAttemptRepo.php";
require_once __DIR__."/../repo/QuizAttemptDetailedInfoRepo.php";
require_once __DIR__."/../models/QuizAttempt.php";
require_once __DIR__."/../functions.php";
require_once __DIR__."/QuestionService.php";
require_once __DIR__."/QuizAttemptDetailedInfoService.php";
require_once __DIR__."/QuizService.php";
require_once __DIR__."/../models/QuizAttemptDetailedInfo.php";

class QuizAttemptService{

    public $quizAttemptRepo;
    public $questionService;
    public $quizAttemptDetailedInfoService;
    private $quizService;
    public $now;

    public function __construct(){
        global $now;
        $this->quizAttemptRepo = new QuizAttemptRepo();
        $this->questionService = new QuestionService();
        $this->quizService = new QuizService();
        $this->quizAttemptDetailedInfoService = new QuizAttemptDetailedInfoService();
        $this->now = $now;
    }

    public function getAll(){
        return $this->quizAttemptRepo->getAll();
    }

    public function save($requestBody){
        $model = new QuizAttempt();
        $correct = 0;
        $totalMarks = 0;
        $incorrect = 0;
        if(!isset($requestBody->attemptedQuestions) 
        || !isset($requestBody->startTime)
        || !isset($requestBody->quizId)
        || !isset($requestBody->userId)
        ){
            echo sendResponse(false, 400, "Missing required parameters.");
        }
        $marks = 0;
        //get the quiz data 
        $quiz = $this->quizService->getById($requestBody->quizId);

        //calculate the marks
        $attemptedQuestions = $requestBody->attemptedQuestions;
        foreach($attemptedQuestions as $que){
            $question = $this->questionService->getById($que->questionId);
            if($question != null){
                $totalMarks = $totalMarks + $question['marks'];
                if($question['correctAns'] === $que->optionSelected){
                    $marks = $marks + $question['marks'];
                    $correct = $correct + 1;
                }
            }
        }

        $endTime = $this->now;
        $incorrect = count($requestBody->attemptedQuestions) - $correct;
        //

        $model->quizId = $requestBody->quizId;
        $model->marks = $marks;
        $model->endTime = $endTime;
        $model->startTime = $requestBody->startTime;
        $model->attemptedQuestions = count($requestBody->attemptedQuestions);
        $model->userId = $requestBody->userId;
        $this->quizAttemptRepo->save($model);

        //save the analytics to detailed info
        $savedData = $this->quizAttemptRepo->getLastestByUserId($model->userId);
        if($savedData != null){
            $quizAttemptId = $savedData['quizAttemptId'];
            foreach($attemptedQuestions as $que){
                $quizAttemptDetailedInfo = new QuizAttemptDetailedInfo();
                $question = $this->questionService->getById($que->questionId);
                if($question != null){
                    $isCorrect = $question['correctAns'] == $que->optionSelected ? 1 : 0;
                    $quizAttemptDetailedInfo = new QuizAttemptDetailedInfo();
                    $quizAttemptDetailedInfo->questionId = $question['questionId'];
                    $quizAttemptDetailedInfo->quizId = $requestBody->quizId;
                    $quizAttemptDetailedInfo->userSelectedOption = $que->optionSelected;
                    $quizAttemptDetailedInfo->isCorrect = $isCorrect;
                    $quizAttemptDetailedInfo->userId = $requestBody->userId;
                    $quizAttemptDetailedInfo->quizAttemptId = $quizAttemptId;
                    $this->quizAttemptDetailedInfoService->save($quizAttemptDetailedInfo);
                }
                
            }
        }
        $response = new stdClass();
        $response->attemptedQuestions = count($requestBody->attemptedQuestions);
        $response->correct = $correct;
        $response->marks = $marks;
        $response->totalMarks = $totalMarks;
        $response->incorrect = $incorrect;
        $response->total =$quiz['noOfQuestions'];

        echo sendResponse(true, 200, $response);
        

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
        return $this->getByUserId($loggedInUser->userId);

    }


}