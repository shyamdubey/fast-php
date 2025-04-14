<?php

include_once __DIR__."/DbConstants.php";

class AppConstants{
    // const MCQBUDDY_DB_NAME = DbConstant::MCQBUDDY_DB;
    const QUIZBUDDY_DB_NAME = DbConstant::FAMILYTREE_DB;


    //common
    const QUESTIONS_IMAGE_DIR = __DIR__."/../images/questions/";
    const FILES_DIR = __DIR__."/../images/files/";
    const USERS_IMAGE_DIR = __DIR__."/../images/users/";
    const EXTENSIONS_FOR_IMAGES = ['jpeg', 'png', 'jpg'];
    const EXTENSIONS_FOR_FILES = ['pdf'];
    const MAX_FILE_SIZE = 2 * 1024 * 1024;   ///bits
    const BASE_URL = "http://localhost:4200";
    // const BASE_URL = "https://space.mcqbuddy.com";

    //tables
    const FAMILY_TABLE = "families";
    const RELATION_TABLE = "relations";
    const PERSON_TABLE = "persons";
    

    //mcqbuddy const
    const MCQBUDDY_URL = "http://localhost:8081";
    // const MCQBUDDY_URL = "https://www.mcqbuddy.com";
    
    const MCQBUDDY_VERIFY_TOKEN_API = AppConstants::MCQBUDDY_URL."/api/verifyToken.php";
    const MCQBUDDY_LOGIN_API = AppConstants::MCQBUDDY_URL."/api/userLogin.php";
    const MCQBUDDY_LOGOUT_API = AppConstants::MCQBUDDY_URL."/api/logout.php";
    const MCQBUDDY_REFRESH_TOKEN = AppConstants::MCQBUDDY_URL."/api/refreshToken.php";
    const MCQBUDDY_GET_USER_BY_TOKEN = AppConstants::MCQBUDDY_URL."/api/getUserFromToken.php";
    const MCQBUDDY_GET_USER_BY_ID = AppConstants::MCQBUDDY_URL."/api/url/user/getById/";
    const MCQBUDDY_GET_USERS_WHERE_NAME_LIKE = AppConstants::MCQBUDDY_URL."/api/url/user/getByUsernameOrEmailOrName&value/";
    const MCQBUDDY_GET_USERS_BY_EMAIL = AppConstants::MCQBUDDY_URL."/api/url/user/getByEmail/";
    const MCQBUDDY_GET_ALL_USERS = AppConstants::MCQBUDDY_URL."/api/url/user/getAllUsers";
    const MCQBUDDY_GET_ALL_USERS_BY_PAGINATION = AppConstants::MCQBUDDY_URL."/api/url/user/getAllUsersByPagination/";
    const MCQBUDDY_CREATE_ACCOUNT = AppConstants::MCQBUDDY_URL."/api/url/user";
    const MCQBUDDY_SAVE_NOTIFICATION = AppConstants::MCQBUDDY_URL."/api/url/notification";

}