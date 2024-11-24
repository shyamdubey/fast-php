<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class QuestionRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::QUESTIONS_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        questionId varchar(255) not null,
        question text not null,
        option1 varchar(1000) ,
        option2 varchar(1000) ,
        option3 varchar(1000) ,
        option4 varchar(1000) ,
        correctAns varchar(255) not null,
        userId int not null,
        marks float not null,
        questionDatetime varchar(45) not null,
        questionVisibility int default 0,
        questionStatus int default 1,
        primary key (questionId)

        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (questionId, question, option1, option2, option3, option4, correctAns, userId, questionDatetime, questionVisibility, questionStatus, marks) 
        values ('".getUUID()."', '$model->question', '$model->option1', '$model->option2', '$model->option3', '$model->option4',  '$model->correctAns', $model->userId, '$this->now', $model->questionVisibility, 1, $model->marks)";
        if(mysqli_query($this->conn, $sql)){
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

    function getById($id){
        $sql = "SELECT * FROM ".$this->tableName." where questionId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where questionId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }
}