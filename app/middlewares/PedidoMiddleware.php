<?php

require_once '../app/models/Pedido.php';
require_once './interfaces/IApiUsable.php';



class PedidoMiddleware extends Pedido implements IApiUsable
{
    #region TRAER POR
    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::mostrarPedidos();
        $payload = json_encode(array("Lista de pedidos: " => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos_PorUsuario($request, $response, $args)
    {
        echo $args['id_empleado'] . "<br>";
        $lista = Pedido::obtenerPedidosPorUsuario($args['id_empleado']);
        $payload = json_encode(array("Lista de pedidos: " => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos_PorSector($request, $response, $args)
    {
        echo $args['sector'] . "<br>";
        $lista = Pedido::obtenerPedidosPorSector($args['sector']);
        $payload = json_encode(array("Lista de pedidos: " => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodos_PorSector_Estado($request, $response, $args)
    {
        $lista[] = Pedido::obtenerPedidosPorSector_Comanda($args['comanda'], $args['estado']);

        $payload = json_encode(array("Lista de pedidos: " => $lista));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public function Traer_Demorados_NoDemorados($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $demora = $parametros['demora'];
        $lista = Pedido::getAllDemorados($demora);
        if (count($lista) > 0) {
            $payload = json_encode(array("mensaje" => "Pedidos"));
            Pedido::imprimirPedido($lista);
        } else {
            $payload = json_encode(array("mensaje" => "No hay pedidos demorados"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    #endregion


    public static function VerTiempoDemora($request, $response, $args)
    {
        $pedido = Pedido::obtenerPedido_idPedido($args['id_pedido']);

        if ($pedido != null) {
            $hora = Pedido::getMaxEntregaEstimada($pedido->id_comanda);
            $payload = json_encode("Hola, " . $pedido->nombre_cliente . "! Tu pedido estará listo a las: " . $hora);
        } else {
            $payload = json_encode(array("mensaje" => "El ID del pedido ingresado no corresponde a un pedido existente"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*
    7- De los empleados:
         b- Cantidad de operaciones de todos por sector.
    */
    public function Traer_OperacionesPorSector_Todas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];

        $lista = Pedido::getOperacionesPorSector($fecha1, $fecha2);

        if (count($lista) == 0) {
            $payload = json_encode(array("mensaje" => "No hay operaciones para esas fechas"));
        } else {
            foreach ($lista as $item) {
                echo ('<tr>');
                echo ('<td>' . 'Sector: ' . $item[1] . '</td>');
                echo ('<td>' . 'Cantidad operaciones: ' . $item[0] . '</td>');
            }
            $payload = json_encode("Operaciones devueltos");
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    7- De los empleados:
         c- Cantidad de operaciones de todos por sector, listada por cada empleado.
    */
    public function Traer_OperacionesPorSector_Empleados($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];

        $sectores = Pedido::getSectores();

        if (count($sectores) == 0) {
            $payload = json_encode(array("mensaje" => "No hay sectores"));
        } else {
            foreach ($sectores as $sector) {

                $lista = Pedido::getOperacionesPorSector_empleado($fecha1, $fecha2, $sector['id_sector']);
                if (count($lista) == 0) {
                    $payload = json_encode(array("mensaje" => "No hay operaciones para esas fechas"));
                } else {
                    foreach ($lista as $item) {
                        if ($item['cantidad_operaciones'] != 0) {
                            echo ('<tr>');
                            echo ('<td>' . 'Sector: ' . $item['nombre_sector'] . '</td>');
                            echo ('<td>' . 'Cantidad operaciones: ' . $item['cantidad_operaciones'] . '</td>');
                            echo ('<td>' . 'ID Usuario: ' . $item['ID'] . '</td>');
                            echo ('<td>' . 'Nombre empleado: ' . $item['nombre_empleado'] . '</td>');
                        }
                    }
                    $payload = json_encode("Operaciones devueltos");
                }
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    //0 - Mas pedidos vendidos
    //1 - Menos pedidos vendidos
    public function Traer_Mas_Menos_Pedidos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $consulta = $parametros['consulta'];
        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];
        $lista = Pedido::getMas_Menos_Pedidos($consulta, $fecha1, $fecha2);
        if (count($lista) == 0) {
            $payload = json_encode(array("mensaje" => "No hay pedidos vendidos"));
        } else {
            if ($consulta == 0) {
                //find max value from lista->cantidad
                $max = max($lista);
                //print max value
                echo ('<tr>');
                echo ('<td>' . '------------ Producto más vendido ----------' . '</td>');
                echo ('<td>' . 'Producto: ' . $max->nombre . '</td>');
                echo ('<td>' . 'Veces pedidas: ' . $max->cantidad . '</td>');
                $payload = json_encode("Producto mas vendido");
            } else {
                $min = min($lista);
                echo ('<tr>');
                echo ('<td>' . '------------ Producto menos vendido ----------' . '</td>');
                echo ('<td>' . 'Producto: ' . $min->nombre . '</td>');
                echo ('<td>' . 'Veces pedidas: ' . $min->cantidad . '</td>');
                $payload = json_encode("Producto menos vendido");
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function Traer_Cancelados($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];
        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];
        $lista = Pedido::TraerTodos_PorEstado_Fecha($fecha1, $fecha2, $estado);
        if (count($lista) == 0) {
            $payload = json_encode(array("mensaje" => "No hay pedidos cancelados"));
        } else {
            foreach ($lista as $item) {
                echo ('<tr>');
                echo ('<td>' . 'ID Pedido: ' . $item->id_pedido . '</td>');
                echo ('<td>' . 'Cliente: ' . $item->nombre_cliente . '</td>');
                echo ('<td>' . 'Fecha: ' . $item->fecha . '</td>');
                echo ('<td>' . 'Estado: ' . $item->estado_pedido . '</td>');
                echo ('<tr>');
            }
            $payload = json_encode("Pedidos cancelados");
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
    }

    #region CRUD
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado_pedido = $parametros['estado_pedido'];
        $id_mozo = $parametros['id_mozo'];
        $id_socio_encargado = $parametros['id_socio_encargado'];
        $id_producto = $parametros['id_producto'];
        $nombre_cliente = $parametros['nombre_cliente'];
        $id_empleado = $parametros['id_empleado'];
        $id_mesa = $parametros['id_mesa'];


        $pedido = new Pedido();
        $pedido->estado_pedido = $estado_pedido;
        $pedido->id_mozo = $id_mozo;
        $pedido->id_socio_encargado = $id_socio_encargado;
        $pedido->id_producto = $id_producto;
        $pedido->nombre_cliente = $nombre_cliente;
        $pedido->id_empleado = $id_empleado;
        $pedido->id_mesa = $id_mesa;

        if ($pedido->crearPedido() != null) {
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al crear el usuario"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    /*  Modificar un pedido en su totalidad
    */
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $pedidoModificar = $args['id_pedido'];
        $estado_pedido = $parametros['estado_pedido'];
        $fecha = $parametros['fecha'];
        $hora_inicial = $parametros['hora_inicial'];
        $hora_entrega_estimada = $parametros['hora_entrega_estimada'];
        $hora_entrega_real = $parametros['hora_entrega_real'];
        $id_mozo = $parametros['id_mozo'];
        $id_socio_encargado = $parametros['id_socio_encargado'];
        $id_producto = $parametros['id_producto'];
        $nombre_cliente = $parametros['nombre_cliente'];
        $id_empleado = $parametros['id_empleado'];
        $id_mesa = $parametros['id_mesa'];

        $pedido = Pedido::obtenerPedido_idPedido($pedidoModificar);

        if ($pedido != null) {

            $pedido->estado_pedido = $estado_pedido;
            $pedido->fecha = $fecha;
            $pedido->hora_inicial = $hora_inicial;
            $pedido->hora_entrega_estimada = $hora_entrega_estimada;
            $pedido->hora_entrega_real = $hora_entrega_real;
            $pedido->id_mozo = $id_mozo;
            $pedido->id_socio_encargado = $id_socio_encargado;
            $pedido->id_producto = $id_producto;
            $pedido->nombre_cliente = $nombre_cliente;
            $pedido->id_empleado = $id_empleado;
            $pedido->id_mesa = $id_mesa;

            Pedido::modificarPedido($pedido);
            $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al modificar el usuario"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /* Baja logica de un pedido, cambia el estado a 4 -> cancelado
    */
    public function BorrarUno($request, $response, $args)
    {
        //  $parametros = $request->getParsedBody();
        $pedidoModificar = $args['id_pedido'];
        $pedido = Pedido::obtenerPedido_idPedido($pedidoModificar);

        if ($pedido != null) {
            Pedido::cambiarEstado_Pedido($pedido, 4);
            $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al borrar el usuario"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    #endregion


}
