<?php

require_once __DIR__ . "/../repo/SpaceTeacherMappingRepo.php";
require_once __DIR__ . "/../functions.php";
require_once __DIR__ . "/SpaceService.php";
require_once __DIR__."/../models/SpaceTeacherMapping.php";

class SpaceTeacherMappingService
{

    public $spaceTeacherMappingRepo;
    private $spaceService;
    public function __construct()
    {
        $this->spaceTeacherMappingRepo = new SpaceTeacherMappingRepo();
        $this->spaceService = new SpaceService();
    }

    public function getAll()
    {
        $data = $this->spaceTeacherMappingRepo->getAll();
        $dataWithUserDetails = [];
        foreach ($data as $d) {
            $d['teacher'] = getUserById($d['teacherId']);
            $dataWithUserDetails[] = $d;
        }
        return $dataWithUserDetails;
    }

    public function save($requestBody)
    {
        $model = new SpaceTeacherMapping();
        if (
            !isset($requestBody->spaceId)
            || !isset($requestBody->teacherId)
        ) {
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->spaceId = $requestBody->spaceId;
        $model->teacherId = $requestBody->teacherId;
        $model->spaceTeacherMappedBy = $requestBody->userId;

        //check whether the same mapping already exist
        if($this->getByTeacherIdAndSpaceId($model->teacherId, $model->spaceId) != null){
            sendResponse(false, 500, "User is already mapped with this space.");
        }
        if ($this->spaceTeacherMappingRepo->save($model)) {
            sendResponse(true, 201, "Mapped Successfully.");
        } else {
            sendResponse(false, 500, "Internal Server Error. Please Try Again Or Report to Administrator.");
        }
    }


    public function bulkMapByEmail($requestBody)
    {
        if (
            !isset($requestBody->teacherList) || !isset($requestBody->spaceId) && count($requestBody->teacherList) < 1
        ) {
            echo sendResponse(false, 400, 'Missing Required Parameters.');
        }
        $mappedCount = 0;
        $unMappedCount = 0;
        $teacherList = $requestBody->teacherList;
        foreach ($teacherList as $teacher) {

            
            $model = new SpaceTeacherMapping();
            $model->spaceId = $requestBody->spaceId;
            $model->teacherId = $teacher->userId;
            $model->spaceTeacherMappedBy = $requestBody->userId;
            if ($this->getByTeacherIdAndSpaceId($model->teacherId, $model->spaceId) == null) {
                if ($this->spaceTeacherMappingRepo->save($model)) {
                    //give notification to the user
                    saveNotification($model->teacherId, $requestBody->userId, ' mapped you as teacher in a space on MCQ Buddy Space.', AppConstants::BASE_URL);
                    
                    $mappedCount = $mappedCount + 1;
                }
            }
        }
        $unMappedCount = count($teacherList) - $mappedCount;
        if ($unMappedCount == 0) {
            sendResponse(true, 200, "Mapped successfully.");
        } else {
            sendResponse(true, 200, $mappedCount . " User(s) mapped successfully. Found " . $unMappedCount . " user(s) already mapped with this space.");
        }
    }


    public function mapByEmail($requestBody)
    {
        $model = new SpaceTeacherMapping();
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
            $model->teacherId = $user->userId;
            $model->spaceTeacherMappedBy = $requestBody->userId;
            if ($this->getByTeacherIdAndSpaceId($model->teacherId, $model->spaceId) == null) {
                if ($this->spaceTeacherMappingRepo->save($model)) {
                    //give notification to the user
                    saveNotification($model->teacherId, $requestBody->userId, ' mapped you as teacher in a space on MCQ Buddy Space.', AppConstants::BASE_URL);
                    echo sendResponse(true, 201, "Mapped successfully");
                }
            } else {
                echo sendResponse(false, 500, "User Already Mapped.");
            }
        } else {
            echo sendResponse(false, 404, 'User not found.');
        }
    }


