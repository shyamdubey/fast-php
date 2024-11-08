<?php

class RouteTable{
    private $routes = [];
    public function __construct(){

        //register route for get all quizzes
        $this->registerRoute('quiz', 'QuizService', '', ['GET', 'POST']);
        $this->registerRoute('quiz/getAllByUserId/{val}', 'QuizService', 'getAllByUserId', ['GET']);
        $this->registerRoute('quiz/deleteById/{val}', 'QuizService', 'deleteById', ['DELETE']);
        $this->registerRoute('quiz/update', 'QuizService', 'update', ['PUT', 'POST']);
    }


    public function getRoutes(){
        return $this->routes;
    }

    public function registerRoute($route, $controller, $function, $allowedRequestMethods){
        if(strlen($route) > 0 && strlen($controller) > 0){
            if(strlen($function)>0){
                $controller = $controller."::".$function;
            }

            $routeObj['route']=$route;
            $routeObj['controller']=$controller;
            $routeObj['allowedMethods']=$allowedRequestMethods;
            array_push($this->routes, $routeObj);
        }
    }
   

}