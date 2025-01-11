<?php

require_once __DIR__."/../repo/SpaceQuizMappingRepo.php";
require_once __DIR__."/../models/Quiz.php";
require_once __DIR__."/../functions.php";
require_once __DIR__."/SpaceService.php";

class SpaceQuizMappingService{

    public $spaceQuizMapRepo, $spaceService;

    public function __construct(){
        $this->spaceQuizMapRepo = new SpaceQuizMappingRepo();
        $this->spaceService = new SpaceService();
    }

    public function getAll(){
        return $this->spaceQuizMapRepo->getAll();
    }

    public function save($requestBody){
        $model = new Quiz();
        if(!isset($requestBody->quizId) || !isset($requestBody->spaceId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->quizId = $requestBody->quizId;
        $model->spaceId = $requestBody->spaceId;

        //get the space data
        $spaceModel = $this->spaceService->getById($model->spaceId);

        
        //if logged in user is not the owner of space. inform owner about a quiz mapping
        if($requestBody->userId != $spaceModel['userId']){
            saveNotification($spaceModel['userId'], $requestBody->userId, ' has mapped a quiz in your space '.$spaceModel['spaceName']. ' on MB Spaces', AppConstants::BASE_URL);
        }


        if($this->spaceQuizMapRepo->save($model)){
            return true;
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Something went wrong.");
        }

    }


    public function getAllByUserId($userId){
        return $this->spaceQuizMapRepo->getAllByUserId($userId);
    }

    public function getAllBySpaceId($spaceId){
        return $this->spaceQuizMapRepo->getAllBySpaceId($spaceId);
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
                //if the owner of space want to remove any user added by the mapped teacher he can do so
                //get the space data 
                $spaceData = $this->spaceService->getById($data['spaceId']);
                if(($loggedInUser->userId == $data['userId']) || ($spaceData != null && $spaceData['userId'] == $loggedInUser->userId) ){
                    return $this->spaceQuizMapRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }

            }
        }
    }

    public function getById($id){
        $result = $this->spaceQuizMapRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }

    public function myData(){
        $loggedInUserData = getLoggedInUserInfo();
        if($loggedInUserData != null){
            $loggedInUser = json_decode($loggedInUserData);
            return $this->getAllByUserId($loggedInUser->userId);
        }
        else{
            echo sendResponse(false, 500, "Could not loader user data. I think token has expired or malpracticed.");
        }
    }


    public function unMap($id){
        if($this->spaceQuizMapRepo->deleteById($id)){
            return 'Unmapped successfully.';
        }
        else{
            echo sendResponse(false, 500, "Internal Server Error. Could not unmap this mapping.");
        }
    }

    public function softDelete($id){
        if ($id != null) {
            $data = $this->getById($id);

            if ($data != null) {
                //if the owner of space want to remove any user added by the mapped teacher he can do so
                //get the space data 
                $spaceData = $this->spaceService->getById($data['spaceId']);
                $loggedInUser = getLoggedInUserInfo();
                if ( ($loggedInUser != null && $data['userId'] == $loggedInUser->userId) || ($spaceData != null && $loggedInUser->userId == $spaceData['userId']) ) {
                    if($this->spaceQuizMapRepo->softDelete($id, $loggedInUser->userId)){
                        sendResponse(true, 200, "Deleted successfully.");
                    }
                    else{
                        sendResponse(false, 500, "Something went wrong.");
                    }
                } else {
                    sendResponse(false, 403, "You are not authorized to delete.");
                }
            }
            else{
                sendResponse(false, 404, "Resource Not Found.");
            }
        }else{
            sendResponse(false, 400, "Invalid Request.");
        }
    }



}