<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";
require_once __DIR__."/QuestionImageMappingRepo.php";


class QuizQuestionRelationRepo{
    public $tableName;
    public $conn, $now;
    private $questionImageMappingRepo;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::QUIZ_QUESTION_RELATION;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();
        $this->questionImageMappingRepo = new QuestionImageMappingRepo();


    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        quizQuestionRelId varchar(255) not null,
        quizId varchar(255) not null,
        questionId varchar(255) not null,
        quizQueRelStatus int default 1,
        userId int not null,
        quizQueRelDatetime varchar(45) not null,
        primary key (quizQuestionRelId),
        constraint FK_QQRel_Quiz FOREIGN KEY (quizId) REFERENCES '.AppConstants::QUIZ_TABLE.' (quizId),
        constraint FK_QQRel_Question FOREIGN KEY (questionId) REFERENCES '.AppConstants::QUESTIONS_TABLE.' (questionId)
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
        $sql = "INSERT INTO ".$this->tableName." (quizQuestionRelId, quizId, questionId,  quizQueRelStatus, userId, quizQueRelDatetime) 
        values ('".getUUID()."', '$model->quizId', '$model->questionId',  1, $model->userId, '$this->now')";
        if(mysqli_query($this->conn, $sql)){
            return true;
        }
        else {
            return false;
        }
    }


    function getAll(){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::QUESTIONS_TABLE." B on B.questionId = A.questionId inner join ".AppConstants::QUIZ_TABLE." C on C.quizId = A.quizId" ;
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByQuizId($quizId){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::QUESTIONS_TABLE." B on B.questionId = A.questionId inner join ".AppConstants::QUIZ_TABLE." C on C.quizId = A.quizId where A.quizId = '$quizId'" ;
        $data = [];
        $images = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            if($row['haveImages'] == 1){
                $images = $this->questionImageMappingRepo->getAllByQuestionId($row['questionId']);
            }
            $row['images'] = $images;
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT A.*, B.*, C.* FROM ".$this->tableName." A inner join ".AppConstants::QUESTIONS_TABLE." B on B.questionId = A.questionId inner join ".AppConstants::QUIZ_TABLE." C on C.quizId = A.quizId where A.quizQuestionRelId = '$id'"  ;
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where quizQuestionRelId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

}