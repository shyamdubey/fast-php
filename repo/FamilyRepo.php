<?php
require_once __DIR__ . "/../models/Family.php";

class FamilyRepo
{
    public $conn;
    public $tableName;
    public $now;



    public function __construct()
    {
        global $conn, $now;
        $this->conn = $conn;
        $this->now = $now;
        $this->tableName = "fmt_families";
        $this->__createTable();
    }


    private function __createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS ".$this->tableName." (
            `familyId` VARCHAR(255) NOT NULL,
            `familyName` VARCHAR(255) NOT NULL,
            `familyDescription` VARCHAR(4000),
            `familyCreatedBy` INT,
            `familyModifiedBy` INT,
            `familyDeletedBy` INT,
            `familyCreatedOn` VARCHAR(45),
            `familyModifiedOn` VARCHAR(45),
            `isFamilyDeleted` INT default 0,
            `familyDeletedOn` VARCHAR(45),
            PRIMARY KEY (`familyId`),
            constraint FamilyCreatedUserId foreign key (familyCreatedBy) references fmt_users(user_id)
            )
            ";

        $res = mysqli_query($this->conn, $sql);
    }

    public function save($model){
        $sql = "INSERT INTO ".$this->tableName." (familyId, familyName, familyDescription, familyCreatedBy,familyCreatedOn, familyModifiedBy, familyModifiedOn) values ( '".getUUID('fmt')."' , '$model->familyName', $model->familyDescription, '$model->familyCreatedBy', $this->now, $model->familyModifiedBy, '$this->now')";

        $res = mysqli_query($this->conn, $sql);
        if($res){
            return true;
        }
        else{
            return false;
        }

    }

    public function getByUserId($userId){
        $sql = "SELECT * FROM ".$this->tableName." WHERE familyCreatedBy = ".$userId;
        $res = mysqli_query($this->conn,$sql);
        $data = [];
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }
        return $data;
    }


    public function getById($id){
        $sql = "SELECT * FROM ".$this->tableName." WHERE familyId = ".$id;
        $res = mysqli_query($this->conn,$sql);
        return mysqli_fetch_assoc($res);
    }

    public function getAll(){
        $sql = "SELECT * FROM ".$this->tableName." WHERE isFamilyDeleted = 0";
        $res = mysqli_query($this->conn,$sql);
        $data = [];
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }
        return $data;
    }

    public function deleteById($familyId){
        
        $sql = "DELETE FROM ".$this->tableName." WHERE familyId = '$familyId'";
        $res = mysqli_query($this->conn,$sql);
        if($res){
            return true;
        }
        else{
            return false;
        }

    }

    public function update($model){
        $sql = "UPDATE ".$this->tableName." set familyName = '$model->familyName' , familyDescription = $model->familyDescription , familyModifiedBy = $model->familyModifiedBy, familyModifiedOn = '$model->familyModifiedOn' where bookmarkListId = $model->bookmarkListId";
        $res = mysqli_query($this->conn, $sql);
        return json_encode(["message"=>"Updated successfully."]);
    }

    






}

