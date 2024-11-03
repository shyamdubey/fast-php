<?php

include_once __DIR__."/cors.php";
include_once __DIR__."/functions.php";

logMessage("Hello and welcome");
echo readLogFileContent(__DIR__."/functions.php");
echo json_encode(["data"=>"Welcome to Quiz Buddy APIs"]);