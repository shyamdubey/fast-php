<?php

require_once __DIR__ . "/../repo/SpaceRepo.php";
require_once __DIR__ . "/../models/Category.php";
require_once __DIR__ . "/../functions.php";

class SpaceService
{

    public $spaceRepo;

    public function __construct()
    {
        $this->spaceRepo = new SpaceRepo();
    }

    public function getAll()
    {
        return $this->spaceRepo->getAll();
    }

    public function save($requestBody)
    {
        $model = new Space();
        if (
            !isset($requestBody->spaceName)
            || !isset($requestBody->spaceDescription)
            || !isset($requestBody->spaceVisibility)
            || !isset($requestBody->spaceUrl)
        ) {
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        //generate space join code
        $spaceJoinCode = generateSpaceJoinCode();


        $model->spaceName = $requestBody->spaceName;
        $model->spaceUrl = trim(str_replace(" ", "", $requestBody->spaceUrl));
        $model->spaceDescription = $requestBody->spaceDescription;
        $model->spaceJoinCode = $spaceJoinCode;
        $model->spaceVisibility = $requestBody->spaceVisibility;
        $model->userId = $requestBody->userId;
        if ($this->spaceRepo->save($model)) {
            echo sendResponse(true, 201, "Saved successfully.");
        }
        echo sendResponse(false, 500, "Internal Server Error. Could not save data.");
    }


    public function getAllByUserId($userId)
    {
        return $this->spaceRepo->getAllByUserId($userId);
    }

    public function mySpaces()
    {
        $loggedInUser = getLoggedInUserInfo();
        if ($loggedInUser != null) {
            return $this->getAllByUserId($loggedInUser->userId);
        }
    }


    public function update($requestBody)
    {
        if (
            !isset($requestBody->spaceId)
            || !isset($requestBody->spaceName)
            || !isset($requestBody->spaceDescription)
            || !isset($requestBody->spaceVisibility)
            || !isset($requestBody->spaceUrl)
        ) {
            echo sendResponse(false, 400, "Missing required parameters.");
        }

        //get the saved space
        $savedSpace = $this->getById($requestBody->spaceId);
        if($savedSpace == null){
            sendResponse(false, 404, "Space Not Found.");
        }

        $model = new Space;
        $model->spaceId = $savedSpace['spaceId'];
        $model->spaceName = $requestBody->spaceName;
        $model->spaceDescription = $requestBody->spaceDescription;
        $model->spaceVisibility = $requestBody->spaceVisibility;
        $model->spaceUrl = $requestBody->spaceUrl;

        try{
            if($this->spaceRepo->update($model)){
                sendResponse(true, 200, "Space Updated Successfully.");
            }
            else{
                sendResponse(false, 500, "Something went wrong. Please try again.");
            }

        }
        catch(Exception $e){
            sendResponse(false, 500, $e->getMessage());
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
                    return $this->spaceRepo->deleteById($id);
                } else {
                    sendResponse(false, 403, "You are not authorized user to delete.");
                }
            }
        }
    }

    public function getById($id)
    {
        $result = $this->spaceRepo->getById($id);
        if ($result != null) {
            return $result;
        } else {
            echo sendResponse(false, 404, "The Data for Requested Id not found.");
        }
    }


    public function getBySpaceJoinCode($code)
    {
        return $this->spaceRepo->getBySpaceJoinCode($code);
    }


    public function getBySpaceUrl($url)
    {
        $data = $this->spaceRepo->getBySpaceUrl($url);
        if ($data != null) {
            return $data;
        } else {
            echo sendResponse(false, 404, "Not found.");
        }
    }

    public function getPublicSpaces()
    {
        return $this->spaceRepo->getAllByVisibility(1);
    }


    public function updateColors($requestBody)
    {
        if (
            !isset($requestBody->spaceId)
            || !isset($requestBody->spaceProfileBgColor)
            || !isset($requestBody->spaceProfileFontColor)
            || !isset($requestBody->spaceBgColor)
            || !isset($requestBody->spaceBgFontColor)
        ) {
            echo sendResponse(false, 400, "Missing required parameters.");
        }


        $spaceModel = new Space();
        $spaceModel->spaceId = $requestBody->spaceId;
        $spaceModel->spaceProfileBgColor = $requestBody->spaceProfileBgColor;
        $spaceModel->spaceProfileFontColor = $requestBody->spaceProfileFontColor;
        $spaceModel->spaceBgColor = $requestBody->spaceBgColor;
        $spaceModel->spaceBgFontColor = $requestBody->spaceBgFontColor;

        if ($this->spaceRepo->updateColors($spaceModel)) {
            echo sendResponse(true, 200, "Colors updated successfully.");
        } else {
            echo sendResponse(true, 500, "Internal Server Error. Could not update colors. Please try again.");
        }
    }

    public function softDelete($id)
    {
        if ($id != null) {
            if ($this->getById($id) != null) {
                $loggedInUser = getLoggedInUserInfo();
                if ($loggedInUser != null) {
                    if($this->spaceRepo->softDelete($id, $loggedInUser->userId)){
                        sendResponse(true, 200, "Deleted successfully.");
                    }
                    else{
                        sendResponse(false, 500, "Something went wrong.");
                    }
                } else {
                    sendResponse(false, 500, "Could not load user data.");
                }
            }
        }
    }
}
