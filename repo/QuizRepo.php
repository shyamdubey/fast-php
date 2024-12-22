<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class QuizRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::QUIZ_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = "CREATE TABLE IF NOT EXISTs ".$this->tableName."  (
        quizId varchar(255) not null,
        quizName varchar(1000) not null,
        quizDescription varchar(4000) ,
        quizVisibility varchar(255) not null,
        quizStatus int default 1,
        noOfQuestion int not null,
        userId int not null,
        quizDatetime varchar(45) not null,
        quizAttemptedCount int default 0,
        quizViews int default 0,
        primary key (quizId)
        )";
        $res = mysqli_query($this->conn, $sql);
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


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (quizId, quizName, quizDescription, quizVisibility, quizStatus, userId, quizDatetime, quizAttemptedCount, quizViews, noOfQuestions) 
        values ('".getUUID()."', '$model->quizName', '$model->quizDescription', '$model->quizVisibility', 1, $model->userId, '$this->now', 0, 0, $model->noOfQuestions)";
        if(mysqli_query($this->conn, $sql)){
            return true;
        }
        else {
            return false;
        }
    }


    function getAll(){
        $sql = "SELECT A.* FROM ".$this->tableName." A";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT A.* FROM ".$this->tableName." A where A.userId = $userId";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByVisibility($visibility){
        $sql = "SELECT A.* FROM ".$this->tableName." A where A.quizVisibility = $visibility";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }


    function getAllQuizzesWhichAreNotMappedToSpaceId($spaceId){
        $sql = "SELECT A.* FROM ".$this->tableName." A where A.quizId not in (select B.quizId from ".AppConstants::SPACE_QUIZ_MAPPING_TABLE." B where B.spaceId = '$spaceId')";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;

    }

    function getTopByUserId($userId){
        $sql = "SELECT A.* FROM ".$this->tableName." A where A.userId = $userId order by A.quizDatetime desc limit 0, 1";
        $res = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($res);
        return $row;
    }

    function getById($id){
        $sql = "SELECT A.* FROM ".$this->tableName." A where A.quizId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where quizId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }
}