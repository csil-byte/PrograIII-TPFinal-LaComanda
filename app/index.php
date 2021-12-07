<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);


#region uses y requires
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
require_once '../API/EncuestaApi.php';
require_once '../API/FacturaApi.php';
require_once './middlewares/AutentificadorJWT.php'; //
require_once './models/Pedido.php';
require_once '../API/ComandaApi.php';
require_once '../app/models/ReportePDF.php';
require_once './middlewares/PedidoMiddleware.php';

#endregion

#region NO TOCAR
// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/comanda');

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

//ver listado de pendientes ---------- 
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

#region USUARIOS
/*            USUARIOS
    - Listar todos los usuarios
    - Listar todos los usuarios por tipo 
    - Registrar un usuario nuevo
    - Modificar un usuarios por id del usuario
    - Eliminar un usuario por id del usuario
    - Suspender un usuario por id del usuario
*/

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioApi::class . ':TraerTodos');
  $group->get('/listarPor_Tipo/{identificador}[/]', \UsuarioApi::class . ':TraerTodos_PorTipo');
  $group->post('/registrarNuevo[/]', \UsuarioApi::class . ':CargarUno');
  $group->put('/{identificador}', \UsuarioApi::class . ':ModificarUno');
  $group->delete('/cambiarEstado/{identificador},{estado}[/]', \UsuarioApi::class . ':BorrarUno');
  $group->put('/suspender/{identificador},{estado}', \UsuarioApi::class . ':Suspender_Usuario');
})->add(\UsuarioMiddleware::class . ':ValidarToken');
#endregion

#region PEDIDOS
/*            PEDIDOS
    - Listar todos los pedidos por sector de empleado
    - Registrar un pedido nuevo
    - Modificar un pedido por id del pedido
    - Baja lógica de un pedido por id del pedido (cambia a estado 4 - cancelado)
*/

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('/listarPorSector/{sector}', \PedidoMiddleware::class . ':TraerTodos_PorSector');
  $group->post('/registrarPedido[/]', \PedidoMiddleware::class . ':CargarUno');
  $group->put('/{id_pedido}', \PedidoMiddleware::class . ':ModificarUno');
  $group->delete('/{id_pedido}', \PedidoMiddleware::class . ':BorrarUno');
})->add(\UsuarioMiddleware::class . ':ValidarToken');
#endregion

#region MESAS
/*        MESAS
    - Listar todas las mesas
    - Listar mesas por estado
    - Registrar una mesa nueva
    - Modificar una mesa por id de mesa
    - Baja lógica de una mesa por id de mesa (cambia a estado 4 -mesa cerrada)
*/
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('/listarTodos[/]', \MesaApi::class . ':TraerTodos');
  $group->get('/listarPorEstado/{estado}[/]', \MesaApi::class . ':TraerTodosPor_Estado');
  $group->post('/registrar[/]', \MesaApi::class . ':CargarUno');
  $group->put('/{id_mesa}[/]', \MesaApi::class . ':ModificarUno');
  $group->delete('/{id_mesa}[/]', \MesaApi::class . ':BorrarUno');
})->add(\UsuarioMiddleware::class . ':ValidarToken');
#endregion

#region PRODUCTOS

/*        PRODUCTOS
    - Listar todos las productos
    - Listar producto por sector
    - Registrar un producto nuevo
    - Modificar un producto por id de producto
    - Baja lógica de un producto por id de producto (cambia a estado 4 -producto eliminado)
*/
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('/listarTodos[/]', \ProductoApi::class . ':TraerTodos');
  $group->get('/listarPorSector/{sector}[/]', \ProductoApi::class . ':TraerTodos_PorSector');
  $group->post('/registrarProducto[/]', \ProductoApi::class . ':CargarUno');
  $group->put('/{id_producto}[/]', \ProductoApi::class . ':ModificarUno');
  $group->delete('/{id_producto}[/]', \ProductoApi::class . ':BorrarUno');
})->add(\UsuarioMiddleware::class . ':ValidarToken');
#endregion

