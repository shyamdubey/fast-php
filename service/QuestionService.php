<?php

require_once __DIR__ . "/../repo/QuestionRepo.php";
require_once __DIR__ . "/../repo/QuizQuestionRelationRepo.php";
require_once __DIR__ . "/../repo/QuestionImageMappingRepo.php";
require_once __DIR__ . "/../models/Question.php";
require_once __DIR__ . "/../functions.php";
require_once __DIR__ . "/../models/QuestionImageMapping.php";
require_once __DIR__ . "/QuestionImageMappingService.php";

class QuestionService
{

    public $questionRepo;
    public $quizQueRelRepo;
    public $questionImageMappingService;
    public $questionImageMappingRepo;

    public function __construct()
    {
        $this->questionRepo = new QuestionRepo();
        $this->quizQueRelRepo = new QuizQuestionRelationRepo();
        $this->questionImageMappingService = new QuestionImageMappingService();
        $this->questionImageMappingRepo = new QuestionImageMappingRepo();
    }

    public function getAll()
    {
        return $this->questionRepo->getAll();
    }

    public function save($requestBody)
    {
        $model = new Question();
        if (
            !isset($requestBody->question)
            || !isset($requestBody->option1)
            || !isset($requestBody->option2)
            || !isset($requestBody->option3)
            || !isset($requestBody->option4)
            || !isset($requestBody->correctAns)
            || !isset($requestBody->categoryId)
        ) {
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
        if (isset($requestBody->haveImages)) {
            $model->haveImages = $requestBody->haveImages;
        }
        $this->questionRepo->save($model);

        $imageArr = $requestBody->imageArr;
        if (count($imageArr) > 0) {
            $savedQuestion = $this->getLatestQuestionByUserId($model->userId);
            foreach ($imageArr as $img) {
                //get the latest question added
                if ($savedQuestion != null) {
                    $mapping = new QuestionImageMapping();
                    $mapping->questionId = $savedQuestion['questionId'];
                    $mapping->imageId = $img->fileUploadId;
                    $mapping->userId = $model->userId;
                    $this->questionImageMappingRepo->save($mapping);
                }
            }
        }

        echo sendResponse(true, 201, "Question Saved Successfully.");
    }


    public function getAllByUserId($userId)
    {
        return $this->questionRepo->getAllByUserId($userId);
    }

    public function myQuestions()
    {
        $loggedInUser = getLoggedInUserInfo();
        if ($loggedInUser != null) {
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }

    public function update($requestBody)
    {
        if (
            !isset($requestBody->question)
            || !isset($requestBody->option1)
            || !isset($requestBody->option2)
            || !isset($requestBody->option3)
            || !isset($requestBody->option4)
            || !isset($requestBody->correctAns)
            || !isset($requestBody->categoryId)
        ) {
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $loggedInUser = getLoggedInUserInfo();


        $savedQuestion = $this->getById($requestBody->questionId);

        //if not owner of the question
        if ($loggedInUser->userId != $savedQuestion['userId']) {
            sendResponse(false, 403, "Unauthorized Access!");
        }

        //get all fileupload id from the data received from UI to manage the question image mapping
        $imageIdArrayFromUI = [];

        if ($requestBody->haveImages == 1) {
            if ($requestBody->imageArr != null) {
                foreach ($requestBody->imageArr as $img) {
                    $imageIdArrayFromUI[] = $img->fileUploadId;
                }
            }
        }

        //check whether saved question has images
        $questionImageMappingArr = $this->questionImageMappingService->getAllByQuestionId($savedQuestion['questionId']);

        //get all image question mapping
        if (count($questionImageMappingArr) > 0) {
            $i = 0;
            foreach ($questionImageMappingArr as $mapping) {
                if (!in_array($mapping['fileUploadId'], $imageIdArrayFromUI)) {
                    //remove this mapping from db
                    $this->questionImageMappingRepo->deleteById($mapping['queImgMappingId']);

                    //delete from the array
                    array_slice($questionImageMappingArr, $i, 1);
                }
                $i++;
            }
        }
        $imageArr = $requestBody->imageArr;
        if (gettype($imageArr) == 'array' && count($imageArr) > 0) {
            foreach ($imageArr as $img) {
                //check whether the same mapping already exists
                $savedMapping = $this->questionImageMappingService->getByFileuploadIdAndQuestionId($img->fileUploadId, $savedQuestion['questionId']);
                if ($savedMapping == null) {
                    $mapping = new QuestionImageMapping();
                    $mapping->questionId = $savedQuestion['questionId'];
                    $mapping->imageId = $img->fileUploadId;
                    $mapping->userId = $savedQuestion['userId'];
                    $this->questionImageMappingService->save($mapping);
                }
            }
        }




        $model = new Question();
        $model->question = $requestBody->question;
        $model->option1 = $requestBody->option1;
        $model->option2 = $requestBody->option2;
        $model->option3 = $requestBody->option3;
        $model->option4 = $requestBody->option4;
        $model->correctAns = $requestBody->correctAns;
        $model->marks = $requestBody->marks;
        $model->categoryId = $requestBody->categoryId;
        $model->questionId = $requestBody->questionId;
        $model->haveImages = $requestBody->haveImages;
        if ($this->questionRepo->update($model)) {
            sendResponse(true, 200, "Question Updated Successfully");
        } else {
            sendResponse(false, 500, "Internal Server Error. Please try again.");
        }
    }

    public function deleteById($id)
    {


        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if ($loggedInUser != null) {
            //get the saved data
            $data = $this->getById($id);
            if ($data != null) {
                if ($loggedInUser->userId == $data['userId']) {
                    //if mapping exists with images then we have to delete those mappings as well 
                    $question = $this->getById($id);
                    if ($question != null) {
                        if ($question['haveImages'] == 1) {
                            //get the mappings
                            $questionImageMappingService = new QuestionImageMappingService();
                            $questionImageMappings = $questionImageMappingService->getAllByQuestionId($question['questionId']);
                            foreach ($questionImageMappings as $mapping) {
                                $questionImageMappingService->deleteById($mapping['queImgMappingId']);
                            }
                        }
                    }
                    return $this->questionRepo->deleteById($id);
                } else {
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }
            }
        }
    }

    public function getById($id)
    {
        $result = $this->questionRepo->getById($id);
        if ($result != null) {
            return $result;
        } else {
            echo sendResponse(false, 404, "Not Found");
        }
    }

    private function getLatestQuestionByUserId($userId)
    {
        return $this->questionRepo->getLatestQuestionByUserId($userId);
    }

    public function getQuestionsByCategoryIdAndUserId($categoryId)
    {
        $loggedInUser = getLoggedInUserInfo();
        if ($loggedInUser != null) {
            return $this->questionRepo->getQuestionsByCategoryIdAndUserId($categoryId, $loggedInUser->userId);
        }
    }

    public function softDelete($id)
    {
        if ($id != null) {
            $data = $this->getById($id);
            if ($data != null) {
                $loggedInUser = getLoggedInUserInfo();
                if ($loggedInUser != null && $data['userId'] == $loggedInUser->userId) {
                    if ($this->questionRepo->softDelete($id, $loggedInUser->userId)) {
                        sendResponse(true, 200, "Deleted successfully.");
                    } else {
                        sendResponse(false, 500, "Something went wrong.");
                    }
                } else {
                    sendResponse(false, 403, "Access Forbidden.");
                }
            }
        }
    }
}
