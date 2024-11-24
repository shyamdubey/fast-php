<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class CategoryRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::CATEGORY_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        categoryId varchar(255) not null,
        categoryName varchar(1000) not null,
        categoryStatus int default 1,
        userId int not null,
        categoryDatetime varchar(45) not null,
        primary key (categoryId)

        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (categoryId, categoryName, categoryStatus, userId, categoryDatetime) 
        values ('".getUUID()."', '$model->categoryName', 1, $model->userId, '$this->now')";
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
        $sql = "SELECT * FROM ".$this->tableName." where categoryId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where categoryId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }
}