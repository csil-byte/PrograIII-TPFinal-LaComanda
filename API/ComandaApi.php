<?php
require_once '../app/models/Producto.php';
require_once '../app/models/Pedido.php';
require_once '../app/interfaces/IApiUsable.php';
require_once '../app/models/Comanda.php';
require_once '../app/models/Factura.php';
class ComandaApi extends Comanda
{

    /*      1        **************** LA   COMANDA - RESTAURANTE **********************

1-  Se recibe un archivo CSV por postman con los datos del pedido.
2-  Se asigna un mozo de los que están disponibles en la base de datos.
3-  Se asigna un socio de los que están disponibles en la base de datos.
4-  Se guarda en la tabla COMANDA, la información del pedido de esta mesa.
5-  Se guarda en la tabla PEDIDO, la información del pedido de esta mesa, con id de la comanda.
6-  Se cambia el estado de la mesa a CLIENTE ESPERANDO.

❏ 1- Una moza toma el pedido de una:
    ❏ Una milanesa a caballo
    ❏ Dos hamburguesas de garbanzo
    ❏ Una corona
    ❏ Un Daikiri
*/

    public function Leer_Pedido_CSV($request, $response, $args)
    {
        // ASIGNO UN MOZO AL PEDIDO
        $mozoAsignado = Usuario::Asignar_Empleado(4);
        $mozoNombre = Usuario::obtenerPor_Id($mozoAsignado);
        echo 'Nombre del mozo asignado: ' . $mozoNombre->nombre_usuario . ' --------ID: ' . $mozoAsignado . '<br>';


        // ASIGNO SOCIO AL PEDIDO
        $socioAsignado = Usuario::Asignar_Empleado(5);
        $socioNombre = Usuario::obtenerPor_Id($socioAsignado);
        echo 'Nombre del socio asignado: ' . $socioNombre->nombre_usuario . ' --------ID: ' . $socioAsignado . '<br>';


        $parameters = $request->getUploadedFiles();
        $file = $parameters['archivo'];
        try {
            if (isset($file) && $file->getError() === UPLOAD_ERR_OK) {
                $fileName = $file->getClientFilename();
                $destination = ".\Archivos\CSV\Comanda\\";
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }
                $file->moveTo($destination . $fileName);
                $file = fopen($destination . $fileName, "r");
                $i = 0;
                $headers = fgetcsv($file, 1000, ",", "'");
                while (($line = fgetcsv($file, 1000, ",", "'")) !== false) {
                    if ($i == 0) {
                        $i++;
                        // Creo una nueva comanda
                        $comanda = new ComandaApi();
                        $comanda->id_mesa = $line[0];
                        $comanda->id_mozo = $mozoAsignado;
                        $comanda->id_socio_encargado = $socioAsignado;
                        $_comanda[] = $comanda;
                        $comanda->crearComanda();

                        continue;
                    }
                    //Creo un nuevo pedido
                    $pedido = new Pedido();
                    $pedido->id_mesa = $line[0];
                    $pedido->id_producto = $line[1];
                    $pedido->id_mozo = $mozoAsignado;
                    $pedido->id_socio_encargado = $socioAsignado;
                    $pedido->nombre_cliente = $line[2];
                    $pedido->estado_pedido = $line[3];
                    $pedido->crearPedido();
                    $i++;

                    //Cambio estado de la mesa a cliente esperando
                    $mesa = Mesa::obtenerMesa_idMesa($line[0]);
                    Mesa::cambiarEstado_Mesa($mesa, 1);
                }
                $payload = json_encode(array("mensaje" => "Comanda creada - los pedidos de esta mesa han sido agregados a la base de datos"));
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            $newResponse = $response->withJson($e->getMessage(), 500);
            return $newResponse;
        } finally {
            fclose($file);
        }
    }



    /*       2               **************** LA   COMANDA - RESTAURANTE **********************
❏ 2- El mozo saca una foto de la mesa y lo relaciona con el pedido.

1-  Se recibe una imagen por postman con la foto de la mesa para asignarla al pedido/comanda
2-  Se guarda la foto en la carpeta de imagenes de la comanda.
3-  Se adjudica la foto al pedido/comanda, se guarda en tabla COMANDA, relacionada a este pedido.
*/

