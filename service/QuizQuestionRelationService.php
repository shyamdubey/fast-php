<?php

require_once __DIR__."/../repo/QuizQuestionRelationRepo.php";
require_once __DIR__."/../repo/QuestionRepo.php";
require_once __DIR__."/../models/QuizQuestionRelation.php";
require_once __DIR__."/../functions.php";

class QuizQuestionRelationService{

    private $quizQueRelRepo;
    private $questionRepo;

    public function __construct(){
        $this->quizQueRelRepo = new QuizQuestionRelationRepo();
        $this->questionRepo = new QuestionRepo();
    }

    public function getAll(){
        return $this->quizQueRelRepo->getAll();
    }

    public function save($requestBody){
        $model = new QuizQuestionRelation();
        if(!isset($requestBody->quizId) || !isset($requestBody->questionId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizId = $requestBody->quizId;
        $model->questionId = $requestBody->questionId;
        $model->userId = $requestBody->userId;
        $this->quizQueRelRepo->save($model);
        

    }


    public function getByQuizId($quizId){
        return $this->quizQueRelRepo->getAllByQuizId($quizId);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        return $this->quizQueRelRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->quizQueRelRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }


    public function getNotMappedQuestions($quizId){
        $questionList = [];
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            $questionList = $this->questionRepo->getQuestionsWhichAreNotMappedInQuiz($quizId, $loggedInUser->userId);
            
        }
        return $questionList;

    }

}