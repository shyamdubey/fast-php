<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class SpaceTeacherMappingRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::SPACE_TEACHER_MAPPING_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        spaceTeacherMappingId varchar(255) not null,
        spaceId varchar(255) not null,
        spaceTeacherMappedBy int not null,
        teacherId int not null,
        spaceTeacherMappingTime varchar(45) not null,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        primary key (spaceTeacherMappingId),
        constraint FK_spaceteacer foreign key (spaceId) references '.AppConstants::SPACE_TABLE.' (spaceId) on delete cascade on update cascade

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
        $sql = "INSERT INTO ".$this->tableName." (spaceTeacherMappingId, spaceId, teacherId, spaceTeacherMappedBy, spaceTeacherMappingTime) 
        values ('".getUUID()."', '$model->spaceId', '$model->teacherId', $model->spaceTeacherMappedBy, '$this->now')";
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
            $row['teacher'] = getUserById($row['teacherId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($spaceTeacherMappedBy){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::SPACE_TABLE." B on B.spaceId = A.spaceId where A.spaceTeacherMappedBy = $spaceTeacherMappedBy and A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['teacher'] = getUserById($row['spaceTeacherMappedBy']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllBySpaceId($spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where spaceId = '$spaceId' and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['teacher'] = getUserById($row['teacherId']);
            $data[] = $row;
        }

        return $data;
    }

    function getAllTeacherBySpaceId($spaceId){
        $sql = "SELECT teacherId FROM ".$this->tableName." where spaceId = '$spaceId' and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
           $data[] = $row['teacherId'];
        }

        return $data;
    }

    function getAllByTeacherId($teacherId){
        $sql = "SELECT A.*, B.* FROM ".$this->tableName." A inner join ".AppConstants::SPACE_TABLE." B on B.spaceId = A.spaceId where A.teacherId = $teacherId and A.isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['teacher'] = getUserById($row['spaceTeacherMappedBy']);
            $data[] = $row;
        }

        return $data;
    }

    function getByTeacherIdAndSpaceId($teacherId, $spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where teacherId = $teacherId and spaceId = '$spaceId' and isDeleted = 0";
        $res = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if($row != null){
            $row['teacher'] = getUserById($row['spaceTeacherMappedBy']);
        }
        return $row;
    }

    function getById($id){
        $sql = "SELECT * FROM ".$this->tableName." where spaceTeacherMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if($row != null){
            $row['teacher'] = getUserById($row['teacherId']);
        }
        return $row;

    }

    function getBySpaceId($spaceId){
        $sql = "SELECT * FROM ".$this->tableName." where spaceId = '$spaceId'";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $row['teacher'] = getUserById($row['spaceTeacherMappedBy']);
            $data[] = $row;
        }

        return $data;
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where spaceTeacherMappingId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedOn = '$this->now', deletedBy = $userId where spaceTeacherMappingId = '$id'";
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