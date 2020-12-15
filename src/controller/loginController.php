<?php

namespace App\Controllers;
use App\Models\User;
use PsrJwt\Factory\Jwt;
use Psr\Http\Message\ServerRequestInterface as Request;


class LoginController{

    public static function login(Request $request){
        $token = '';
        //var_dump($request->getParsedBody()['email']);
        
        $user = User::where('username', $request->getParsedBody()['username'])->first();
    
        if(empty($user)){
            echo json_encode(["Error"=>"Usuario inexistente."]);
        } else {
            $passwordVerify = password_verify($request->getParsedBody()['password'], $user['password']);
            
            if($passwordVerify){
                $factory = new Jwt();

                $builder = $factory->builder();
        
                $token = $builder->setSecret($factory->key)
                                ->setPayloadClaim('username', $user['username'])
                                ->setPayloadClaim('area', $user['area'])
                                ->build();

                return $token->getToken();
            } else {
                echo json_encode(["Error"=>"Contraseña incorrecta."]);;
            }   
        }
        return $token;
    }

}


?>