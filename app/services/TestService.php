<?php


namespace App\services;

use Core\Enums\HttpStatus;
use Core\Http\Request;
use Core\Http\Response;
use Core\Log\LoggerFactory;

class TestService{

    private LoggerFactory $logger;

    public function __construct()
    {
        $this->logger = LoggerFactory::get_logger(self::class);
    }


    public function test($foo){
        
        $this->logger->info("Hitting Test Service test method.");
        Response::json(HttpStatus::OK, ["hello"=>$foo]);
    }
}