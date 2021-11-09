<?php

use FastRoute\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './db/AccesoDatos.php';
require_once 'middlewares/Logger.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->setBasePath('/public');

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
  $response->getBody()->write("hola alumnos de los jueves!");
  return $response;
});

// peticiones
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->put('/{usuario}', \UsuarioController::class . ':ModificarUno');
  $group->delete('/{usuario}', \UsuarioController::class . ':BorrarUno');
  $group->post('/login', \UsuarioController::class . ':Login');
});

//Ejercicio 1
$app->group('/credenciales', function (RouteCollectorProxy $group) {
  $group->post('[/]', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Metodo POST");
    return $response;
  });
  $group->get('[/]', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Metodo GET");
    return $response;
  });
})->add(\Logger::class . ':VerificadorDeCredenciales');

//Ejercicio 2
$app->group('/json', function (RouteCollectorProxy $group) {
  $group->get('[/]', function (Request $request, Response $response, $args) {
    $json = json_encode(array("mensaje" => "API => GET", "status" => 200));
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
  });
  $group->post('[/]', function (Request $request, Response $response, $args) {
    $json = json_encode(array("mensaje" => "API => POST", "status" => 200));
    $response->getBody()->write($json);
    return $response->withHeader('Content-Type', 'application/json');
  })->add(\Credenciales::class . ':ValidarPostJSON');
});





$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . 'ValidarUsuario');
});


// Run app
$app->run();
