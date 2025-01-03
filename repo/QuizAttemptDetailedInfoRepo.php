<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class QuizAttemptDetailedInfoRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::QUIZ_ATTEMPT_DETAILED_INFO_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        quizAttemptDetailedInfoId varchar(255) not null,
        quizAttemptId varchar(255) not null,
        questionId varchar(255) not null,
        userSelectedOption varchar(50),
        isCorrect int not null,
        quizAttemptedBy int not null,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        quizAtmptDetInfoDatetime varchar(45) not null,
        primary key (quizAttemptDetailedInfoId),
        constraint FK_QAttempt_delinfo_Question FOREIGN KEY (questionId) REFERENCES '.AppConstants::QUESTIONS_TABLE.' (questionId) on delete cascade on update cascade
        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (quizAttemptDetailedInfoId, quizAttemptId, questionId, isCorrect, userId, quizAtmptDetInfoDatetime, userSelectedOption) 
        values ('".getUUID()."', '$model->quizAttemptId', '$model->questionId', $model->isCorrect, $model->userId, '$this->now', '$model->userSelectedOption')";
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
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId inner join ".AppConstants::QUESTIONS_TABLE." C on C.questionId = A.questionId where A.isDeleted = 0"  ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByQuizId($quizId){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId inner join ".AppConstants::QUESTIONS_TABLE." C on C.questionId = A.questionId where A.quizId = '$quizId' and A.isDeleted = 0" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByQuizIdAndUserId($quizId, $userId){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId inner join ".AppConstants::QUESTIONS_TABLE." C on C.questionId = A.questionId where A.userId = '$userId' and A.quizId = '$quizId' and A.isDeleted = 0" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId inner join ".AppConstants::QUESTIONS_TABLE." C on C.questionId = A.questionId where A.quizAttemptDetailedInfoId = '$id'"  ;
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where quizAttemptDetailedInfoId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedOn = '$this->now', deletedBy = $userId where quizAttemptDetailedInfoId = '$id'";
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