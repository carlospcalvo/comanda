<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Models\User;
use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\LoginController;
use App\Controllers\TableController;
use App\Controllers\ItemController;
use App\Controllers\PollController;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use Slim\Psr7\Message;
use Config\Database;
use PsrJwt\Factory\JwtMiddleware;
use PsrJwt\Handler\Json;



//require __DIR__.'/config/database.php';
require __DIR__ . '/vendor/autoload.php';
$key = 'todorojo';
$app = AppFactory::create();
$conn = new Database();
//si no esta en la raiz hay que indicarle la raiz
//$app->setBasePath('/comanda');
//$app->setBasePath('public_html');
$app->addErrorMiddleware(true, false, false);

//LOGIN
$app->post('/login[/]', function (Request $request, Response $response, $args){

    $rta = LoginController::login($request);

    if(!empty($rta)){
        $token = ["JSON Web Token" => $rta];
        $response->getBody()->write(json_encode($token));
    }

    return $response;
})->add(new JsonMiddleware);

//RUTAS USUARIO
$app->group('/usuario', function(RouteCollectorProxy $group){
    
    $group->get('[/]', UserController::class . ":getAll")->add(new AuthMiddleware('admin', 'socio'));

    $group->get('/{id}', UserController::class . ":getOne")->add(new AuthMiddleware('admin', 'socio'));

    $group->post('[/]', UserController::class . ":addOne");

    $group->put('/{id}', UserController::class . ":updateOne")->add(new AuthMiddleware('admin', 'socio'));
    
    $group->delete('/{id}', UserController::class . ":deleteOne")->add(new AuthMiddleware('admin', 'socio'));

})->add(new JsonMiddleware);

$app->group('/pedidos', function(RouteCollectorProxy $group){
    
    $group->get('[/]', OrderController::class . ":getAll")->add(new AuthMiddleware('admin', 'socio', 'mozo'));

    $group->get('/{id}', OrderController::class . ":getOne");

    $group->post('[/]', OrderController::class . ":addOne")->add(new AuthMiddleware('admin', 'socio', 'mozo'));

    $group->put('/{id}', OrderController::class . ":updateOne");

    $group->delete('/{id}', OrderController::class . ":deleteOne")->add(new AuthMiddleware('admin', 'socio'));

})->add(new JsonMiddleware);

$app->group('/mesas', function(RouteCollectorProxy $group){
    
    $group->get('[/]', TableController::class . ":getAll")->add(new AuthMiddleware('admin'));

    $group->post('[/]', TableController::class . ":addOne");

    $group->put('/{id}', TableController::class . ":updateOne");
    
    $group->delete('/{id}', TableController::class . ":deleteOne")->add(new AuthMiddleware('admin'));

})->add(new AuthMiddleware('admin', 'socio'))
  ->add(new JsonMiddleware);

$app->group('/productos', function(RouteCollectorProxy $group){
    
    $group->get('[/]', ItemController::class . ":getAll");

    $group->post('[/]', ItemController::class . ":addOne");

    $group->put('/{id}', ItemController::class . ":updateOne");
    
    $group->delete('/{id}', ItemController::class . ":deleteOne");

})->add(new AuthMiddleware('admin', 'socio'))
  ->add(new JsonMiddleware);

$app->group('/encuestas', function(RouteCollectorProxy $group){

    $group->get('[/]', PollController::class . ":getAll")->add(new AuthMiddleware('admin', 'socio'));

    //$group->get('/{id}', PollController::class . ":getOne");

    $group->post('[/]', PollController::class . ":addOne");

    //$group->put('/{id}', PollController::class . ":updateOne");

    //$group->delete('/{id}', PollController::class . ":deleteOne")->add(new AuthMiddleware('admin', 'socio'));

})->add(new JsonMiddleware);


$app->addBodyParsingMiddleware();
$app->run();



// hosts gratuitos - tienen que tener c panel
// ar.000webhost.com
// hostinger.com.ar 