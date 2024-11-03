<?php

class RouteTable{
    private $routes = [];
    public function __construct(){

        //register route for get all quizzes
        $this->registerRoute('employee/createAccount', 'EmployeeController', 'createAccount');
        $this->registerRoute('employee/getById/{val}', 'EmployeeController', 'getById');
        $this->registerRoute('employee', 'EmployeeController', '');
        $this->registerRoute('quiz', 'QuizService', '');
        $this->registerRoute('quiz/getAllByUserId/{val}', 'QuizService', 'getAllByUserId');
        $this->registerRoute('quiz/deleteById/{val}', 'QuizService', 'deleteById');
        $this->registerRoute('quiz/update', 'QuizService', 'update');
    }


    public function getRoutes(){
        return $this->routes;
    }

    public function registerRoute($route, $controller, $method){
        if(strlen($route) > 0 && strlen($controller) > 0){
            if(strlen($method)>0){
                $controller = $controller."::".$method;
            }

            $routeObj['route']=$route;
            $routeObj['controller']=$controller;
            array_push($this->routes, $routeObj);
        }
    }
   

}