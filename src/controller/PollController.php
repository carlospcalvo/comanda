<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Poll;
use App\Models\Table;
use App\Controllers\UserController;


class PollController{

    public function getAll(Request $request, Response $response, $args){
        $rta = Poll::get();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }
    /*
    public function getOne(Request $request, Response $response, $args){
        $rta = Poll::find($args['id']);

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }
    */
    public function addOne(Request $request, Response $response, $args){
        $poll = new Poll;    
        
        $table_id = $request->getParsedBody()['table_id'];
        $table_value = $request->getParsedBody()['table_value'];
        $rest_value = $request->getParsedBody()['restaurant_value'];
        $chef_value = $request->getParsedBody()['chef_value'];
        $waiter_value = $request->getParsedBody()['waiter_value'];
        $comment = $request->getParsedBody()['comment'];
        $poll_array = array($table_value, $rest_value, $rest_value, $chef_value, $waiter_value);
        $table = Table::where('table_id', $table_id)->get();
        /*
        if(!empty($pollExists)){
            $response->getBody()->write(json_encode(['Error'=>"El producto ya existe."],JSON_PRETTY_PRINT));
        } else { 
            
        }
        */

        if(!empty($table)){
            if($table[0]['status'] == 'Con clientes pagando'){
                if(!empty($table_id) && !empty($table_value) && !empty($rest_value) && !empty($chef_value) && !empty($waiter_value)){
                    if($this->checkPollScore($poll_array)){
                        $poll['id_table'] = $table_id;
                        $poll['table_value'] = $table_value;
                        $poll['restaurant_value'] = $rest_value;
                        $poll['chef_value'] = $chef_value;
                        $poll['waiter_value'] = $waiter_value;
                        $poll['comment'] = $comment;
                        
                        if($poll->save()){
                            $response->getBody()->write(json_encode(['Exito'=>"La encuesta fue creada con éxito."],JSON_PRETTY_PRINT));
                        } else {
                            $response->getBody()->write(json_encode(['Error'=>"No se pudo guardar la encuesta."],JSON_PRETTY_PRINT));
                        }
                    } else {
                        $response->getBody()->write(json_encode(['Error'=>"Parámetros no válidos."],JSON_PRETTY_PRINT));
                    } 
                }
                else{
                    $response->getBody()->write(json_encode(['Error'=>"Campos incompletos."],JSON_PRETTY_PRINT));
                }
            } else {
                $response->getBody()->write(json_encode(['Error'=>"La mesa aún no terminó de comer."],JSON_PRETTY_PRINT));
            }
            
        } else {
            $response->getBody()->write(json_encode(['Error'=>"La mesa no existe."],JSON_PRETTY_PRINT));
        }        

        return $response;
    }
    /*
    public function updateOne(Request $request, Response $response, $args){
        $poll = Poll::find($args);
        $updateSql = array();
        
        $newDesc = $request->getParsedBody()['description'];
        $newPrice = $request->getParsedBody()['price'];
        $newArea = $request->getParsedBody()['responsable'];       

        if(!empty($poll))
        {
            if(!empty($newDesc)){
                $updateSql['description'] = $newDesc;
            }

            if(!empty($newPrice)){
                $updateSql['price'] = $newPrice;
            }

            if(!empty($newArea)){
                if(UserController::validateArea($newArea)){
                    $updateSql['responsable'] = $newArea;
                } else {
                    $response->getBody()->write(json_encode(['Error'=>"Area del producto no válida."],JSON_PRETTY_PRINT));
                }
            }

            if($poll[0]->update($updateSql)){
                $response->getBody()->write(json_encode(['Exito'=>"El producto fue modificado con éxito."],JSON_PRETTY_PRINT));
            } else {
                $response->getBody()->write(json_encode(['Error'=>"No se pudo modificar el producto."],JSON_PRETTY_PRINT));
            }
        } else {
            $response->getBody()->write(json_encode(['Error'=>"Producto no existente."],JSON_PRETTY_PRINT));
        }
        
        return $response;
    }
    */
    /*
    public function deleteOne(Request $request, Response $response, $args){    
        if(Poll::destroy($args)){
            $response->getBody()->write(json_encode(['Exito'=>"El producto fue eliminada con éxito."],JSON_PRETTY_PRINT));
        } else {
            $response->getBody()->write(json_encode(['Error'=>"No se pudo eliminar el producto."],JSON_PRETTY_PRINT));
        }

        return $response;
    }
    */

    public function checkPollScore($array){
        $check = false;

        foreach($array as $item){
            if(is_numeric($item) && intval($item) > 0 && intval($item) <= 10){
                $check = true;
            } else {
                $check = false;
                break;
            }
        }

        return $check;
    }
}

?>