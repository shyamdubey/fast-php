<?php

include_once __DIR__."/DbConstants.php";

class AppConstants{
    // const MCQBUDDY_DB_NAME = DbConstant::MCQBUDDY_DB;
    const QUIZBUDDY_DB_NAME = DbConstant::QUIZBUDDY_DB;


    //common
    const QUESTIONS_IMAGE_DIR = __DIR__."/../images/questions/";
    const FILES_DIR = __DIR__."/../images/files/";
    const USERS_IMAGE_DIR = __DIR__."/../images/users/";
    const EXTENSIONS_FOR_IMAGES = ['jpeg', 'png', 'jpg'];
    const EXTENSIONS_FOR_FILES = ['pdf'];
    const MAX_FILE_SIZE = 2 * 1024 * 1024;   ///bits
    const BASE_URL = "http://localhost:8084";

    //tables
    const QUIZ_TABLE = "quizzes";
    const SPACE_QUIZ_MAPPING_TABLE = "space_quiz_mappings";
    const CATEGORY_TABLE = "categories";
    const SPACE_USER_MAPPING_TABLE = "space_user_mapping";
    const SPACE_TABLE = "spaces";
    const FILE_UPLOAD_TABLE = "files_uploaded";
    const QUIZ_QUESTION_RELATION = "quiz_question_relations";
    const QUIZ_STUDENT_MAPPING_TABLE = "quiz_students_mapping";
    const QUESTIONS_TABLE = "questions";
    const QUESTION_IMAGE_MAPPING_TABLE = "question_image_mapping";
    const QUIZ_ATTEMPT_TABLE = "quiz_attempts";
    const QUIZ_ATTEMPT_DETAILED_INFO_TABLE = "quiz_attempt_detailed_info";

    //mcqbuddy const
    const MCQBUDDY_URL = "https://www.mcqbuddy.com";
    
    const MCQBUDDY_VERIFY_TOKEN_API = AppConstants::MCQBUDDY_URL."/api/verifyToken.php";
    const MCQBUDDY_LOGIN_API = AppConstants::MCQBUDDY_URL."/api/userLogin.php";
    const MCQBUDDY_GET_USER_BY_TOKEN = AppConstants::MCQBUDDY_URL."/api/getUserFromToken.php";
    const MCQBUDDY_GET_USER_BY_ID = AppConstants::MCQBUDDY_URL."/api/user.php?method=getByUserId&userId=";
    const MCQBUDDY_GET_USERS_WHERE_NAME_LIKE = AppConstants::MCQBUDDY_URL."/api/user.php?method=getByUsernameOrEmailOrName&value=";
    const MCQBUDDY_GET_USERS_BY_EMAIL = AppConstants::MCQBUDDY_URL."/api/user.php?method=getByEmail&value=";
    const MCQBUDDY_GET_ALL_USERS = AppConstants::MCQBUDDY_URL."/api/user.php?method=getAllUsers";
    const MCQBUDDY_GET_ALL_USERS_BY_PAGINATION = AppConstants::MCQBUDDY_URL."/api/user.php?method=getAllUsersByPagination&page=";
    const MCQBUDDY_CREATE_ACCOUNT = AppConstants::MCQBUDDY_URL."/api/user.php";

}