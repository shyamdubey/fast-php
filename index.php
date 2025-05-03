<?php
require_once "vendor/autoload.php";

use App\Services\TestService;
use Core\App;
use Core\Http\Request;
use Core\Http\Router;


/**
 * **********************************************************************************************************
 * Welcome to PHP-FAST Light Weight API Framework. This Framework is developed on core php by Mr. Shyam Dubey. 
 * You can start from this file. 
 * 
 * 
 * 
 * Basic Structure of Project will be 
 * -app
 *      -Middleware
 *      -Model
 *      -Repo
 *      -Service
 *      Settings.php
 * -core
 *      (Code which needs not to be changed)
 * -logs
 * -vendor
 * composer.json
 * composer.lock
 * index.php
 * 
 * 
 * 
 * In this framework, everything is simple and easy to use. 
 * Here is the example for creating an endpoint in your application
 * 
 * Router::get("/test",TestService::class."@index");
 * 
 * Points: 
 *      --in the second parameter, provide fully qualified Class Name. You can use ClassName::class or "App\Services\TestService"."@index"
 *  
 * 
 * That's It. It will call the function index() of TestService
 * 
 * If you want to add any Middleware for any route you can add that Middleware in Router::func($url, $callback, $middleware = []) 
 * $middleware should be provided in array.
 * ***********************************************************************************************************
 */

Router::get("/test/{val}", TestService::class."@test");
Router::get("/", TestService::class."@index");




//place at the end
App::start();
