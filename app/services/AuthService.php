<?php

namespace App\services;

use Core\Exception\UnauthorizedException;

class AuthService{

    public function __construct()
    {
        
    }


    public function verify_token($token){
        if($token != null && strlen($token) > 0){
            if($token != "shyam"){
                throw new UnauthorizedException("Token is expired or not valid.");
            }
        }
        else{
            throw new UnauthorizedException("Invalid Token Provided.");
        }
    }
}