    public function getByTeacherIdAndSpaceId($studentId, $spaceId)
    {
        return $this->spaceTeacherMappingRepo->getByTeacherIdAndSpaceId($studentId, $spaceId);
    }

    public function getAllByUserId($userId)
    {
        return $this->spaceTeacherMappingRepo->getAllByUserId($userId);
    }

    public function getAllBySpaceId($spaceId)
    {
        $data = $this->spaceTeacherMappingRepo->getAllBySpaceId($spaceId);
        $dataWithUserDetails = [];
        if(count($data) > 0){
            foreach ($data as $d) {
                $d['teacher'] = getUserById($d['teacherId']);
                $dataWithUserDetails[] = $d;
            }
        }
        
        return $dataWithUserDetails;
    }


    public function getAllByTeacherId($teacherId)
    {
        return $this->spaceTeacherMappingRepo->getAllByTeacherId($teacherId);
    }

    public function myData()
    {
        $loggedInUser = getLoggedInUserInfo();
        $loggedInUser = json_decode($loggedInUser);
        if ($loggedInUser != null) {
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }

    public function deleteById($id)
    {
        //check whether the deleting user is owner
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            //get the saved data
            $data = $this->getById($id);
            if($data != null){
                if($loggedInUser->userId == $data['spaceTeacherMappedBy'] || $loggedInUser->userId == $data['teacherId']){
                    $spaceData = $this->spaceService->getById($data['spaceId']);
                    if($loggedInUser->userId == $data['spaceTeacherMappedBy']){
 //give notification to the user
 saveNotification($data['teacherId'], $loggedInUser->userId, ' removed you (as a teacher) from '.$spaceData['spaceName']. ' Space on MCQ Buddy Space', AppConstants::BASE_URL);
                    
                    }
                    else if ($loggedInUser->userId == $data['teacherId']){
                         //give notification to the user
                     saveNotification($data['spaceTeacherMappedBy'], $loggedInUser->userId, ' left your space (as a teacher) '.$spaceData['spaceName']. ' on MCQ Buddy Space', AppConstants::BASE_URL);
                    
                    }
                    
                    return $this->spaceTeacherMappingRepo->deleteById($id);
                }
                else{
                    sendResponse(false, 403, "You are not authorized to delete.");
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
        $result = $this->spaceTeacherMappingRepo->getById($id);
        if ($result != null) {
            return $result;
        } else {
            echo sendResponse(false, 404, "Not Found");
        }
    }


    


    public function softDelete($id)
    {
        if ($id != null) {
            $data = $this->getById($id);
            if ($data != null) {
                $loggedInUser = getLoggedInUserInfo();
                if ($loggedInUser != null && $loggedInUser->userId == $data['spaceTeacherMappedBy']) {
                    if($this->spaceTeacherMappingRepo->softDelete($id, $loggedInUser->userId)){
                        //give notification to the user
                        $spaceData = $this->spaceService->getById($data['spaceId']);
                        saveNotification($data['teacherId'], $loggedInUser->userId, ' removed you from '.$spaceData['spaceName']. ' Space on MCQ Buddy Space', AppConstants::BASE_URL);
                        sendResponse(true, 200, "Deleted successfully.");
                    }
                    else{
                        sendResponse(false, 500, "Something went wrong.");
                    }
                } else {
                    sendResponse(false, 403, "You are not authorized to delete.");
                }
            }else{
                sendResponse(false, 404, "Resource Not Found.");
            }
        }else{
            sendResponse(false, 400, "Invalid Request.");
        }
    }


    function getAllTeacherIdMappedWithSpace($spaceId){
        $data = $this->spaceTeacherMappingRepo->getAllTeacherBySpaceId($spaceId);
        sendResponse(true, 200, $data);
    }

    function getAllByLoggedInUserId(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->getAllByTeacherId($loggedInUser->userId);
        }
    }
}
