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
        spaceId varchar(255) not null,
        userId int not null,
        studentId int not null,
        spaceUserMappingDatetime varchar(45) not null,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        primary key (spaceUserMappingId),
        constraint FK_spaceuser foreign key (spaceId) references '.AppConstants::SPACE_TABLE.' (spaceId) on delete cascade on update cascade

        )';
        try{
            $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;
        }
        catch(Exception $e){
            sendResponse(false, 500, $e->getMessage());
        }

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
        $sql = "SELECT * FROM ".$this->tableName." and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT * FROM ".$this->tableName." where userId = $userId and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllBySpaceId($spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where spaceId = '$spaceId' and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllByStudentId($studentId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::SPACE_TABLE." B on B.spaceId = A.spaceId where A.studentId = $studentId and A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['user'] = getUserById($row['userId']);
            $data[] = $row;
        }

        return $data;
    }

    function getByStudentIdAndSpaceId($studentId, $spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where studentId = $studentId and spaceId = '$spaceId' and isDeleted = 0";
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

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedOn = '$this->now', deletedBy = $userId where spaceUserMappingId = '$id'";
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