<?php

class RouteTable{
    private $routes = [];
    private $exemptedRoutes = [];
    public function __construct(){

        // //exempt routes for token validation
        $this->registerRoutesForExemptions("user/", ['POST']);
        $this->registerRoutesForExemptions("user/login", ['POST']);




        //common routes
        $this->registerRoute('common/endpoints', 'CommonService', 'getApiEndpoints', ['GET']);


        //user routes
        $this->registerRoute('user', 'UserService', '', ['GET', 'POST']);
        $this->registerRoute('user/me', 'UserService', 'myData', ['GET']);
        $this->registerRoute('user/login', 'UserService', 'performLogin', ['POST']);
        $this->registerRoute('user/page/{val}', 'UserService', 'getByPagination', ['GET']);
        $this->registerRoute('user/getById/{val}', 'UserService', 'getById', ['GET']);
        $this->registerRoute('user/getByEmail/{val}', 'UserService', 'getByEmail', ['GET']);
        $this->registerRoute('user/filter/{val}', 'UserService', 'getByUsernameOrEmailOrNameLike', ['GET']);



         //register route for space
         $this->registerRoute('space', 'SpaceService', '', ['GET', 'POST']);
         $this->registerRoute('space/getAllByUserId/{val}', 'SpaceService', 'getAllByUserId', ['GET']);
         $this->registerRoute('space/public', 'SpaceService', 'getPublicSpaces', ['GET']);
         $this->registerRoute('space/deleteById/{val}', 'SpaceService', 'deleteById', ['DELETE']);
         $this->registerRoute('space/getById/{val}', 'SpaceService', 'getById', ['GET']);
         $this->registerRoute('space/update', 'SpaceService', 'update', ['PUT', 'POST']);
         $this->registerRoute('space/mySpaces', 'SpaceService', 'mySpaces', ['GET']);
         $this->registerRoute('space/getByCode/{val}', 'SpaceService', 'getBySpaceJoinCode', ['GET']);
         $this->registerRoute('space/getByUrl/{val}', 'SpaceService', 'getBySpaceUrl', ['GET']);
         $this->registerRoute('space/updateColors', 'SpaceService', 'updateColors', ['POST']);

 

          //register route for space students mapping
          $this->registerRoute('spaceStudentMapping', 'SpaceUserMappingService', '', ['GET', 'POST']);
          $this->registerRoute('spaceStudentMapping/mapByEmail', 'SpaceUserMappingService', 'mapByEmail', ['POST']);
          $this->registerRoute('spaceStudentMapping/bulkMapByEmail', 'SpaceUserMappingService', 'bulkMapByEmail', ['POST']);
          $this->registerRoute('spaceStudentMapping/getAllByUserId/{val}', 'SpaceUserMappingService', 'getAllByUserId', ['GET']);
          $this->registerRoute('spaceStudentMapping/getAllByStudentId/{val}', 'SpaceUserMappingService', 'getAllByStudentId', ['GET']);
          $this->registerRoute('spaceStudentMapping/getBySpaceId/{val}', 'SpaceUserMappingService', 'getAllBySpaceId', ['GET']);
          $this->registerRoute('spaceStudentMapping/deleteById/{val}', 'SpaceUserMappingService', 'deleteById', ['DELETE']);
          $this->registerRoute('spaceStudentMapping/getById/{val}', 'SpaceUserMappingService', 'getById', ['GET']);
          $this->registerRoute('spaceStudentMapping/getShared', 'SpaceUserMappingService', 'getShared', ['GET']);
          $this->registerRoute('spaceStudentMapping/update', 'SpaceUserMappingService', 'update', ['PUT', 'POST']);
  

        //register route for quizzes
        $this->registerRoute('quiz', 'QuizService', '', ['GET', 'POST']);
        $this->registerRoute('quiz/public', 'QuizService', 'getPublicQuiz', ['GET', 'POST']);
        $this->registerRoute('quiz/private', 'QuizService', 'getPrivateQuiz', ['GET', 'POST']);
        $this->registerRoute('quiz/getAllByUserId/{val}', 'QuizService', 'getAllByUserId', ['GET']);
        $this->registerRoute('quiz/myQuizzes', 'QuizService', 'myQuizzes', ['GET']);
        $this->registerRoute('quiz/deleteById/{val}', 'QuizService', 'deleteById', ['DELETE']);
        $this->registerRoute('quiz/getById/{val}', 'QuizService', 'getById', ['GET']);
        $this->registerRoute('quiz/update', 'QuizService', 'update', ['PUT', 'POST']);
        $this->registerRoute('quiz/notMappedWithSpace/{val}', 'QuizService', 'getBySpaceNotMappedData', ['GET']);



        //for Question
        $this->registerRoute('question', 'QuestionService', '', ['GET', 'POST']);
        $this->registerRoute('question/getAllByUserId/{val}', 'QuestionService', 'getAllByUserId', ['GET']);
        $this->registerRoute('question/deleteById/{val}', 'QuestionService', 'deleteById', ['DELETE']);
        $this->registerRoute('question/getById/{val}', 'QuestionService', 'getById', ['GET']);
        $this->registerRoute('question/update', 'QuestionService', 'update', ['PUT', 'POST']);
        $this->registerRoute('question/getByQuizId/{val}', 'QuestionService', 'getByQuizId', ['GET']);
        $this->registerRoute('question/myQuestion', 'QuestionService', 'myQuestions', ['GET']);


        //for Question Image Mapping
        $this->registerRoute('queImgMapping/getByQueId/{val}', 'QuestionImageMappingService', 'getAllByQuestionId', ['GET']);
        


        
        //for Category
        $this->registerRoute('category', 'CategoryService', '', ['GET', 'POST']);
        $this->registerRoute('category/deleteById/{val}', 'CategoryService', 'deleteById', ['DELETE']);
        $this->registerRoute('category/getById/{val}', 'CategoryService', 'getById', ['GET']);
        $this->registerRoute('category/update', 'CategoryService', 'update', ['PUT']);
        $this->registerRoute('category/myCategory', 'CategoryService', 'myCategory', ['GET']);

        //for Files
        $this->registerRoute('files', 'FileUploadService', '', ['GET']);
        $this->registerRoute('files/upload', 'FileUploadService', 'upload', ['POST']);
        $this->registerRoute('files/deleteById/{val}', 'FileUploadService', 'deleteById', ['DELETE']);
        $this->registerRoute('files/questions', 'FileUploadService', 'filterByQuestions', ['GET']);
        $this->registerRoute('files/getById/{val}', 'FileUploadService', 'getById', ['GET']);
        $this->registerRoute('files/update', 'FileUploadService', 'update', ['PUT']);
        $this->registerRoute('files/myFiles', 'FileUploadService', 'myFiles', ['GET']);

        //for Quiz Question Relation
        $this->registerRoute('quizQueRelation', 'QuizQuestionRelationService', '', ['GET', 'POST']);
        $this->registerRoute('quizQueRelation/deleteById/{val}', 'QuizQuestionRelationService', 'deleteById', ['DELETE']);
        $this->registerRoute('quizQueRelation/getById/{val}', 'QuizQuestionRelationService', 'getById', ['GET']);
        $this->registerRoute('quizQueRelation/getByQuizId/{val}', 'QuizQuestionRelationService', 'getByQuizId', ['GET']);
        $this->registerRoute('quizQueRelation/update', 'QuizQuestionRelationService', 'update', ['PUT']);
        $this->registerRoute('quizQueRelation/getNotMappedQuestions/{val}', 'QuizQuestionRelationService', 'getNotMappedQuestions', ['GET']);


        //for Quiz Student Mapping
        $this->registerRoute('quizStudent', 'QuizStudentMappingService', '', ['GET', 'POST']);
        $this->registerRoute('quizStudent/deleteById/{val}', 'QuizStudentMappingService', 'deleteById', ['DELETE']);
        $this->registerRoute('quizStudent/getById/{val}', 'QuizStudentMappingService', 'getById', ['GET']);
        $this->registerRoute('quizStudent/getByQuizId/{val}', 'QuizStudentMappingService', 'getByQuizId', ['GET']);
        $this->registerRoute('quizStudent/mapByEmail', 'QuizStudentMappingService', 'mapByEmail', ['POST']);
        $this->registerRoute('quizStudent/bulkMapByEmail', 'QuizStudentMappingService', 'bulkMapByEmail', ['POST']);



        //for Quiz Attempt
        $this->registerRoute('quizAttempt', 'QuizAttemptService', '', ['GET', 'POST']);
        $this->registerRoute('quizAttempt/deleteById/{val}', 'QuizAttemptService', 'deleteById', ['DELETE']);
        $this->registerRoute('quizAttempt/getById/{val}', 'QuizAttemptService', 'getById', ['GET']);
        $this->registerRoute('quizAttempt/getByQuizId/{val}', 'QuizAttemptService', 'getByQuizId', ['GET']);
        $this->registerRoute('quizAttempt/myData', 'QuizAttemptService', 'getByToken', ['GET']);
        $this->registerRoute('quizAttempt/update', 'QuizAttemptService', 'update', ['PUT']);
        $this->registerRoute('quizAttempt/saveData', 'QuizAttemptService', 'calculateQuizAttempt', ['POST']);
        $this->registerRoute('quizAttempt/startQuiz', 'QuizAttemptService', 'startQuizAttempt', ['POST']);



        //for quiz attempt detailed info
        $this->registerRoute('quizAttemptDetailedInfo', 'QuizAttemptDetailedInfoService', '', ['GET', 'POST']);
        $this->registerRoute('quizAttemptDetailedInfo/deleteById/{val}', 'QuizAttemptDetailedInfoService', 'deleteById', ['DELETE']);
        $this->registerRoute('quizAttemptDetailedInfo/getById/{val}', 'QuizAttemptDetailedInfoService', 'getById', ['GET']);
        $this->registerRoute('quizAttemptDetailedInfo/getByQuizId/{val}', 'QuizAttemptDetailedInfoService', 'getByQuizId', ['GET']);
        $this->registerRoute('quizAttemptDetailedInfo/myData', 'QuizAttemptDetailedInfoService', 'getByToken', ['GET']);
        $this->registerRoute('quizAttemptDetailedInfo/update', 'QuizAttemptDetailedInfoService', 'update', ['PUT', 'POST']);


        //for space quiz mapping
        $this->registerRoute('spaceQuizMapping', 'SpaceQuizMappingService', '', ['GET', 'POST']);
        $this->registerRoute('spaceQuizMapping/deleteById/{val}', 'SpaceQuizMappingService', 'deleteById', ['DELETE']);
        $this->registerRoute('spaceQuizMapping/getById/{val}', 'SpaceQuizMappingService', 'getById', ['GET']);
        $this->registerRoute('spaceQuizMapping/getByQuizId/{val}', 'SpaceQuizMappingService', 'getByQuizId', ['GET']);
        $this->registerRoute('spaceQuizMapping/getBySpaceId/{val}', 'SpaceQuizMappingService', 'getAllBySpaceId', ['GET']);
        $this->registerRoute('spaceQuizMapping/myData', 'SpaceQuizMappingService', 'getByToken', ['GET']);
        $this->registerRoute('spaceQuizMapping/update', 'SpaceQuizMappingService', 'update', ['PUT', 'POST']);
        $this->registerRoute('spaceQuizMapping/unMap/{val}', 'SpaceQuizMappingService', 'unMap', ['DELETE']);


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