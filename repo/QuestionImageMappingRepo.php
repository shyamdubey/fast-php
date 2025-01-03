<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class QuestionImageMappingRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::QUESTION_IMAGE_MAPPING_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        queImgMappingId varchar(255) not null,
        questionId varchar(255) not null,
        imageId varchar(255) not null,
        userId int not null,
        queImgMappingDatetime varchar(45) not null,
        queImgMappingStatus int default 1,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        primary key (queImgMappingId),
        constraint FK_que_img_map_que Foreign Key (questionId) references '.AppConstants::QUESTIONS_TABLE.' (questionId) on delete cascade on update cascade,
        constraint FK_que_img_map_img Foreign Key (imageId) references '.AppConstants::FILE_UPLOAD_TABLE.' (fileUploadId) on delete cascade on update cascade
        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (queImgMappingId, questionId, imageId, userId, queImgMappingDatetime) 
        values ('".getUUID()."', '$model->questionId', '$model->imageId', $model->userId, '$this->now')";
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
        $sql = "SELECT * FROM ".$this->tableName." where isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUESTIONS_TABLE." B on A.questionId = B.questionId inner join ".AppConstants::FILE_UPLOAD_TABLE." C on C.fileUploadId = A.imageId where A.userId = $userId and A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByQuestionId($questionId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::FILE_UPLOAD_TABLE." B on B.fileUploadId = A.imageId where A.questionId = '$questionId' and A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUESTIONS_TABLE." B on A.questionId = B.questionId inner join ".AppConstants::FILE_UPLOAD_TABLE." C on C.fileUploadId = A.imageId where A.queImgMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where queImgMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedOn = '$this->now' , deletedBy = $userId where queImgMappingId = '$id'";
        try{
            $res = mysqli_query($this->conn, $sql);
            if($res){
                return true;
            }
            else{
                return false;
            }
        }
        catch(Exception $e){
            echo sendResponse(false, 500, $e->getMessage());
        }
    }
}