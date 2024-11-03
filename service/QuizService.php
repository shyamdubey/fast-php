<?php

require_once __DIR__."/../repo/QuizRepo.php";
require_once __DIR__."/../functions.php";

class QuizService{

    public $quizRepo;



    public function __construct(){
        $this->quizRepo = new QuizRepo();
    }



    public function getAll(){
        assertRequestGet();
        return $this->quizRepo->getAll();
    }


    public function save($requestBody){
        assertRequestPost();
        return "Save Request quiz";
    }


    public function getAllByUserId($userId){
        assertRequestGet();
        return "Getting all by userid ---- ".$userId;
    }


    public function update($requestBody){
        assertRequestPut();
        return "Updating the data";
    }

    public function deleteById($id){
        assertRequestDelete();
        return "deleting the data";
    }



}