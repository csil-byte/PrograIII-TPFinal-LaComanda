<?php

require_once '../app/models/Pedido.php';
require_once './interfaces/IApiUsable.php';



class PedidoMiddleware extends Pedido implements IApiUsable
{
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

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        // $pedido = $args[0];
        // echo  $pedido;
        // $usuario = Pedido::obtenerPedido($pedido, $pedido);
        // $payload = json_encode($usuario);

        // $response->getBody()->write($payload);
        // return $response
        //     ->withHeader('Content-Type', 'application/json');
    }


    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
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

        // Creamos el usuario
        $pedido = new Pedido();
        $pedido->id_pedido = $id_pedido;
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

        if ($pedido->crearPedido() != 0) {
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

    //Crear

}
