<?php

include_once __DIR__."/DbConstants.php";

class AppConstants{
    const MCQBUDDY_DB_NAME = DbConstant::MCQBUDDY_DB;
    const QUIZBUDDY_DB_NAME = DbConstant::QUIZBUDDY_DB;

    //tables
    const QUIZ_TABLE = "quizzes";
    const CATEGORY_TABLE = "categories";
    const QUIZ_QUESTION_RELATION = "quiz_question_relations";
    const QUESTIONS_TABLE = "questions";
    const QUIZ_ATTEMPT_TABLE = "quiz_attempts";
    const QUIZ_ATTEMPT_DETAILED_INFO_TABLE = "quiz_attempt_detailed_info";

    //mcqbuddy const
    const MCQBUDDY_URL = "http://localhost:8081";
    
    const MCQBUDDY_VERIFY_TOKEN_API = AppConstants::MCQBUDDY_URL."/api/verifyToken.php";
    const MCQBUDDY_GET_USER_BY_TOKEN = AppConstants::MCQBUDDY_URL."/api/getUserFromToken.php";

}