<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class FileUploadRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::FILE_UPLOAD_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = 'CREATE TABLE IF NOT EXISTs '.$this->tableName.'  (
        fileUploadId varchar(255) not null,
        fileUrl varchar(1000) not null,
        purpose varchar(255) not null,
        fileStatus int default 1,
        userId int not null,
        isPublic int default 1,
        fileUploadDatetime varchar(45) not null,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        primary key (fileUploadId)

        )';
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save(FileUpload $model){
        $sql = "INSERT INTO ".$this->tableName." (fileUploadId, fileUrl, fileStatus, userId, fileUploadDatetime, purpose, isPublic) 
        values ('".getUUID()."', '$model->fileUrl', 1, $model->userId, '$this->now', '$model->purpose', $model->isPublic)";
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
        $sql = "SELECT * FROM ".$this->tableName." where isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId){
        $sql = "SELECT * FROM ".$this->tableName." where userId = $userId and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserIdAndPurpose($userId, $purpose){
        $sql = "SELECT * FROM ".$this->tableName." where userId = $userId and purpose = '$purpose' and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT * FROM ".$this->tableName." where fileUploadId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where fileUploadId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedBy = $userId, deletedOn = '$this->now' where fileUploadId = '$id'";
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