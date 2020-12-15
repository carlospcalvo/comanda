<?php

namespace App\Middlewares;

use App\Controllers\UserController;
use App\Models\Usuario;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use PsrJwt\Factory\Jwt;
use ReallySimpleJWT\Token;


class AuthMiddleware
{
    public $roles;

    public function __construct(string $role1, string $role2 = '', string $role3 = '', string $role4 = '', string $role5 = '')
    {
        $this->roles = array();
        array_push($this->roles, $role1, $role2, $role3, $role4, $role5);
    }
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $valido = false;
        $allowed = false;
        $token = $request->getHeader('token')[0] ?? '';
        $jwt = new Jwt();

        if(!empty($token)){
            $valido = Token::validate($token, $jwt->key);
            $payload = Token::getPayload($token, $jwt->key);
            $allowed = in_array($payload['area'], $this->roles);
        }

        if (!$valido || !$allowed) {
            //$response = new Response();
            throw new \Slim\Exception\HttpForbiddenException($request);
        }
        
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
        $resp = new Response();
        $resp->getBody()->write($existingContent);
        
        return $resp;
    }

    public static function validateAuth($request, $allowed_role){
        $valido = false;
        $allowed = false;
        $token = $request->getHeader('token')[0] ?? '';
        $jwt = new Jwt();

        if(!empty($token)){
            $valido = Token::validate($token, $jwt->key);
            $payload = Token::getPayload($token, $jwt->key);
            $allowed = $payload['area'] == $allowed_role;
            //$allowed = in_array($payload['user_type'], $allowed_roles);
        }

        return $valido && $allowed;
    }

    public static function getRole($request){
        $token = $request->getHeader('token')[0] ?? '';
        $jwt = new Jwt();
        $role = 'Token o rol no válido.';

        if(!empty($token) && Token::validate($token, $jwt->key)){
            $payload = Token::getPayload($token, $jwt->key);
            $role = $payload['area'];
        }

        return $role;
    }

}

