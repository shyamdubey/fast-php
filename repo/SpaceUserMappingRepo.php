<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class SpaceUserMappingRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::SPACE_USER_MAPPING_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        spaceUserMappingId varchar(255) not null,
        spaceId varchar(1000) not null,
        userId int not null,
        studentId int not null,
        spaceUserMappingDatetime varchar(45) not null,
        primary key (spaceUserMappingId)

        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (spaceUserMappingId, spaceId, userId, studentId, spaceUserMappingDatetime) 
        values ('".getUUID()."', '$model->spaceId', '$model->userId', $model->studentId, '$this->now')";
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
        $sql = "SELECT * FROM ".$this->tableName."";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT * FROM ".$this->tableName." where userId = $userId";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllBySpaceId($spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where spaceId = '$spaceId'";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllByStudentId($studentId){
        $sql = "SELECT * FROM ".$this->tableName." where studentId = $studentId";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getByStudentIdAndSpaceId($studentId, $spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where studentId = $studentId and spaceId = '$spaceId'";
        $res = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if($row != null){
            $row['user'] = getUserById($row['userId']);
        }
        return $row;
    }

    function getById($id){
        $sql = "SELECT * FROM ".$this->tableName." where spaceUserMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if($row != null){
            $row['user'] = getUserById($row['userId']);
        }
        return $row;

    }

    function getBySpaceId($spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where spaceId = '$spaceId'";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where spaceUserMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }
}