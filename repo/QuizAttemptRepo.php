<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class QuizAttemptRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::QUIZ_ATTEMPT_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        quizAttemptId varchar(255) not null,
        quizId varchar(255) not null,
        marks int not null,
        attemptedQuestions int not null,
        startTime varchar(50),
        endTime varchar(50),
        quizAttemptUserId int not null,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        quizAttemptDatetime varchar(45) not null,
        primary key (quizAttemptId),
        constraint FK_QAttempt_Quiz FOREIGN KEY (quizId) REFERENCES '.AppConstants::QUIZ_TABLE.' (quizId) on delete cascade on update cascade
        )';
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
        $sql = "INSERT INTO ".$this->tableName." (quizAttemptId, quizId, marks, startTime, endTime,  attemptedQuestions, quizAttemptUserId, quizAttemptDatetime) 
        values ('".getUUID()."', '$model->quizId', $model->marks,  '$model->startTime', '$model->endTime', $model->attemptedQuestions, $model->quizAttemptUserId, '$this->now')";
        if(mysqli_query($this->conn, $sql)){
            return true;
        }
        else {
            return false;
        }
    }


    function update($model){
        $sql = "UPDATE ".$this->tableName." set marks = $model->marks , attemptedQuestions = $model->attemptedQuestions where quizAttemptId = '$model->quizAttemptId'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }


    function getAll(){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId where A.isDeleted = 0" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByQuizId($quizId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId where A.quizId = '$quizId' and A.isDeleted = 0" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['quizAttemptUserId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId where A.quizAttemptUserId = '$userId' and A.isDeleted = 0" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId where A.quizAttemptId = '$id'"  ;
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function getLastestByUserId($userId){
        $sql = "SELECT A.quizAttemptId FROM ".$this->tableName." A  where A.quizAttemptUserId = $userId and A.isDeleted = 0 order by A.quizAttemptDatetime desc limit 0, 1"  ;
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where quizAttemptId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedOn = '$this->now', deletedBy = $userId where quizAttemptId = '$id'";
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