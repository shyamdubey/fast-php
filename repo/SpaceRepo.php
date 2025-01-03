<?php

require_once __DIR__."/../utils/AppConstants.php";
require_once __DIR__."/../assets/dbconn.php";
require_once __DIR__."/../functions.php";

class SpaceRepo{
    public $tableName;
    public $conn, $now;


    public function __construct(){
        global $conn, $now;
        $this->tableName = AppConstants::SPACE_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();

    }

    private function createTable(){
        $sql = "CREATE TABLE IF NOT EXISTs ".$this->tableName."  (
        spaceId varchar(255) not null,
        spaceName varchar(1000) not null,
        spaceDescription varchar(4000) not null,
        spaceProfileBgColor VARCHAR(255) not null default '#673ab7',
        spaceProfileFontColor VARCHAR(255) not null default '#fff',
        spaceBgColor VARCHAR(255) not null default '#ffd740',
        spaceBgFontColor VARCHAR(255) not null default '#000',
        spaceVisibility int default 0,
        spaceUrl varchar(255) not null unique,
        spaceJoinCode varchar(50) not null unique,
        spaceStatus int default 1,
        userId int not null,
        isDeleted int not null default 0,
        deletedOn varchar(50),
        deletedBy int ,
        spaceDatetime varchar(45) not null,
        primary key (spaceId)

        )";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        return false;

    }


    function save($model){
        $sql = "INSERT INTO ".$this->tableName." (spaceId, spaceName, spaceStatus, userId, spaceDatetime, spaceDescription, spaceVisibility, spaceJoinCode, spaceUrl) 
        values ('".getUUID()."', '$model->spaceName', 1, $model->userId, '$this->now', '$model->spaceDescription', $model->spaceVisibility, '$model->spaceJoinCode', '$model->spaceUrl')";
        $res = null;
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
        $sql = "SELECT * FROM ".$this->tableName." where A.isDeleted = 0";
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

    

    function getAllByVisibility($visibility){
        $sql = "SELECT * FROM ".$this->tableName." where spaceVisibility = $visibility and isDeleted = 0";
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }

        return $data;
    }

    function getById($id){
        $sql = "SELECT * FROM ".$this->tableName." where spaceId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id){
        $sql = "DELETE FROM ".$this->tableName." where spaceId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }


    function getBySpaceJoinCode($code){
        $sql = "SELECT * FROM ".$this->tableName." where spaceJoinCode = '$code' and isDeleted = 0";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function getBySpaceUrl($url){
        $sql = "SELECT * FROM ".$this->tableName." where spaceUrl = '$url' and isDeleted = 0";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }



    function updateColors($model){

        $sql = "UPDATE ".$this->tableName." set spaceProfileBgColor = '$model->spaceProfileBgColor' , spaceProfileFontColor = '$model->spaceProfileFontColor', spaceBgColor = '$model->spaceBgColor' , spaceBgFontColor = '$model->spaceBgFontColor' where spaceId = '$model->spaceId'";
        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    function softDelete($id, $userId){
        $sql = "UPDATE ".$this->tableName." set isDeleted = 1, deletedOn = '$this->now', deletedBy = $userId where spaceId = '$id'";
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