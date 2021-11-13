<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;



require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
//require_once './middlewares/Logger.php';
require_once './middlewares/UsuarioMiddleware.php';
require_once './controllers/UsuarioController.php';
require_once '../API/UsuarioApi.php';
require_once '../API/MesaApi.php';
require_once '../API/ProductoApi.php';
require_once './middlewares/AutentificadorJWT.php'; //
require_once './models/Pedido.php';
require_once './middlewares/PedidoMiddleware.php';


#region NO TOCAR
// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

#endregion


// Log in - Usuario
$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioApi::class . ':Login_Usuario');
});

#region EMPLEADO
/*                EMPLEADO

      - OPCIÓN : sacar foto de la mesa con integrantes y relacionar con pedido
      - SOCIOS/ADMIN : pueden ver el estado de todos los pedidos, en todo momento
      - CADA EMPLEADO TIENE UNA LISTA DE PENDIENTES

*/

//ver listado de pendientes ---------- FALTAN COSAS
$app->group('/pendientes', function (RouteCollectorProxy $group) {
  $group->get('[/]', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Metodo GET");
    return $response;
  })->add(\UsuarioMiddleware::class . ':ValidarToken');
});

// Listar todas las entidades
$app->group('/listarTodos', function (RouteCollectorProxy $group) {
  $group->get('[/pedidos]', \PedidoMiddleware::class . ':TraerTodos');
  $group->get('/{id_empleado}', \PedidoMiddleware::class . ':TraerTodos_PorUsuario');
});

#endregion

#region PEDIDOS
/*            PEDIDOS
    - Listar todos los pedidos por sector de empleado
    - Registrar un pedido nuevo
    - Modificar un pedido por id del pedido
    - Eliminar un pedido por id del pedido
*/

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('/listarPorSector/{sector}', \PedidoMiddleware::class . ':TraerTodos_PorSector');
  $group->post('/registrarPedido[/]', \PedidoMiddleware::class . ':CargarUno');
  $group->put('/{id_pedido}', \PedidoMiddleware::class . ':ModificarUno');
  $group->delete('/{id_pedido}', \PedidoMiddleware::class . ':BorrarUno');
});
#endregion

#region MESAS
/*        MESAS
    - Listar todas las mesas
    - Listar mesas por estado
    - Registrar una mesa nueva
    - Modificar una mesa por id de mesa
    - Eliminar una mesa por id de mesa
*/
$app->group('/mesa', function (RouteCollectorProxy $group) {
  $group->get('/listarTodos[/]', \PedidoMiddleware::class . ':TraerTodos');
  $group->get('/listarPorSector/{sector}', \PedidoMiddleware::class . ':TraerTodos_PorSector');
  $group->post('/registrarPedido[/]', \PedidoMiddleware::class . ':CargarUno');
  $group->put('/{id_pedido}', \PedidoMiddleware::class . ':ModificarUno');
  $group->delete('/{id_pedido}', \PedidoMiddleware::class . ':BorrarUno');
});
#endregion

#region PRODUCTOS

/*        PRODUCTOS
    - Listar todas las productos
    - Listar mesas por estado
    - Registrar una mesa nueva
    - Modificar una mesa por id de mesa
    - Eliminar una mesa por id de mesa
*/
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('/listarTodos[/]', \ProductoApi::class . ':TraerTodos');
  $group->get('/listarPorSector/{sector}', \ProductoApi::class . ':TraerTodos_PorSector');
  $group->post('/registrarProducto[/]', \ProductoApi::class . ':CargarUno');
  $group->put('/{id_producto}', \ProductoApi::class . ':ModificarUno');
  $group->delete('/{id_producto}', \ProductoApi::class . ':BorrarUno');
});
#endregion


$app->run();
