<?php

namespace Core\Middleware;

use Core\Http\Request;

interface MiddlewareInterface{

    function handle(Request $request):Request;
}