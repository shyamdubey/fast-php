<?php
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../repo/QuizAttemptRepo.php";
require_once __DIR__."/../repo/QuizAttemptDetailedInfoRepo.php";
require_once __DIR__."/../models/QuizAttempt.php";
require_once __DIR__."/../functions.php";
require_once __DIR__."/QuestionService.php";
require_once __DIR__."/QuizQuestionRelationService.php";
require_once __DIR__."/QuizAttemptDetailedInfoService.php";
require_once __DIR__."/QuizService.php";
require_once __DIR__."/../models/QuizAttemptDetailedInfo.php";

class QuizAttemptService{

    public $quizAttemptRepo;
    public $questionService;
    public $quizAttemptDetailedInfoService;
    private $quizQuestionRelationService;
    private $quizService;
    public $now;

    public function __construct(){
        global $now;
        $this->quizAttemptRepo = new QuizAttemptRepo();
        $this->questionService = new QuestionService();
        $this->quizQuestionRelationService = new QuizQuestionRelationService();
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
        ////get Quiz Total Questions
        $noOfQuestions = $this->quizQuestionRelationService->getMappedQuestionsCountByQuizId($quiz['quizId']);
        

        $model->quizId = $requestBody->quizId;
        $model->marks = $marks;
        $model->noOfQuestions = $noOfQuestions;
        $model->endTime = $endTime;
        $model->startTime = $requestBody->startTime;
        $model->attemptedQuestions = count($requestBody->attemptedQuestions);
        $model->quizAttemptUserId = $requestBody->userId;
        $this->quizAttemptRepo->save($model);

        //save the analytics to detailed info
        $savedData = $this->quizAttemptRepo->getLastestByUserId($model->quizAttemptUserId);
        if($savedData != null){
            $quizAttemptId = $savedData['quizAttemptId'];
            foreach($attemptedQuestions as $que){
                $quizAttemptDetailedInfo = new QuizAttemptDetailedInfo();
                $question = $this->questionService->getById($que->questionId);
                if($question != null){
                    $isCorrect = $question['correctAns'] == $que->optionSelected ? 1 : 0;
                    $quizAttemptDetailedInfo = new QuizAttemptDetailedInfo();
                    $quizAttemptDetailedInfo->questionId = $question['questionId'];
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
        $response->total = $savedData['noOfQuestions'];

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
        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            //get the saved data
            $data = $this->getById($id);
            if($data != null){
                if($loggedInUser->userId == $data['userId']){
                    return $this->quizAttemptRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }

            }
        }
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


    public function softDelete($id){
        if($id != null){
            if($this->getById($id) != null){
                $loggedInUser = getLoggedInUserInfo();
                if($loggedInUser != null){
                    if($this->quizAttemptRepo->softDelete($id, $loggedInUser->userId)){
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