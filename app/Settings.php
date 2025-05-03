<?php


namespace App;

use Core\Log\LogPattern;

class Settings{

    const GLOBAL_EXCEPTION_HANDLER_CLASS_NAME = "ExceptionHandler";
    const GLOBAL_EXCEPTION_HANDLER_REGISTER_FUNCTION_NAME = "init";
    const LOG_IN_CUSTOM_DIR = true;
    const LOG_DIR = "logs";
    const LOG_FILE_PATTERN = LogPattern::DDMMYYYY_LOGS ; //%d-%m-%y_logs.log [%d = date, %m = month , %y = year]
    const CORS_DOMAINS = ["localhost:8085"];
}