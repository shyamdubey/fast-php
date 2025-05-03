<?php


namespace App\Services;

use Core\Enums\HttpStatus;
use Core\Http\Response;
use Core\Log\LoggerFactory;

class TestService{

    private LoggerFactory $logger;

    public function __construct()
    {
        $this->logger = LoggerFactory::get_logger(self::class);
    }


    public function test($params){
        
        $this->logger->info("Hitting Test Service test method.");
        Response::json(HttpStatus::OK, ["hello"=>$params["val"]]);
    }

    public function index(){
        
        $this->logger->info("Hitting Test Service test method.");
        Response::json(HttpStatus::OK, ["hello"=>"Welcome"]);
    }
}