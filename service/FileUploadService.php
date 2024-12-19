<?php

require_once __DIR__."/../repo/FileUploadRepo.php";
require_once __DIR__."/../models/FileUpload.php";
require_once __DIR__."/../functions.php";

class FileUploadService{

    public $fileUploadRepo;

    public function __construct(){
        $this->fileUploadRepo = new FileUploadRepo();
    }

    public function getAll(){
        return $this->fileUploadRepo->getAll();
    }

    public function save($requestBody){
        $model = new FileUpload();
        if(!isset($requestBody->purpose) 
        || !isset($requestBody->fileUrl)
        || !isset($requestBody->userId)
    ){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->purpose = $requestBody->purpose;
        $model->isPublic = $requestBody->isPublic;
        $model->fileUrl = $requestBody->fileUrl;
        $model->userId = $requestBody->userId;
        $this->fileUploadRepo->save($model);
        

    }



    public function upload($requestBody){
        if (isset($_FILES['images'])) {
            $purpose = htmlentities($_POST['purpose']);
            $file = $_FILES['images'];
            try {
                if (uploadFile($file, $purpose, $requestBody->userId)) {
                    echo sendResponse(true, 201, "Image uploaded successfully");
                } else {
                    echo sendResponse(false, 500, "Internal Server Error. Something went wrong.");
                }
            } catch (Exception $e) {
                echo sendResponse(false, 500, $e->getMessage());
            }
        }
    }


    public function getAllByUserId($userId){
        return $this->fileUploadRepo->getAllByUserId($userId);
    }

    public function myFiles(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->getAllByUserIdAndPurpose($loggedInUser->userId, 'files');
        }
    }

    public function filterByQuestions(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->getAllByUserIdAndPurpose($loggedInUser->userId, 'questions');
        }
    }

    public function filterByPurpose($purpose){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->getAllByUserIdAndPurpose($loggedInUser->userId, $purpose);
        }
    }


    public function getAllByUserIdAndPurpose($userId, $purpose){
        return $this->fileUploadRepo->getAllByUserIdAndPurpose($userId, $purpose);
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function deleteById($id){
        //delete file from the disk
        $fileModel = $this->getById($id);
        if($fileModel != null){
            $fileUrl = $fileModel['fileUrl'];
            $fileUrlArr = explode("/", $fileUrl);
            $fileName = $fileUrlArr[count($fileUrlArr)-1];
            try{
                unlink(__DIR__."/../images/".$fileModel['purpose']."/".$fileName);
            }
            catch(Exception $e){
                sendResponse(false, 500, "Could not remove file from disk. Please try again or report to admin.");
            }

        }
        return $this->fileUploadRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->fileUploadRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }



}