#region CIRCUITO DE PEDIDO
$app->group('/circuito', function (RouteCollectorProxy $group) {
  $group->post('/[/]', \ComandaApi::class . ':Leer_Pedido_CSV');
  $group->post('/imagen[/]', \ComandaApi::class . ':GetPicture');
  $group->post('/asignarEmpleados[/]', \ComandaApi::class . ':AsignarEmpleado_Comanda');
  $group->post('/listarPendientes[/]', \ComandaApi::class . ':Listar_Sector');
  $group->get('/cliente/{id_mesa},{id_pedido}[/]', \PedidoMiddleware::class . ':VerTiempoDemora');
  $group->get('/listarTodos[/]', \PedidoMiddleware::class . ':TraerTodos');
  $group->post('/listoParaServir[/]', \ComandaApi::class . ':CambiarEstado_Comanda');
  $group->post('/listarServir[/]', \ComandaApi::class . ':Listar_Sector');
  $group->post('/servir[/]', \ComandaApi::class . ':Servir');
  $group->get('/listarMesas[/]', \MesaApi::class . ':ListarMesasConEstado');
  $group->post('/cobrarMesa[/]', \ComandaApi::class . ':CobrarMesa');
  $group->put('/cerrarMesa/{id_comanda}[/]', \ComandaApi::class . ':CerrarMesa');
  $group->post('/encuesta[/]', \EncuestaApi::class . ':CargarUno');
  $group->get('/mejoresEncuestas[/]', \EncuestaApi::class . ':getMejoresComentarios');
  $group->get('/mesaMasUsada[/]', \MesaApi::class . ':TraerMesa_MasUsada');
  $group->post('/demorados[/]', \PedidoMiddleware::class . ':Traer_Demorados_NoDemorados');
  $group->get('/logo[/]', \ReportePDF::class . ':generarLogo');
})->add(\UsuarioMiddleware::class . ':ValidarToken');
#endregion

#region admin
$app->group('/admin', function (RouteCollectorProxy $group) {
  $group->post('/ingresoSistema[/]', \UsuarioApi::class . ':Traer_IngresoSistema'); //A
  $group->post('/operacionesPorSector[/]', \PedidoMiddleware::class . ':Traer_OperacionesPorSector_Todas'); //B
  $group->post('/operacionesPorEmpleado[/]', \PedidoMiddleware::class . ':Traer_OperacionesPorSector_Empleados'); //C
  $group->post('/operacionesPorMesa[/]', \PedidoMiddleware::class . ':Traer_OperacionesPorSector_Mesas'); //D
  $group->post('/masMenosVendidos[/]', \PedidoMiddleware::class . ':Traer_Mas_Menos_Pedidos'); // 8 ----- A y B
  $group->post('/demorados[/]', \PedidoMiddleware::class . ':Traer_Demorados_NoDemorados'); // 8 ------- C
  $group->post('/cancelados[/]', \PedidoMiddleware::class . ':Traer_Cancelados'); // 8 ------- D
  $group->post('/mesaMasMenosUsada[/]', \MesaApi::class . ':Traer_Mas_Menos_Usada'); // 9 ------- A y B
  $group->post('/mesaMasMenosFacturada[/]', \FacturaApi::class . ':Traer_Mas_Menos_Facturado_Mesa'); // 9 ------- C
  $group->post('/facturaMasMenos[/]', \FacturaApi::class . ':Traer_Mas_Menos_Facturado'); // 9 ------- D
})->add(\UsuarioMiddleware::class . ':ValidarSocio');;




#endregion
//generate HTML doc with a simple navigation menu and a title
$app->get('[/]', function (Request $request, Response $response) {
  $response->getBody()->write("
  <html>
  <head>
  <title>
  API REST
  </title>
  </head>
  <body>
  <h1>
  API REST
  </h1>
  <ul>
  <li><a href='https://progra3-lacomanda2021.herokuapp.com/comanda/usuarios'>Usuarios</a></li>
  <li><a href='/api/v1/usuarios/listarPorEmail/{email}'>Usuarios por email</a></li>
  <li><a href='/api/v1/usuarios/listarPorId/{id_usuario}'>Usuarios por id</a></li>
  <li><a href='/api/v1/usuarios/listarPorToken/{token}'>Usuarios por token</a></li>
  <li><a href='/api/v1/usuarios/registrar'>Registrar usuario</a></li>
  <li><a href='/api/v1/usuarios/login'>Login</a></li>
  <li><a href='/api/v1/usuarios/logout'>Logout</a></li>
  <li><a href='/api/v1/usuarios/borrar/{id_usuario}'>Borrar usuario</a></li>
  <li><a href='/api/v1/usuarios/borrarToken/{token}'>Borrar token</a></li>
  <li><a href='/api/v1/usuarios/borrarTodos'>Borrar todos los usuarios</a></li>
  <li><a href='/api/v1/usuarios/borrarTodosTokens'>Borrar todos los tokens</a></li>
  </ul>
  </body>
  </html>

 ");
  return $response;
});

$app->run();
