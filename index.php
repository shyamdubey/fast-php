<?php

include_once __DIR__."/cors.php";
include_once __DIR__."/functions.php";
include_once __DIR__."/utils/AppConstants.php";

if(isset($_FILES['file'])){
    $file = $_FILES['file'];
uploadImage($file, AppConstants::QUESTIONS_IMAGE_DIR);

}
echo json_encode(["data"=>"Welcome to Quiz Buddy APIs"]);