    public function GetPicture($request, $response, $args)
    {
        $parameters = $request->getUploadedFiles();
        $file = $parameters['imagen'];

        try {
            if (isset($file) && $file->getError() === UPLOAD_ERR_OK) {
                $fileName = $file->getClientFilename();
                $destination = ".\Archivos\CSV\Comanda\Imagenes\\";
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }
                $file->moveTo($destination . $fileName);
                $file = fopen($destination . $fileName, "r");
            }
            Comanda::adjudicarImagen($fileName);
            $payload = json_encode(array("mensaje" => "La imagen ha sido asociada a la mesa/comanda. Guardada en : " . $destination . $fileName));
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /*      3                 **************** LA   COMANDA - RESTAURANTE **********************

-3❏ Debe cambiar el estado a “en preparación” y agregarle el tiempo de preparación.


1- Recibe todos los pedidos de esta comanda/mesa y los cambia a “en preparación”. Inicialmente están como 'tomados'
2- Asigna un empleado a cada pedido según el sector, si el empleado está activo (0)

    */
    public function AsignarEmpleado_Comanda($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $comanda = $parametros['comanda'];
        try {
            $pedidos = Pedido::TraerTodos_PorComanda($comanda);
            var_dump($pedidos);
            if (count($pedidos) > 0) {

                foreach ($pedidos as $pedido) {
                    $sector = Pedido::TraerSector_PorIdProducto($pedido->id_producto);
                    $pedido->id_empleado = Usuario::Asignar_Empleado($sector);
                    Pedido::AsignarEmpleado_Pedido($pedido->id_pedido, $pedido->id_empleado);
                    Pedido::cambiarEstado_Pedido($pedido, 1);
                    $nombreEmpleado = Usuario::obtenerPor_Id($pedido->id_empleado);
                    echo '<br>' . 'Asignando empleados a pedido ID: ' . $pedido->id_pedido .   '<br>' . ' - Empleado asignado es: ' . $nombreEmpleado->nombre_usuario . '<br>';
                }

                echo '<br>'  . '<br>';
                $payload = json_encode(array("mensaje" => "Empleados asignados"));
            } else {
                $payload = json_encode(array("mensaje" => "No hay pedidos para asignar empleados"));
            }
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $newResponse = $response->withJson($e->getMessage(), 500);
            return $newResponse;
        }
    }



    /*    3/6                    **************** LA   COMANDA - RESTAURANTE **********************

❏ 3- Cada empleado responsable de cada producto del pedido , debe:
        ❏ Listar todos los productos pendientes de este tipo de empleado.
❏ 6- Cada empleado responsable de cada producto del pedido, debe:
    ❏ Listar todos los productos pendientes de este tipo de empleado

    Según el estado del pedido, se imprimirá uha lista de los pedidos correspondientes.
    De acuerdo a la comanda/mesa actual.
   */
    public function Listar_Sector($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();
            $comanda = $parametros['comanda'];
            $estado = $parametros['estado_pedido'];
            $sector = $parametros['sector'];

            $newPedidos = Producto::obtenerProductos_PorEmpleadoSector_PedidoPendiente($sector, $estado);
            if (count($newPedidos) == 0) {
                if ($estado == 1) {
                    echo '<br>' . 'No hay pedidos pendientes para este sector' . '<br>';
                    $payload = json_encode(array("mensaje" => "Sin productos pendientes"));
                } else {
                    echo '<br>' . 'No hay pedidos listos para servir para este sector' . '<br>';
                    $payload = json_encode(array("mensaje" => "Sin productos listos para servir"));
                }
            } else {
                if ($estado == 1) {
                    echo ("Productos pendientes para el sector: " . $sector . PHP_EOL);
                } else {
                    echo ("Productos listos para servir para el sector: " . $sector . PHP_EOL);
                }
                foreach ($newPedidos as $producto) {
                    echo '<tr>';
                    echo '<td>' . ' ID producto: ' . $producto->id_producto . '- </td>';
                    echo '<td>' . ' Nombre: ' . $producto->nombre . '- </td>';
                    echo '<tr>';
                }
                $payload = json_encode(array("mensaje" => "Productos listados"));
            }

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $newResponse = $response->withJson($e->getMessage(), 500);
            return $newResponse;
        }
    }

    /*     3/6                **************** LA   COMANDA - RESTAURANTE **********************

-3❏ Debe cambiar el estado a “en preparación” y agregarle el tiempo de preparación.
-6❏ Debe cambiar el estado a “listo para servir” .

Según el estado que reciba por parametro en postman, cambiará el estado del pedido y lo actualiza en la base de datos.
Siempre dentro de la comanda/mesa actual.

*/
    public function CambiarEstado_Comanda($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $comanda = $parametros['comanda'];
        $estado = $parametros['estado_pedido'];
        try {
            $pedidos = Pedido::TraerTodos_PorComanda($comanda);

            if (count($pedidos) == 0) {
                echo '<br>' . 'No hay pedidos pendientes para esta comanda' . '<br>';
                $payload = json_encode(array("mensaje" => "Sin pedidos pendientes"));
            } else {

                foreach ($pedidos as $pedido) {
                    Pedido::cambiarEstado_Pedido($pedido, $estado);
                }
                $payload = json_encode("El estado de los pedidos ha sido cambiado");
            }

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $newResponse = $response->withJson($e->getMessage(), 500);
            return $newResponse;
        }
    }

