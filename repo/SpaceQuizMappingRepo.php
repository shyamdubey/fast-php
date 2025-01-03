<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class SpaceQuizMappingRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::SPACE_QUIZ_MAPPING_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = "CREATE TABLE IF NOT EXISTs ".$this->tableName."  (
        spaceQuizMappingId varchar(255) not null,
        spaceId VARCHAR(255) not null,
        quizId VARCHAR(255) not null,
        spaceQuizMappingStatus int default 1,
        spaceQuizMappingTime varchar(45) not null,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        primary key (spaceQuizMappingId),
        constraint FK_qzspMap_space foreign Key (spaceId) references ".AppConstants::SPACE_TABLE." (spaceId) on delete cascade on update cascade,
        constraint FK_qzspMap_quiz foreign Key (quizId) references ".AppConstants::QUIZ_TABLE." (quizId) on delete cascade on update cascade

        )";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (spaceQuizMappingId, spaceId, quizId, spaceQuizMappingTime) 
        values ('".getUUID()."', '$model->spaceId', '$model->quizId', '$this->now')";
        if(mysqli_query($this->conn, $sql)){
            return true;
        }
        else {
            return false;
        }
    }


    function getAll(){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::SPACE_TABLE." B on A.spaceId = B.spaceId inner join ".AppConstants::QUIZ_TABLE." C on C.quizId = A.quizId where A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::SPACE_TABLE." B on A.spaceId = B.spaceId inner join ".AppConstants::QUIZ_TABLE." C on C.quizId = A.quizId where B.userId = $userId and A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllBySpaceId($spaceId){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::SPACE_TABLE." B on A.spaceId = B.spaceId inner join ".AppConstants::QUIZ_TABLE." C on C.quizId = A.quizId where A.spaceId = '$spaceId' and A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }


    function getById($id){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::SPACE_TABLE." B on A.spaceId = B.spaceId inner join ".AppConstants::QUIZ_TABLE." C on C.quizId = A.quizId where A.spaceQuizMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where spaceQuizMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedOn = '$this->now', deletedBy = $userId where spaceQuizMappingId = '$id'";
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