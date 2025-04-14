<?php

class RouteTable{
    private $routes = [];
    private $exemptedRoutes = [];
    public function __construct(){

        // //exempt routes for token validation
        $this->registerRoutesForExemptions("user/create", ['POST']);
        $this->registerRoutesForExemptions("user/login", ['POST']);




        //common routes
        $this->registerRoute('common/endpoints', 'CommonService', 'getApiEndpoints', ['GET']);
        $this->registerRoute('common/serverTime', 'CommonService', 'getServerDatetime', ['GET']);


        //user routes
        $this->registerRoute('user', 'UserService', '', ['GET', 'POST']);
        $this->registerRoute('user/create', 'UserService', 'save', ['POST']);
        $this->registerRoute('user/refreshToken', 'UserService', 'refreshToken', ['POST']);
        $this->registerRoute('user/me', 'UserService', 'myData', ['GET']);
        $this->registerRoute('user/login', 'UserService', 'performLogin', ['POST']);
        $this->registerRoute('user/logout', 'UserService', 'performLogout', ['POST']);
        $this->registerRoute('user/page/{val}', 'UserService', 'getByPagination', ['GET']);
        $this->registerRoute('user/getById/{val}', 'UserService', 'getById', ['GET']);
        $this->registerRoute('user/getByEmail/{val}', 'UserService', 'getByEmail', ['GET']);
        $this->registerRoute('user/filter/{val}', 'UserService', 'getByUsernameOrEmailOrNameLike', ['GET']);


        //for Files
        $this->registerRoute('files', 'FileUploadService', '', ['GET']);
        $this->registerRoute('files/upload', 'FileUploadService', 'upload', ['POST']);
        $this->registerRoute('files/deleteById/{val}', 'FileUploadService', 'deleteById', ['DELETE']);
        $this->registerRoute('files/delete/{val}', 'FileUploadService', 'softDelete', ['DELETE']);
        $this->registerRoute('files/questions', 'FileUploadService', 'filterByQuestions', ['GET']);
        $this->registerRoute('files/getById/{val}', 'FileUploadService', 'getById', ['GET']);
        $this->registerRoute('files/update', 'FileUploadService', 'update', ['PUT']);
        $this->registerRoute('files/myFiles', 'FileUploadService', 'myFiles', ['GET']);

        

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


    public function registerRoutesForExemptions($route, $methods){
        $exemptedRouteObj['route'] = $route;
        $exemptedRouteObj['allowedMethods'] = $methods;
        array_push($this->exemptedRoutes, $exemptedRouteObj);
    }

    public function getExemptedRoutes(){
        return $this->exemptedRoutes;
    }
   

}