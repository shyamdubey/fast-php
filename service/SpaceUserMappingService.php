<?php

require_once __DIR__ . "/../repo/SpaceUserMappingRepo.php";
require_once __DIR__ . "/../models/Category.php";
require_once __DIR__ . "/../functions.php";
require_once __DIR__ . "/SpaceService.php";

class SpaceUserMappingService
{

    public $spaceUserMappingRepo;
    private $spaceService;
    public function __construct()
    {
        $this->spaceUserMappingRepo = new SpaceUserMappingRepo();
        $this->spaceService = new SpaceService();
    }

    public function getAll()
    {
        $data = $this->spaceUserMappingRepo->getAll();
        $dataWithUserDetails = [];
        foreach ($data as $d) {
            $d['user'] = getUserById($d['studentId']);
            $dataWithUserDetails[] = $d;
        }
        return $dataWithUserDetails;
    }

    public function save($requestBody)
    {
        $model = new SpaceUserMapping();
        if (
            !isset($requestBody->spaceId)
            || !isset($requestBody->studentId)
        ) {
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->spaceId = $requestBody->spaceId;
        $model->studentId = $requestBody->studentId;
        $model->userId = $requestBody->userId;
        if ($this->spaceUserMappingRepo->save($model)) {
            sendResponse(true, 201, "Mapped Successfully.");
        } else {
            sendResponse(false, 500, "Internal Server Error. Please Try Again Or Report to Administrator.");
        }
    }


    public function bulkMapByEmail($requestBody)
    {
        if (
            !isset($requestBody->studentsList) || !isset($requestBody->spaceId) && count($requestBody->studentsList) < 1
        ) {
            echo sendResponse(false, 400, 'Missing Required Parameters.');
        }
        $mappedCount = 0;
        $unMappedCount = 0;
        $studentsList = $requestBody->studentsList;
        foreach ($studentsList as $student) {
            $model = new SpaceUserMapping();
            $model->spaceId = $requestBody->spaceId;
            $model->studentId = $student->userId;
            $model->userId = $requestBody->userId;
            if ($this->getByStudentIdAndSpaceId($model->studentId, $model->spaceId) == null) {
                if ($this->spaceUserMappingRepo->save($model)) {
                    //save notification
                    saveNotification($model->studentId, $requestBody->userId, ' mapped you in a space on MCQ Buddy Space.', AppConstants::BASE_URL);
                    
                    $mappedCount = $mappedCount + 1;
                }
            }
        }
        $unMappedCount = count($studentsList) - $mappedCount;
        if ($unMappedCount == 0) {
            sendResponse(true, 200, "Mapped successfully.");
        } else {
            sendResponse(true, 200, $mappedCount . " Student(s) mapped successfully. Found " . $unMappedCount . " student(s) already mapped with this space.");
        }
    }


    public function mapByEmail($requestBody)
    {
        $model = new SpaceUserMapping();
        if (
            !isset($requestBody->spaceId) ||
            !isset($requestBody->email)
        ) {
            echo sendResponse(false, 400, 'Missing Required Parameters.');
        }
        //get user by email
        $user = getUsersByEmail($requestBody->email);
        if ($user != null) {
            $model->spaceId = $requestBody->spaceId;
            $model->studentId = $user->userId;
            $model->userId = $requestBody->userId;
            if ($this->getByStudentIdAndSpaceId($model->studentId, $model->spaceId) == null) {
                if ($this->spaceUserMappingRepo->save($model)) {
                    //save notification
                    saveNotification($model->studentId, $requestBody->userId, ' mapped you in a space on MCQ Buddy Space.', AppConstants::BASE_URL);
                    echo sendResponse(true, 201, "Mapped successfully");
                }
            } else {
                echo sendResponse(false, 500, "Student Already Mapped.");
            }
        } else {
            echo sendResponse(false, 404, 'User not found.');
        }
    }


    public function getByStudentIdAndSpaceId($studentId, $spaceId)
    {
        return $this->spaceUserMappingRepo->getByStudentIdAndSpaceId($studentId, $spaceId);
    }

    public function getAllByUserId($userId)
    {
        return $this->spaceUserMappingRepo->getAllByUserId($userId);
    }

    public function getAllBySpaceId($spaceId)
    {
        $data = $this->spaceUserMappingRepo->getAllBySpaceId($spaceId);
        $dataWithUserDetails = [];
        foreach ($data as $d) {
            $d['user'] = getUserById($d['studentId']);
            $dataWithUserDetails[] = $d;
        }
        return $dataWithUserDetails;
    }

    public function getAllByStudentId($studentId)
    {
        return $this->spaceUserMappingRepo->getAllByStudentId($studentId);
    }

    public function myData()
    {
        $loggedInUser = getLoggedInUserInfo();
        $loggedInUser = json_decode($loggedInUser);
        if ($loggedInUser != null) {
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }


    public function getShared()
    {
        $loggedInUser = getLoggedInUserInfo();
        if ($loggedInUser != null) {
            return $this->spaceUserMappingRepo->getAllByStudentId($loggedInUser->userId);
        }
    }


    public function update($requestBody)
    {
        $this->save($requestBody);
    }

    public function getBySpaceId($spaceId)
    {
        $data = $this->spaceUserMappingRepo->getBySpaceId($spaceId);
        $dataWithUserDetails = [];
        foreach ($data as $d) {
            $d['user'] = getUserById($d['studentId']);
            $dataWithUserDetails[] = $d;
        }
        return $dataWithUserDetails;
    }

    public function deleteById($id)
    {
        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            //get the saved data
            $data = $this->getById($id);
            if($data != null){
                //if the owner of space want to remove any user added by the mapped teacher he can do so
                //get the space data 
                $spaceData = $this->spaceService->getById($data['spaceId']);
                if(($loggedInUser->userId == $data['userId']) || ($loggedInUser->userId == $data['studentId']) || ($spaceData != null && $spaceData['userId'] == $loggedInUser->userId)){
                    $spaceData = $this->spaceService->getById($data['spaceId']);
                    //give notification to the user
                    //if user is leaving the space
                    if($loggedInUser->userId == $data['studentId']){
                        saveNotification($spaceData['userId'], $loggedInUser->userId, ' has left your space '.$spaceData['spaceName']. ' on MCQ Buddy Space', AppConstants::BASE_URL);
                    }

                    //owner is removing the user
                    if($loggedInUser->userId == $spaceData['userId']){
                        saveNotification($data['studentId'], $loggedInUser->userId, ' removed you from '.$spaceData['spaceName']. ' Space on MCQ Buddy Space', AppConstants::BASE_URL);
                    }
                   
                    return $this->spaceUserMappingRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "Access Forbidden.");
                }

            }
            else{
                sendResponse(false, 404, "Resource Not Found.");
            }
        }
        else{
            sendResponse(false, 403, "Access Forbidden.");
        }
    }

