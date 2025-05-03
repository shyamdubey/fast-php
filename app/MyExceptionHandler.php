<?php


namespace App;

use Core\Exception\ExceptionHandler;
use Throwable;

class MyExceptionHandler extends ExceptionHandler{


    public static function init(){
        set_exception_handler([self::class, 'handle']);
    }


    public static function handle(Throwable $ex){
        echo $ex->getMessage();
    }
}