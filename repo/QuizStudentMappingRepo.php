<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class QuizStudentMappingRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::QUIZ_STUDENT_MAPPING_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        quizStudentMappingId varchar(255) not null,
        quizId varchar(255) not null,
        studentId varchar(255) not null,
        userId int not null,
        quizStudentMappingtime varchar(45) not null,
        primary key (quizStudentMappingId),
        constraint FK_QSRel_Quiz FOREIGN KEY (quizId) REFERENCES '.AppConstants::QUIZ_TABLE.' (quizId)
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
        $sql = "INSERT INTO ".$this->tableName." (quizStudentMappingId, quizId, studentId,  userId, quizStudentMappingtime) 
        values ('".getUUID()."', '$model->quizId', '$model->studentId', $model->userId, '$this->now')";
        if(mysqli_query($this->conn, $sql)){
            return true;
        }
        else {
            return false;
        }
    }


    function getAll(){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getByQuizIdAndStudentId($quizId, $studentId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId where A.quizId = '$quizId' and A.studentId = $studentId"  ;
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function getAllByQuizId($quizId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId where A.quizId = '$quizId'" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['studentId']);
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::QUIZ_TABLE." B on B.quizId = A.quizId where A.quizStudentMappingId = '$id'"  ;
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where quizStudentMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

}