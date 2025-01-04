<?php


require_once __DIR__ . "/../functions.php";
require_once __DIR__ . "/../utils/RouteTable.php";


class CommonService
{
    public $routeTable;

    public function __construct()
    {
        $this->routeTable = new RouteTable;
    }


    public function getApiEndpoints()
    {
        $loggedInUser = getLoggedInUserInfo();
        $routes = [];
        if ($loggedInUser != null) {
            $allroutes = $this->routeTable->getRoutes();
            foreach ($allroutes as $route) {
                $routeObj['route'] = $route['route'];
                $routeObj['allowedMethods'] = $route['allowedMethods'];
                array_push($routes, $routeObj);
            }

            return $routes;
        } else {
            echo sendResponse(false, 401, "Unauthorized access.");
        }
    }


    function getServerDatetime()
    {
        $now = date("Y-m-d H:i:s", time());
        echo sendResponse(true, 200, $now);
    }
}
