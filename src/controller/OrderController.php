<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Order;
use App\Models\Order_line;
use App\Models\Table;
use Illuminate\Database\Capsule\Manager as DB;
use App\Middlewares\AuthMiddleware;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;

class OrderController{

    public function getAll(Request $request, Response $response, $args){
        $pedidos = Order::get();

        $rta = array();

        foreach($pedidos as $pedido){
            array_push($rta, $this->getOrderLines($pedido));
        }

        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function getOne(Request $request, Response $response, $args){
        if(!empty($request->getHeader('token'))){
            if(is_numeric($args['id'])){
                $pedido = Order::find($args['id']);
                $rta = array($this->getOrderLines($pedido)); 
            } else {
                $rta = $this->getAllOrdersPerSector($args);
            }
        } else {
            $codigo = explode('-', $args['id']);
            $pedido = $codigo[0];
            $mesa = $codigo[1];
            //echo "Pedido: $pedido <br> Mesa: $mesa";

            $rta = $this->getOrderInfo($mesa, $pedido);
        }
        


        $response->getBody()->write(json_encode($rta, JSON_PRETTY_PRINT));
        return $response;
    }

    public function addOne(Request $request, Response $response, $args){
        $order = new Order;   
        
        //$lastId = Order::orderBy('id', 'DESC')->first()['id'];
        $newGuid = substr(md5(time()), 0, 5);   //'A'. str_pad($lastId++, 4, '0', STR_PAD_LEFT);
        $table = Table::where('id', $request->getParsedBody()['id_table'])->get(); 
        //$newExpectedTime = $request->getParsedBody()['expectedTime'];
        $items = $request->getParsedBody()['items'];


        $orderExists = Order::where('guid', $newGuid)->exists();
            
        if(!empty($orderExists)){
            $response->getBody()->write(json_encode(['Error'=>"El pedido ya existe."],JSON_PRETTY_PRINT));
        } else { 
            if($table[0]['status'] != 'Cerrada'){
                $response->getBody()->write(json_encode(['Error'=>"La mesa ya tiene un pedido creado."],JSON_PRETTY_PRINT));
            } else {
                if(!empty($newGuid) && !empty($table[0]['id'])){
                    $order['guid'] = $newGuid;
                    $order['id_table'] = $table[0]['id'];
                    $order['status'] = 'Recepcionado';
                    $table[0]['status'] = 'Con clientes esperando pedido';
                    if($order->save() && $table[0]->save()){
                        $newOrderId = Order::select('id')->where('guid', $newGuid)->get(); 
                        
                        foreach($items as $item){
                            $lines = new Order_line;  
                            $lines['order_id'] = $newOrderId[0]['id'];
                            $lines['item_id'] = $item[0];
                            $lines['quantity'] = $item[1];
                            $lines['status'] = 'Recepcionado';
                            if(!$lines->save()){
                                $response->getBody()->write(json_encode(['Error'=>"Error al agregar los items del pedido."],JSON_PRETTY_PRINT));
                            }
                        }
                        /*
                        if($lines->save()){
                            $response->getBody()->write(json_encode(['Exito'=>"Pedido creado con éxito!"],JSON_PRETTY_PRINT));
                        } else {
                            $response->getBody()->write(json_encode(['Error'=>"Error al agregar los items del pedido."],JSON_PRETTY_PRINT));
                        }
                        */
                        $response->getBody()->write(json_encode(['Exito'=>"Pedido creado con éxito!"],JSON_PRETTY_PRINT));
                    } else{
                        $response->getBody()->write(json_encode(['Error'=>"No se pudo guardar el pedido."],JSON_PRETTY_PRINT));
                    }
                    
                } else {
                    $response->getBody()->write(json_encode(['Error'=>"Parámetros no válidos."],JSON_PRETTY_PRINT));
                }
            }
            
        }

        return $response;
    }

    public function updateOne(Request $request, Response $response, $args){
        $newStatus = $request->getParsedBody()['status'];
        $role = AuthMiddleware::getRole($request);

        switch($request->getParsedBody()['status']){
            case 0:
                $newStatus = "Recepcionado";
                break;
            case 1:
                $newStatus = "En preparación";
                break;
            case 2:
                $newStatus = "Listo para servir";
                break;
            case 3:
                $newStatus = "Servido";
                break;
            default:
                $newStatus = -1;
                break;
        } 
        //si es socio, admin o mozo modifica el pedido, si es cervcero, cocinero o bartender modifica las lineas
        if(in_array($role, ['admin', 'socio', 'mozo'])){
            $order = Order::where('id', $args['id'])->get(); //find($args)->first();
            $table = Table::where('id', $request->getParsedBody()['id_table'])->get();
            $updateOrder = array();
            
            if(!empty($order)){
                if(!empty($newStatus)){
                    if($newStatus == -1){
                        $response->getBody()->write(json_encode(['Error'=>"Estado del pedido no válido."],JSON_PRETTY_PRINT));
                    } else {  
                        $updateOrder['status'] = $newStatus;                         
                    }                    
                }                

                if(!empty($table[0]['id'])){
                    $updateOrder['id_table'] = $table;
                }

            } else {
                $response->getBody()->write(json_encode(['Error'=>"El pedido no existe."],JSON_PRETTY_PRINT));
            }            

            if($order[0]->update($updateOrder)){
                $response->getBody()->write(json_encode(['Exito'=>"Pedido modificado con éxito."],JSON_PRETTY_PRINT));
            }

        } else if(in_array($role, ['cervecero', 'cocinero', 'bartender'])) {
            $item_id = $request->getParsedBody()['item_id'];
            $newExpectedTime = $request->getParsedBody()['expectedTime'];
            $line = Order_line::where('order_id', $args['id'])->where('item_id', $item_id)->get();

            if(!empty($newStatus)){
                if($newStatus == -1){
                    $response->getBody()->write(json_encode(['Error'=>"Estado del pedido no válido."],JSON_PRETTY_PRINT));
                } else {  
                    //$updateOrder['status'] = $newStatus;
                    
                    $line[0]['status'] = $newStatus;
                    $response->getBody()->write(json_encode(['Exito'=>"Estado del item modificado."],JSON_PRETTY_PRINT));
                }  
            }

            if(!empty($newExpectedTime)){
                if(is_numeric($newExpectedTime) && $newExpectedTime > 0){
                    //$updateOrder['expected_time'] = intval($newExpectedTime);
                    $line[0]['expected_time'] = intval($newExpectedTime);
                    $response->getBody()->write(json_encode(['Exito'=>"Tiempo estimado de preparacion del item modificado."],JSON_PRETTY_PRINT));
                } else {
                    $response->getBody()->write(json_encode(['Error'=>"Tiempo estimado de preparación del pedido no válido."],JSON_PRETTY_PRINT));
                }
            } 

            if($line[0]->save()){
                
                $order = Order::where('id', $args['id'])->get();
                $lines = Order_line::where('order_id', $args['id'])->get()->count();
                $linesPrep = Order_line::where('order_id', $args['id'])->where('status', 'En preparación')->get()->count();
                $linesReady = Order_line::where('order_id', $args['id'])->where('status', 'Listo para servir')->get()->count();
                $linesServed = Order_line::where('order_id', $args['id'])->where('status', 'Servido')->get()->count();

                if($lines == $linesPrep){
                    $order[0]->update(['status'=>'En preparación']); 
                }  else if($lines == $linesReady){
                    $order[0]->update(['status'=>'Listo para servir']); 
                } else if($lines == $linesServed){
                    $order[0]->update(['status'=>'Servido']); 
                }

                $response->getBody()->write(json_encode(['Exito'=>"Pedido modificado con éxito."],JSON_PRETTY_PRINT));
                
            } else {
                $response->getBody()->write(json_encode(['Error'=>"No se pudo modificar el pedido."],JSON_PRETTY_PRINT));
            }
            /*
            $affected = DB::table('order_lines')
                            ->where('order_id', $args['id'])
                            ->where('items.responsable', $role)
                            ->where('items.id', $item_id)
                            ->join('items', 'order_lines.item_id', '=', 'items.id')
                            ->get();
                            //->update($updateOrder);
            */
            

            //$status[0]['status'] = $newStatus;
            /*
            if($affected == 1){
                

                $response->getBody()->write(json_encode(['Exito'=>"Pedido modificado con éxito."],JSON_PRETTY_PRINT));
            } else if($affected == 0){
                $response->getBody()->write(json_encode(['Error'=>"No se pudo modificar el pedido."],JSON_PRETTY_PRINT));
            } else {
                $response->getBody()->write(json_encode(['Atención'=>"Más de un item fue modifcado en el pedido."],JSON_PRETTY_PRINT));
            }
            */
        } else {
            throw new \Slim\Exception\HttpForbiddenException($request);
        }

        return $response;
    }

    public function deleteOne(Request $request, Response $response, $args){    
        Order::destroy($args);
        
        $response->getBody()->write("Pedido eliminado con éxito!");
        return $response;
    }

    /*
    public function validateStatus($args){
        return in_array($args, ["Recepcionado", "En preparación", "Listo para servir", "Servido"]);
    }
    */

    public function getOrderLines($pedido){

        $items = Order_line::select('items.description AS producto',
                                    'order_lines.quantity AS cantidad', 
                                    'items.price AS precio',
                                    'order_lines.status AS estado',
                                    'order_lines.expected_time AS tiempo estimado (min.)' 
                                    )
                            ->join('items', 'order_lines.item_id', '=', 'items.id')
                            ->where('order_id', $pedido['id'])
                            ->get();

        $total = Order_line::where('order_id', $pedido['id'])
                                ->join('items', 'order_lines.item_id', '=', 'items.id')
                                ->sum(DB::raw('(order_lines.quantity * items.price)'));
        $array = ['Pedido'=>$pedido, 'Items'=>$items, 'Total'=>$total];

        return $array;
    }

    
    public function getRemainingTime($pedido, $mesa){
        $query = Order_line::select('order_lines.creation_date', 'order_lines.expected_time')
                            ->join('orders', 'orders.id', '=', 'order_lines.order_id')
                            ->join('tables', 'tables.id', '=', 'orders.id_table')
                            ->where('orders.guid', $pedido)
                            ->where('tables.table_id', $mesa)
                            ->get();
        //var_dump($query[0]);
        $horaActual = date_timestamp_get(date_create(NULL, new DateTimeZone('America/Argentina/Buenos_Aires')));
        $horaEntrega = date_timestamp_get($query[0]['creation_date']) +$query[0]['expected_time']*60;//$order[0]['expected_time'] * 60;        
        $diff = $horaEntrega - $horaActual;
        $diffMinutes = floor($diff/60);
        /*
        echo "Hora actual: $horaActual". PHP_EOL;
        //echo "Tiempo estimado: " .$order[0]['expected_time'].PHP_EOL;
        echo "Tiempo estimado: " .$expectedTime.PHP_EOL;
        echo "hora de creacion: " .date_timestamp_get($order[0]['creation_date']).PHP_EOL;
        echo "Hora entrega: $horaEntrega". PHP_EOL;
        */
        
        return $diffMinutes;
    }
    

    public function getAllOrdersPerSector($args){
        $pedidos = Order::select('orders.id AS id_pedido',
                                'orders.guid AS guid_pedido',
                                'tables.table_id AS id_mesa',
                                'items.description AS producto',
                                'order_lines.quantity AS cantidad', 
                                'order_lines.status AS estado', 
                                'order_lines.expected_time')
                                ->where('items.responsable', $args)
                                ->join('order_lines', 'order_lines.order_id', '=', 'orders.id')
                                ->join('items', 'order_lines.item_id', '=', 'items.id')
                                ->join('tables', 'tables.id', '=', 'orders.id_table')
                                ->get();
        
        $rta = array($pedidos);
        
        return $rta;
    }

    public function getOrderInfo($mesa, $pedido){
        /*
            'order_lines.creation_date',
            'order_lines.expected_time'
        */
        $items = Order_line::select('items.description AS producto',
                                    'order_lines.quantity AS cantidad', 
                                    'items.price AS precio',
                                    'order_lines.status AS estado',
				    'order_lines.expected_time'
                                    )
                            ->join('items', 'order_lines.item_id', '=', 'items.id')
                            ->join('orders', 'orders.id', '=', 'order_lines.order_id')
                            ->join('tables', 'tables.id', '=', 'orders.id_table')
                            ->where('orders.guid', $pedido)
                            ->where('tables.table_id', $mesa)
                            ->get();
        
        $total = Order_line::where('orders.guid', $pedido)
                            ->join('items', 'order_lines.item_id', '=', 'items.id')
                            ->join('orders', 'orders.id', '=', 'order_lines.order_id')
                            ->sum(DB::raw('(order_lines.quantity * items.price)'));

        $array_items = array();
        $item_number = 1;
        foreach($items as $item){
            if($item['expected_time'] > 0){
                $time = $this->getRemainingTime($pedido, $mesa);//$this->getRemainingTime($items, $item['expected_time']);
                $time = $this->getRemainingTime($pedido, $mesa);//$this->getRemainingTime($items, $item['expected_time']);
                if($time > 0 && !in_array($item['estado'], ['Servido', 'Listo para servir'])){
                    $estimado = "Faltan ". $time ." minutos para que su pedido sea servido.";
                } else if($time <= 0 && in_array($item['estado'], ['Servido', 'Listo para servir']) || $time > 0 && in_array($item['estado'], ['Servido', 'Listo para servir'])){
                    $estimado = "Su pedido ya está listo y pronto va a ser servido.";
                } else if($time < 0 && !in_array($item['estado'], ['Servido', 'Listo para servir'])){
                    $estimado = "Su pedido tiene una demora de ". abs($time) ." minutos.";
                }
                
            } else { 
                $estimado = "Su pedido aún no está en preparación.";
            }
            array_push($array_items, ["Item $item_number"=>$item, 'Tiempo estimado'=>$estimado]);
            $item_number++;
        }
        $rta = ['Mesa'=>$mesa, 'Pedido'=>$pedido, 'Items'=>$array_items, 'Total'=>$total];
        
        return $rta;
    }
}
?>