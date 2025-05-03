<?php
require_once "vendor/autoload.php";

use App\Middleware\AuthMiddleware;
use Core\Exception\ExceptionHandler;
use Core\Http\Router;
use Core\Security\CorsSecurity;

CorsSecurity::init();
ExceptionHandler::init();
Router::POST("/fun/{foo}/run/{bar}", "TestService@test", [AuthMiddleware::class]);

Router::init();