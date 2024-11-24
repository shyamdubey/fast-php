<?php

class RouteTable{
    private $routes = [];
    public function __construct(){

        //register route for get all quizzes
        $this->registerRoute('quiz', 'QuizService', '', ['GET', 'POST']);
        $this->registerRoute('quiz/getAllByUserId/{val}', 'QuizService', 'getAllByUserId', ['GET']);
        $this->registerRoute('quiz/deleteById/{val}', 'QuizService', 'deleteById', ['DELETE']);
        $this->registerRoute('quiz/getById/{val}', 'QuizService', 'getById', ['GET']);
        $this->registerRoute('quiz/update', 'QuizService', 'update', ['PUT', 'POST']);



        //for Question
        $this->registerRoute('question', 'QuestionService', '', ['GET', 'POST']);
        $this->registerRoute('question/getAllByUserId/{val}', 'QuestionService', 'getAllByUserId', ['GET']);
        $this->registerRoute('question/deleteById/{val}', 'QuestionService', 'deleteById', ['DELETE']);
        $this->registerRoute('question/getById/{val}', 'QuestionService', 'getById', ['GET']);
        $this->registerRoute('question/update', 'QuestionService', 'update', ['PUT', 'POST']);
        $this->registerRoute('question/getByQuizId/{val}', 'QuestionService', 'getByQuizId', ['GET']);


        //for Category
        $this->registerRoute('category', 'CategoryService', '', ['GET', 'POST']);
        $this->registerRoute('category/deleteById/{val}', 'CategoryService', 'deleteById', ['DELETE']);
        $this->registerRoute('category/getById/{val}', 'CategoryService', 'getById', ['GET']);
        $this->registerRoute('category/update', 'CategoryService', 'update', ['PUT', 'POST']);
        $this->registerRoute('category/myCategory', 'CategoryService', 'myCategory', ['GET']);

        //for Quiz Question Relation
        $this->registerRoute('quizQueRelation', 'QuizQuestionRelationService', '', ['GET', 'POST']);
        $this->registerRoute('quizQueRelation/deleteById/{val}', 'QuizQuestionRelationService', 'deleteById', ['DELETE']);
        $this->registerRoute('quizQueRelation/getById/{val}', 'QuizQuestionRelationService', 'getById', ['GET']);
        $this->registerRoute('quizQueRelation/getByQuizId/{val}', 'QuizQuestionRelationService', 'getByQuizId', ['GET']);
        $this->registerRoute('quizQueRelation/update', 'QuizQuestionRelationService', 'update', ['PUT', 'POST']);


        //for Quiz Attempt
        $this->registerRoute('quizAttempt', 'QuizAttemptService', '', ['GET', 'POST']);
        $this->registerRoute('quizAttempt/deleteById/{val}', 'QuizAttemptService', 'deleteById', ['DELETE']);
        $this->registerRoute('quizAttempt/getById/{val}', 'QuizAttemptService', 'getById', ['GET']);
        $this->registerRoute('quizAttempt/getByQuizId/{val}', 'QuizAttemptService', 'getByQuizId', ['GET']);
        $this->registerRoute('quizAttempt/myData', 'QuizAttemptService', 'getByToken', ['GET']);
        $this->registerRoute('quizAttempt/update', 'QuizAttemptService', 'update', ['PUT', 'POST']);
        $this->registerRoute('quizAttempt/saveData', 'QuizAttemptService', 'calculateQuizAttempt', ['POST']);
        $this->registerRoute('quizAttempt/startQuiz', 'QuizAttemptService', 'startQuizAttempt', ['POST']);



        //for quiz attempt detailed info
        $this->registerRoute('quizAttemptDetailedInfo', 'QuizAttemptDetailedInfoService', '', ['GET', 'POST']);
        $this->registerRoute('quizAttemptDetailedInfo/deleteById/{val}', 'QuizAttemptDetailedInfoService', 'deleteById', ['DELETE']);
        $this->registerRoute('quizAttemptDetailedInfo/getById/{val}', 'QuizAttemptDetailedInfoService', 'getById', ['GET']);
        $this->registerRoute('quizAttemptDetailedInfo/getByQuizId/{val}', 'QuizAttemptDetailedInfoService', 'getByQuizId', ['GET']);
        $this->registerRoute('quizAttemptDetailedInfo/myData', 'QuizAttemptDetailedInfoService', 'getByToken', ['GET']);
        $this->registerRoute('quizAttemptDetailedInfo/update', 'QuizAttemptDetailedInfoService', 'update', ['PUT', 'POST']);


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