<?php

require_once '../app/models/Mesa.php';
require_once '../app/interfaces/IApiUsable.php';

class MesaApi extends Mesa implements IApiUsable
{
    public function TraerUno($request, $response, $args)
    {
        // $id = $args['id'];
        // //   $mesa = Mesa::TraerUnaMesa($id);
        // $newResponse = $response->withJson($mesa, 200);
        // return $newResponse;
    }

    public function TraerTodos($request, $response, $args)
    {
        $mesas = Mesa::mostrarMesas();
        $payload = json_encode(array("Lista de mesas: " => $mesas));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->id_mesa = $ArrayDeParametros['id_mesa'];
        $mesa->estado_mesa = $ArrayDeParametros['estado_mesa'];
        $mesa->foto = $ArrayDeParametros['foto'];

        if ($mesa->crearMesa() != 0) {
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al crear el usuario"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }



    public function BorrarUno($request, $response, $args)
    {
        $mesaModificar = $args['id_mesa'];
        $mesa = Mesa::obtenerMesa_idMesa($mesaModificar);

        if ($mesa != null) {
            Mesa::cambiarEstado_Mesa($mesa, 4);
            $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al borrar el usuario"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesaModificar = $args['id_mesa'];
        $estado_mesa = $parametros['estado_mesa'];
        $foto = $parametros['foto'];
        $mesa = Mesa::obtenerMesa_idMesa($mesaModificar);
        if ($mesa != null) {
            $mesa->estado_mesa = $estado_mesa;
            $mesa->foto = $foto;
            Mesa::modificarMesa($mesa);
            $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al modificar el usuario"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
