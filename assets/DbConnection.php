<?php

class DbConnection{

    private $conn;
    private $dbName;
    private $serverName;
    private $username;
    private $password;

    public function __construct(){
        $this->conn = null;
        $this->dbName = "quizbuddy";
        $this->serverName = "localhost";
        $this->username = "shyam";
        $this->password = "Pass@123";
    }

    


    public function getConnection(){
        if(!$this->conn){
            $this->conn = mysqli_connect($this->serverName,  $this->username, $this->password, $this->dbName);
        }
        return $this->conn;
    }
    
}