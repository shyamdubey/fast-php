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
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        quizId varchar(255) not null,
        quizName varchar(1000) not null,
        quizDescription varchar(4000) ,
        quizVisibility varchar(255) not null,
        quizStatus int default 1,
        userId int not null,
        quizDatetime varchar(45) not null,
        quizAttemptedCount int default 0,
        quizViews int default 0,
        primary key (quizId)

        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO '.$this->tableName.' (quizId, quizName, quizDescription, quizVisibility, quizStatus, userId, quizDatetime, quizAttemptedCount, quizViews) 
        values (".getUUID().", '$model->quizName', '$model->quizDescription', '$model->quizVisibility', 1, $model->userId, '$this->now', 0, 0)";
        if(mysqli_query($this->conn, $sql)){
            return true;
        }
        else {
            return false;
        }
    }


    function getAll(){
        $sql = 'SELECT * FROM '.$this->tableName.'';
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT * FROM '.$this->tableName.' where userId = $userId";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }
}