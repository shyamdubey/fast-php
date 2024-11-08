<?php

require_once __DIR__."/../repo/QuizRepo.php";
require_once __DIR__."/../functions.php";

class QuizService{

    public $quizRepo;



    public function __construct(){
        $this->quizRepo = new QuizRepo();
    }



    public function getAll(){
        return $this->quizRepo->getAll();
    }


    public function save($requestBody){
        return "Save Request quiz";
    }


    public function getAllByUserId($userId){
        return "Getting all by userid ---- ".$userId;
    }


    public function update($requestBody){
        return "Updating the data";
    }

    public function deleteById($id){
        return "deleting the data";
    }



}