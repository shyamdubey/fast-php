<?php

require_once "functions.php";
require_once "cors.php";
require_once "utils/AppConstants.php";



if(isset($_FILES['images']) && isset($_POST['purpose'])){
    $purpose = htmlentities($_POST['purpose']);
    switch($purpose){
        case 'questions':
            $files = $_FILES['images'];
            $fileCounts = array_filter($_FILES['images']);
            // print_r($files);
            for($i = 0; $i < count($fileCounts)-1; $i++){
                $img['name'] = $_FILES['images']['name'][$i];
                $img['full_path'] = $_FILES['images']['full_path'][$i];
                $img['type'] = $_FILES['images']['type'][$i];
                $img['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                $img['error'] = $_FILES['images']['error'][$i];
                $img['size'] = $_FILES['images']['size'][$i];
                uploadImage($file, AppConstants::QUESTIONS_IMAGE_DIR);
            }
    }
}

