<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;


class UserController{

    public function getAll(Request $request, Response $response, $args){
        $rta = User::get();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function getOne(Request $request, Response $response, $args){
        $rta = User::find($args['id']);

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function addOne(Request $request, Response $response, $args){
        $user = new User;     
        $newUsername = $request->getParsedBody()['username'];
        $newPass = $request->getParsedBody()['password'];
        $newArea = $request->getParsedBody()['area'];
        
        $userExists = User::where('username', $newUsername)->exists();

        if(!empty($userExists)){
            $response->getBody()->write(json_encode(['Error'=>"El usuario ya existe."],JSON_PRETTY_PRINT));
        } else { 
            if( !empty($newUsername) && !empty($newPass) && !empty($newArea) && UserController::validateArea($newArea)){
                $user['username'] = $newUsername;
                $user['password'] = password_hash($newPass, PASSWORD_BCRYPT);
                $user['area'] = $newArea;
                if($user->save()){
                    $response->getBody()->write(json_encode(['Exito'=>"Usuario creado correctamente."],JSON_PRETTY_PRINT));
                } else {
                    $response->getBody()->write(json_encode(['Error'=>"Error al guardar el usuario."],JSON_PRETTY_PRINT));
                }            
            }
            else{
                $response->getBody()->write(json_encode(['Error'=>"Parámetros no válidos."],JSON_PRETTY_PRINT));
            }
        }
        return $response;
    }

    public function updateOne(Request $request, Response $response, $args){
        $user = User::find($args);
        $updateSql = array();
        
        $newUsername = $request->getParsedBody()['username'];
        $newPass = $request->getParsedBody()['password'];
        $newArea = $request->getParsedBody()['area'];

        if(!empty($user)){
            if(!empty($newUsername)){
                $updateSql['username'] = $newUsername;
                $response->getBody()->write(json_encode(['Exito'=>"Nombre de usuario modificado correctamente."],JSON_PRETTY_PRINT));
            } else {
                $response->getBody()->write(json_encode(['Error'=>"Username no válido."],JSON_PRETTY_PRINT));
            }
            
            if(!empty($newPass)){
                $updateSql['password'] = password_hash($newPass, PASSWORD_BCRYPT);
                $response->getBody()->write(json_encode(['Exito'=>"Contraseña modificada correctamente."],JSON_PRETTY_PRINT));
            } else {
                $response->getBody()->write(json_encode(['Error'=>"Contraseña no válida."],JSON_PRETTY_PRINT));
            }
    
            if(!empty($newArea)){
                if(UserController::validateArea($newArea)){
                    $updateSql['area'] = $newArea;
                    $response->getBody()->write(json_encode(['Exito'=>"Area de usuario modificada correctamente."],JSON_PRETTY_PRINT));
                } else {
                    $response->getBody()->write(json_encode(['Error'=>"Sector no válido."],JSON_PRETTY_PRINT));
                }
            }

            if($user[0]->update($updateSql)){
                $response->getBody()->write(json_encode(['Exito'=>"El usuario fue modificado con éxito."],JSON_PRETTY_PRINT));
            } else {
                $response->getBody()->write(json_encode(['Error'=>"No se pudo modificar el usuario."],JSON_PRETTY_PRINT));
            }
        } else {
            $response->getBody()->write(json_encode(['Error'=>"Usuario no existente."],JSON_PRETTY_PRINT));
        }

        
        
        return $response;
    }

    public function deleteOne(Request $request, Response $response, $args){    
        User::destroy($args);
        $response->getBody()->write(json_encode(['Exito'=>"Usuario eliminado correctamente."],JSON_PRETTY_PRINT));
        return $response;
    }

    public static function validateArea($args){
        return in_array($args, ['admin', 'socio', 'mozo', 'bartender', 'cocinero', 'cervecero']);
    }
}

?>