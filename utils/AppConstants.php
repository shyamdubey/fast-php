<?php

include_once __DIR__."/DbConstants.php";

class AppConstants{
    const MCQBUDDY_DB_NAME = DbConstant::MCQBUDDY_DB;
    const QUIZBUDDY_DB_NAME = DbConstant::QUIZBUDDY_DB;

    //tables
    const QUIZ_TABLE = "quizzes";
    const QUESTIONS_TABLE = "questions";
    const ATTEMPT_TABLE = "quiz_attempts";
    

}