    public function getById($id)
    {
        $result = $this->spaceUserMappingRepo->getById($id);
        if ($result != null) {
            return $result;
        } else {
            echo sendResponse(false, 404, "Not Found");
        }
    }


    public function joinBySpaceCode($spaceCode)
    {
        $loggedInUser = getLoggedInUserInfo();
        if ($loggedInUser != null && $spaceCode != null) {
            //get the space 
            $spaceModel = $this->spaceService->getBySpaceJoinCode($spaceCode);
            if ($spaceModel != null) {
                if ($spaceModel['userId'] == $loggedInUser->userId) {
                    sendResponse(false, 500, "Could not join you in this space. You are the owner of this space.");
                }

                // check whether logged in user has already joined the space
                if ($this->getByStudentIdAndSpaceId($loggedInUser->userId, $spaceModel['spaceId']) != null) {
                    sendResponse(false, 500, "Already Joined This Space.");
                }

                //if the space is private and logged in user is not the owner of space 
                //will not allow to join the space

                if($spaceModel['spaceVisibility'] == 0 && $loggedInUser->userId != $spaceModel['userId']){
                    sendResponse(false, 500, "You can not join private space. Approach the space owner to join you.");
                }


                $model = new stdClass();
                $model->spaceId = $spaceModel['spaceId'];
                $model->studentId = $loggedInUser->userId;
                $model->userId = $spaceModel['userId'];
                //save notification 
                saveNotification($spaceModel['userId'], $loggedInUser->userId, ' has joined your space using space join code.', AppConstants::BASE_URL);
                $this->save($model);
            } else {
                sendResponse(false, 404, "Could not find Space for this Join Code.");
            }
        }
    }


    public function softDelete($id)
    {
        if ($id != null) {
            $data = $this->getById($id);
            if ($data != null) {
                //if the owner of space want to remove any user added by the mapped teacher he can do so
                //get the space data 
                $spaceData = $this->spaceService->getById($data['spaceId']);
                $loggedInUser = getLoggedInUserInfo();
                if (($loggedInUser != null && $data['userId'] == $loggedInUser->userId) || ($spaceData != null && $loggedInUser->userId == $spaceData['userId']) ) {
                    if($this->spaceUserMappingRepo->softDelete($id, $loggedInUser->userId)){
                        //give notification to the user
                        $spaceData = $this->spaceService->getById($data['spaceId']);
                        saveNotification($data['studentId'], $loggedInUser->userId, 'removed you from '.$spaceData['spaceName']. ' Space on MCQ Buddy Space', AppConstants::BASE_URL);
                       
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
