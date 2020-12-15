<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Table;
use App\Middlewares\AuthMiddleware;


class TableController{

    public function getAll(Request $request, Response $response, $args){
        $rta = Table::get();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }
    /*
    public function getOne(Request $request, Response $response, $args){
        $rta = Table::find($args['id']);

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }
    */
    public function addOne(Request $request, Response $response, $args){
        $table = new Table;    
        $tables = Table::orderBy('table_id', 'DESC')->first();
        $lastId = !empty($tables) ? intval(substr($tables['table_id'],1,4)) : 0; 
        $lastId++;
        $newTableId = 'A'. str_pad(
                                !empty($request->getParsedBody()['id']) ? $request->getParsedBody()['id'] : $lastId, 
                                4, 
                                '0', 
                                STR_PAD_LEFT); 
        $tableExists = Table::where('table_id', $newTableId)->exists();

        switch($request->getParsedBody()['status']){
            case 1:
                $newTableStatus = "Con clientes esperando pedido";
                break;
            case 2:
                $newTableStatus = "Con clientes comiendo";
                break;
            case 3:
                $newTableStatus = "Con clientes pagando";
                break;
            default:
                $newTableStatus = "Cerrada";
                break;
        }        

        if(!empty($tableExists)){
            $response->getBody()->write(json_encode(['Error'=>"La mesa ya existe."],JSON_PRETTY_PRINT));
        } else { 
            if(!empty($newTableId))
            {
                $table['table_id'] = $newTableId;
                $table['status'] = $newTableStatus;
                if($table->save()){
                    $response->getBody()->write(json_encode(['Exito'=>"La mesa fue creada con éxito."],JSON_PRETTY_PRINT));
                } else {
                    $response->getBody()->write(json_encode(['Error'=>"No se pudo guardar la mesa."],JSON_PRETTY_PRINT));
                }
            }
            else{
                $response->getBody()->write(json_encode(['Error'=>"Parámetros no válidos."],JSON_PRETTY_PRINT));
            }
        }
        return $response;
    }
    
    public function updateOne(Request $request, Response $response, $args){
        $table = Table::find($args);
        $updateSql = array();
        
        

        if(is_numeric($request->getParsedBody()['status'])){
            switch($request->getParsedBody()['status']){
                case 0:
                    if(AuthMiddleware::validateAuth($request, 'socio')){
                        $newStatus = "Cerrada";
                    } else{
                        throw new \Slim\Exception\HttpForbiddenException($request);
                    }
                    break;
                case 1:
                    $newStatus = "Con clientes esperando pedido";
                    break;
                case 2:
                    $newStatus = "Con clientes comiendo";
                    break;
                case 3:
                    $newStatus = "Con clientes pagando";
                    break;
                default:
                    $newStatus = -1;
                    break;
            } 
        } else {
            $newStatus = -1;
        }
        

        if($newStatus != -1)
        {
            $updateSql['status'] = $newStatus;
            if($table[0]->update($updateSql)){
                $response->getBody()->write(json_encode(['Exito'=>"La mesa fue modificada con éxito."],JSON_PRETTY_PRINT));
            } else {
                $response->getBody()->write(json_encode(['Error'=>"No se pudo modificar la mesa."],JSON_PRETTY_PRINT));
            }
        } else {
            $response->getBody()->write(json_encode(['Error'=>"Parámetros no válidos."],JSON_PRETTY_PRINT));
        }
        
        return $response;
    }
    
    public function deleteOne(Request $request, Response $response, $args){    
        if(Table::destroy($args)){
            $response->getBody()->write(json_encode(['Exito'=>"La mesa fue eliminada con éxito."],JSON_PRETTY_PRINT));
        } else {
            $response->getBody()->write(json_encode(['Error'=>"No se pudo eliminar la mesa."],JSON_PRETTY_PRINT));
        }

        return $response;
    }




}

?>