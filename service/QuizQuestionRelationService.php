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


    public function getByQuestionId($questionId){
        if($questionId != null){
            return $this->quizQueRelRepo->findAllByQuestionId($questionId);
        }
    }

    public function deleteById($id){
        
        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            //get the saved data
            $data = $this->getById($id);
            if($data != null){
                if($loggedInUser->userId == $data['userId']){
                    return $this->quizQueRelRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }

            }
        }
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

    function getNotMappedQuestionsByQuizIdAndCategoryId($requestBody){
        if(
            !isset($requestBody->quizId) ||
            !isset($requestBody->categoryId)
        ){
            sendResponse(false, 400, "Missing required parameters");
        }
        $quizId = $requestBody->quizId;
        $categoryId = $requestBody->categoryId;


        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->questionRepo->getQuestionsWhichAreNotMappedInQuizByCategoryId($quizId, $loggedInUser->userId, $categoryId);
        }
    }


    public function getMappedQuestionsCountByQuizId($quizId){
        if($quizId != null){
            $row = $this->quizQueRelRepo->getMappedQuestionsCountByQuizId($quizId);
            return $row['noOfQuestions'];
        }
        return null;
    }

    public function softDelete($id){
        if($id != null){
            if($this->getById($id) != null){
                $loggedInUser = getLoggedInUserInfo();
                if($loggedInUser != null){
                    if($this->quizQueRelRepo->softDelete($id, $loggedInUser->userId)){
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