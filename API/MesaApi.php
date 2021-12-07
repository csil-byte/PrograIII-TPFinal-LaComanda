<?php

require_once '../app/models/Mesa.php';
require_once '../app/interfaces/IApiUsable.php';

class MesaApi extends Mesa implements IApiUsable
{
    public function TraerMesa_MasUsada($request, $response, $args)
    {
        Mesa::getMesa_MasUsada();
        $payload = json_encode(array("mensaje" => "Exito"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function Traer_Mas_Menos_Usada($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $consulta = $parametros['consulta'];
        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];
        Mesa::getMesa_MasMenosUsada_Fecha($consulta, $fecha1, $fecha2);
        $payload = json_encode(array("mensaje" => "Exito"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $mesas = Mesa::mostrarMesas();
        $payload = json_encode(array("Lista de mesas: " => $mesas));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPor_Estado($request, $response, $args)
    {
        echo $args['estado'] . "<br>";
        $lista = Mesa::obtenerPor_Estado($args['estado']);
        $payload = json_encode(array("Lista de mesas: " => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ListarMesasConEstado($request, $response, $args)
    {
        $mesas = Mesa::mostrarMesas_conEstado();
        if (count($mesas)) {
            $payload = json_encode(array("Lista de mesas: " => $mesas));
        } else {
            $payload = json_encode(array("Lista de mesas: " => "No hay mesas"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    #region ABM
    public function CargarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->id_mesa = $ArrayDeParametros['id_mesa'];
        $mesa->estado_mesa = $ArrayDeParametros['estado_mesa'];

        if ($mesa->crearMesa() != 0) {
            $payload = json_encode(array("mensaje" => "Mesa creado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al crear el Mesa"));
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
            Mesa::cambiarEstado_Mesa($mesa, 6);
            $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al borrar el Mesa"));
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
        $mesa = Mesa::obtenerMesa_idMesa($mesaModificar);
        if ($mesa != null) {
            $mesa->estado_mesa = $estado_mesa;
            Mesa::modificarMesa($mesa);
            $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al modificar el Mesa"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args)
    {
        // $id = $args['id'];
        // //   $mesa = Mesa::TraerUnaMesa($id);
        // $newResponse = $response->withJson($mesa, 200);
        // return $newResponse;
    }
    #endregion
}
