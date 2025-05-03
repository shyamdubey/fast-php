<?php


namespace App\Middleware;

use App\services\AuthService;
use Core\Enums\HttpStatus;
use Core\Exception\UnauthorizedException;
use Core\Http\Request;
use Core\Http\Response;
use Core\Log\LoggerFactory;
use Core\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface{
    private LoggerFactory $logger;

    public function __construct()
    {
        $this->logger = LoggerFactory::get_logger(self::class);
    }

    public function handle($request):Request
    {
        $this->logger->info("Calling Auth Middle ware");
        $headers = $request->get_headers();
        if(!isset($headers['Authorization'])){
           throw new UnauthorizedException("Authorization header not present.");
        }
        else{
            $token = $headers['Authorization'];
            if(str_contains($token, "Bearer ")){
                $token = explode("Bearer ",$token)[1];
                $authService = new AuthService;
                $authService->verify_token($token);

            }
            else{
                throw new UnauthorizedException("Bearer token not present in Authorization header.");
            }
        }
        return $request;
    }

}