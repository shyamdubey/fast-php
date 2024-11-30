<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class ImageRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::IMAGES_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        imageId varchar(255) not null,
        imageName varchar(255) not null,
        imageUrl text not null,
        userId int not null,
        imageDatetime varchar(45) not null,
        primary key (imageId)
        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (imageId, imageName, imageUrl, userId, imageDatetime) 
        values ('".getUUID()."', '$model->imageName', '$model->imageUrl',  $model->userId, '$this->now')";
        try{
            $res = mysqli_query($this->conn, $sql);
         }
         catch(Exception $e){
             echo sendResponse(false, 500, $e->getMessage());
         }
         if($res){
             return true;
         }
         else {
             return false;
         }
    }


    function getAll(){
        $sql = "SELECT * FROM ".$this->tableName."";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT * FROM ".$this->tableName." where userId = $userId";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByQuestionId($questionId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUESTIONS_TABLE." B on A.questionId = B.questionId inner join ".AppConstants::IMAGES_TABLE." C on C.imageId = A.imageId A.questionId = '$questionId'";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUESTIONS_TABLE." B on A.questionId = B.questionId inner join ".AppConstants::IMAGES_TABLE." C on C.imageId = A.imageId where A.imageId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where imageId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }
}