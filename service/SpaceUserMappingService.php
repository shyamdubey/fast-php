<?php

require_once __DIR__."/../repo/SpaceUserMappingRepo.php";
require_once __DIR__."/../models/Category.php";
require_once __DIR__."/../functions.php";

class SpaceUserMappingService{

    public $spaceUserMappingRepo;

    public function __construct(){
        $this->spaceUserMappingRepo = new SpaceUserMappingRepo();
    }

    public function getAll(){
        $data = $this->spaceUserMappingRepo->getAll();
        $dataWithUserDetails = [];
        foreach($data as $d){
            $d['user'] = getUserById($d['studentId']);
            $dataWithUserDetails[] = $d;
        }
        return $dataWithUserDetails;
    }

    public function save($requestBody){
        $model = new SpaceUserMapping();
        if(!isset($requestBody->spaceId)
        || !isset($requestBody->studentId)){
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        $model->spaceId = $requestBody->spaceId;
        $model->studentId = $requestBody->studentId;
        $model->userId = $requestBody->userId;
        $this->spaceUserMappingRepo->save($model);
        

    }


    public function bulkMapByEmail($requestBody){
        if(
            !isset($requestBody->studentsList) || !isset($requestBody->spaceId) && count($requestBody->studentsList) < 1
        )
        {
            echo sendResponse(false, 400, 'Missing Required Parameters.');
        }
        $mappedCount = 0;
        $unMappedCount = 0;
        $studentsList = $requestBody->studentsList;
        foreach($studentsList as $student){
            $model = new SpaceUserMapping();
            $model->spaceId = $requestBody->spaceId;
            $model->studentId = $student->userId;
            $model->userId = $requestBody->userId;
            if($this->getByStudentIdAndSpaceId($model->studentId, $model->spaceId) == null){
                if($this->spaceUserMappingRepo->save($model)){
                    $mappedCount = $mappedCount + 1;
                }
            }
        }
        $unMappedCount = count($studentsList) - $mappedCount;
        if($unMappedCount == 0){
            sendResponse(true, 200, "Mapped successfully.");
        }
        else{
            sendResponse(true, 200, $mappedCount." Student(s) mapped successfully. Found ".$unMappedCount." student(s) already mapped with this space.");
        }
        
    }


    public function mapByEmail($requestBody){
        $model = new SpaceUserMapping();
        if(
            !isset($requestBody->spaceId) ||
            !isset($requestBody->email)
        )
        {
            echo sendResponse(false, 400, 'Missing Required Parameters.');
        }
        //get user by email
        $user = getUsersByEmail($requestBody->email);
        if($user != null){
            $model->spaceId = $requestBody->spaceId;
            $model->studentId = $user->userId;
            $model->userId = $requestBody->userId;
            if($this->getByStudentIdAndSpaceId($model->studentId, $model->spaceId) == null){
                if($this->spaceUserMappingRepo->save($model)){
                    echo sendResponse(true, 201, "Mapped successfully");
                }
            }
            else{
                echo sendResponse(false, 500, "Student Already Mapped.");
            }
            
        }
        else{
            echo sendResponse(false, 404, 'User not found.');
        }
    }


    public function getByStudentIdAndSpaceId($studentId, $spaceId){
        return $this->spaceUserMappingRepo->getByStudentIdAndSpaceId($studentId, $spaceId);
    }

    public function getAllByUserId($userId){
        return $this->spaceUserMappingRepo->getAllByUserId($userId);
    }

    public function getAllBySpaceId($spaceId){
        $data = $this->spaceUserMappingRepo->getAllBySpaceId($spaceId);
        $dataWithUserDetails = [];
        foreach($data as $d){
            $d['user'] = getUserById($d['studentId']);
            $dataWithUserDetails[] = $d;
        }
        return $dataWithUserDetails;
    }

    public function getAllByStudentId($studentId){
        return $this->spaceUserMappingRepo->getAllByStudentId($studentId);
    }

    public function myData(){
        $loggedInUser = getLoggedInUserInfo();
        $loggedInUser = json_decode($loggedInUser);
        if($loggedInUser != null){
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }


    public function getShared(){
        $loggedInUser = getLoggedInUserInfo();
        if($loggedInUser != null){
            return $this->spaceUserMappingRepo->getAllByStudentId($loggedInUser->userId);
        }
    }


    public function update($requestBody){
        $this->save($requestBody);
    }

    public function getBySpaceId($spaceId){
        $data = $this->spaceUserMappingRepo->getBySpaceId($spaceId);
        $dataWithUserDetails = [];
        foreach($data as $d){
            $d['user'] = getUserById($d['studentId']);
            $dataWithUserDetails[] = $d;
        }
        return $dataWithUserDetails;
    }

    public function deleteById($id){
        return $this->spaceUserMappingRepo->deleteById($id);
    }

    public function getById($id){
        $result = $this->spaceUserMappingRepo->getById($id);
        if($result != null){
            return $result;
        }
        else{
            echo sendResponse(false, 404, "Not Found");
        }
    }



}