    /*     7               **************** LA   COMANDA - RESTAURANTE **********************

❏ 7- La moza se fija los pedidos que están listos para servir , cambia el estado de la mesa,

*/
    public function Servir($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $estado_pedido_servir = $parametros['estado_pedido_servir'];
        $estado_mesa = $parametros['estado_mesa'];

        try {
            $pedidosListos = Pedido::TraerTodos_PorEstado($estado_pedido_servir);
            $cant = count($pedidosListos);

            if ($cant < 1) {

                $payload = json_encode(array("mensaje" => "No hay pedidos listos para servir"));
            } else {
                $id_comanda = $pedidosListos[0]->id_comanda;

                echo ("Cantidad de pedidos listos para servir: " . $cant . "<br>");
                echo ("Pedidos listos para servir: " . "<br>");
                foreach ($pedidosListos as $pedido) {
                    $mesa = Mesa::obtenerMesa_idMesa($pedido->id_mesa);
                    Mesa::cambiarEstado_Mesa($mesa, $estado_mesa);
                    Pedido::cambiarEstado_Pedido($pedido, 3);
                    echo ($pedido->id_pedido . " - " . $pedido->id_producto . " - " . $pedido->id_mesa  . "<br>");
                }
                Pedido::Actualizar_HoraEntrega($pedidosListos, $id_comanda);
                $payload = json_encode(array("mensaje" => "El estado de la mesa se ha cambiado a cliente comiendo, el estado de los pedidos ha sigo modificado a servido"));
            }
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $newResponse = $response->withJson($e->getMessage(), 500);
            return $newResponse;
        }
    }

    /*     9               **************** LA   COMANDA - RESTAURANTE **********************

❏ 9- La moza cobra la cuenta.

Trae todos los pedidos de la comanda.
Si el listado es menos de 1, no cobra nada -> no hay pedidos.
Si el listado es mayor a 1, cobra la cuenta:
                                                    - Por cada pedido, obitnene la mesa y el estado. Si el estado es “listo para servir”, cambie el estado a cliente pagando.

*/
    public function CobrarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $comanda = $parametros['id_comanda'];

        try {

            $pedidos = Pedido::TraerTodos_PorComanda($comanda);
            $cantidadPedidos = count($pedidos);

            if ($cantidadPedidos < 1) {
                $payload = json_encode(array("mensaje" => "No hay pedidos"));
            } else {
                $cant = 0;
                foreach ($pedidos as $pedido) {
                    if ($pedido->estado_pedido == 3) {


                        $mesa = Mesa::obtenerMesa_idMesa($pedido->id_mesa);
                        if ($mesa->estado_mesa == 2) {
                            Mesa::cambiarEstado_Mesa($mesa, 3); //cliente pagando
                        }

                        $cant++;
                    }
                }
                if ($cant > 0) {

                    /* CREACION DE FACTURA */
                    $mesa = Mesa::obtenerMesa_idMesa($pedido->id_mesa);

                    $factura = new Factura();
                    $factura->id_comanda = $pedido->id_comanda;
                    $factura->id_mesa = $mesa->id_mesa;
                    $factura->importe = Factura::calcularImporte($pedido->id_comanda);
                    $factura->fecha = date("Y-m-d");
                    $factura->crearFactura();
                    echo ("Cantidad de pedidos listos para cobrar: " . $cant . "<br>");
                    $payload = json_encode(array("mensaje" => "El estado de la mesa se ha cambiado a cliente pagando y la cuenta ha sido cobrada"));
                    echo ("Cantidad de pedidos pagados: " . $cant . "<br>");
                } else {
                    $payload = json_encode(array("mensaje" => "No hay pedidos listos para cobrar"));
                }
            }
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $newResponse = $response->withJson($e->getMessage(), 500);
            return $newResponse;
        }
    }
    /*     10               **************** LA   COMANDA - RESTAURANTE **********************

❏ 10- Alguno de los socios cierra la mesa.

*/

    public function CerrarMesa($request, $response, $args)
    {

        $id_comanda = $args['id_comanda'];

        try {
            $pedidos = Pedido::TraerTodos_PorComanda($id_comanda);
            $cantidadPedidos = count($pedidos);

            if ($cantidadPedidos < 1) {
                $payload = json_encode(array("mensaje" => "No hay pedidos"));
            } else {
                $mesa = Mesa::obtenerMesa_idMesa($pedidos[0]->id_mesa);
                if ($mesa->estado_mesa == 3) {
                    Mesa::cambiarEstado_Mesa($mesa, 4); //mesa cerrada                   
                    $payload = json_encode(array("mensaje" => "La mesa fue cerrada"));
                } else {
                    $payload = json_encode(array("mensaje" => "La mesa ya está cerrada"));
                }
            }
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $newResponse = $response->withJson($e->getMessage(), 500);
            return $newResponse;
        }
    }
}
    /*     11               **************** LA   COMANDA - RESTAURANTE **********************

❏ 11- El cliente ingresa el código de mesa y el del pedido junto con los datos de la encuesta.

*/
