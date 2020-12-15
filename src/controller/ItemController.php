<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Item;
use App\Controllers\UserController;


class ItemController{

    public function getAll(Request $request, Response $response, $args){
        $rta = Item::get();

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }
    /*
    public function getOne(Request $request, Response $response, $args){
        $rta = Item::find($args['id']);

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }
    */
    public function addOne(Request $request, Response $response, $args){
        $item = new Item;    
        $items = Item::orderBy('id', 'DESC')->first();
        $lastId = !empty($items) ? $items['id'] : 0; 
        $lastId++;
        $newItemId = $lastId;
        $newDesc = $request->getParsedBody()['description'];
        $newPrice = $request->getParsedBody()['price'];
        $newArea = $request->getParsedBody()['responsable'];
        $itemExists = Item::where('id', $newItemId)->exists();     

        if(!empty($itemExists)){
            $response->getBody()->write(json_encode(['Error'=>"El producto ya existe."],JSON_PRETTY_PRINT));
        } else { 
            if(!empty($newItemId) && !empty($newDesc) && !empty($newArea))
            {
                $item['id'] = $newItemId;
                $item['description'] = $newDesc;
                $item['price'] = $newPrice;
                $item['responsable'] = $newArea;
                if($item->save()){
                    $response->getBody()->write(json_encode(['Exito'=>"El producto fue creado con éxito."],JSON_PRETTY_PRINT));
                } else {
                    $response->getBody()->write(json_encode(['Error'=>"No se pudo guardar el producto."],JSON_PRETTY_PRINT));
                }
            }
            else{
                $response->getBody()->write(json_encode(['Error'=>"Parámetros no válidos."],JSON_PRETTY_PRINT));
            }
        }
        return $response;
    }
    
    public function updateOne(Request $request, Response $response, $args){
        $item = Item::find($args);
        $updateSql = array();
        
        $newDesc = $request->getParsedBody()['description'];
        $newPrice = $request->getParsedBody()['price'];
        $newArea = $request->getParsedBody()['responsable'];       

        if(!empty($item))
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

            if($item[0]->update($updateSql)){
                $response->getBody()->write(json_encode(['Exito'=>"El producto fue modificado con éxito."],JSON_PRETTY_PRINT));
            } else {
                $response->getBody()->write(json_encode(['Error'=>"No se pudo modificar el producto."],JSON_PRETTY_PRINT));
            }
        } else {
            $response->getBody()->write(json_encode(['Error'=>"Producto no existente."],JSON_PRETTY_PRINT));
        }
        
        return $response;
    }
    
    public function deleteOne(Request $request, Response $response, $args){    
        if(Item::destroy($args)){
            $response->getBody()->write(json_encode(['Exito'=>"El producto fue eliminada con éxito."],JSON_PRETTY_PRINT));
        } else {
            $response->getBody()->write(json_encode(['Error'=>"No se pudo eliminar el producto."],JSON_PRETTY_PRINT));
        }

        return $response;
    }

}

?>