<?php

require_once __DIR__."/../repo/QuestionRepo.php";
require_once __DIR__."/../models/Question.php";
require_once __DIR__."/../functions.php";
require_once __DIR__."/../models/QuestionImageMapping.php";
require_once __DIR__."/QuestionImageMappingService.php";

class QuestionService{

    public $questionRepo;

    public function __construct(){
        $this->questionRepo = new QuestionRepo();
    }

    public function getAll(){
        return $this->questionRepo->getAll();
    }

    public function save($requestBody){
        $model = new Question();
        if(!isset($requestBody->question) 
        || !isset($requestBody->option1) 
    || !isset($requestBody->option2) 
    || !isset($requestBody->option3) 
    || !isset($requestBody->option4) 
    || !isset($requestBody->correctAns)
    || !isset($requestBody->categoryId)
    ){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->question = $requestBody->question;
        $model->option1 = $requestBody->option1;
        $model->option2 = $requestBody->option2;
        $model->option3 = $requestBody->option3;
        $model->option4 = $requestBody->option4;
        $model->correctAns = $requestBody->correctAns;
        $model->marks = $requestBody->marks;
        $model->categoryId = $requestBody->categoryId;
        $model->userId = $requestBody->userId;
        //check whether have images
        if(isset($requestBody->haveImages)){
            $model->haveImages = $requestBody->haveImages; 
        }
        $this->questionRepo->save($model);

        $imageArr = $requestBody->imageArr;
        if(count($imageArr) > 0){
            $savedQuestion = $this->getLatestQuestionByUserId($model->userId);
            foreach($imageArr as $img){
                //get the latest question added
                if($savedQuestion != null){
                    $mapping = new QuestionImageMapping();
                    $mapping->questionId = $savedQuestion['questionId'];
                    $mapping->imageId = $img->fileUploadId;
                    $mapping->userId = $model->userId;
                    $questionImageMappingService = new QuestionImageMappingService();
                    $questionImageMappingService->save($mapping);
                }
                    
            }
        }
        
        echo sendResponse(true, 201, "Question Saved Successfully.");
    }


    public function getAllByUserId($userId){
        return $this->questionRepo->getAllByUserId($userId);
    }

    public function myQuestions(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }

    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        //if mapping exists with images then we have to delete those mappings as well 
        $question = $this->getById($id);
        if($question != null){
            if($question['haveImages'] == 1){
                //get the mappings
                $questionImageMappingService = new QuestionImageMappingService();
                $questionImageMappings = $questionImageMappingService->getAllByQuestionId($question['questionId']);
                foreach($questionImageMappings as $mapping){
                    $questionImageMappingService->deleteById($mapping['queImgMappingId']);
                }
            }
        }
        return $this->questionRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->questionRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }

    private function getLatestQuestionByUserId($userId){
            return $this->questionRepo->getLatestQuestionByUserId($userId);
    }



}