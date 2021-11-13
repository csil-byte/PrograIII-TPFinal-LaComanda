<?php

//include Usuario class
include_once '../app/middlewares/AutentificadorJWT.php';
include_once '../app/models/Usuario.php';


class UsuarioApi extends Usuario
{
    //Constructor

    public function Login_Usuario($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros["usuario"];
        $clave = $parametros["clave"];
        $retorno = Usuario::Login($usuario, $clave);

        if ($retorno["id_tipo"] != "") {
            $token = AutentificadorJWT::crearToken($usuario, $retorno["id_tipo"], $retorno["id_usuario"], $retorno["nombre_usuario"]);
            echo '<br></br>' . 'id usuario: ' . $retorno["id_usuario"] . "\n" .  '<br></br>';
            Usuario::ActualizarFechaLogin($retorno["id_usuario"]);
            $respuesta = json_encode(array("Estado" => "OK", "Mensaje" => "Logueado exitosamente.", "Token" => $token, "Nombre_usuario" => $retorno["nombre_usuario"]));
        } else {
            $respuesta = json_encode(array(["Estado" => "ERROR", "Mensaje" => "Usuario o clave invalidos."]));
        }
        //  $newResponse = $response->withJson([$respuesta, 200]);
        $response->getBody()->write($respuesta);
        // return $response->withHeader('Content-Type', 'application/json');
        return $response;
    }

    // //Get all Usuarios
    // public function getAll()
    // {
    //     $resultSet = $this->getAllRecords();
    //     $data = array();
    //     $i = 0;
    //     while ($row = $resultSet->fetch_assoc()) {
    //         $data[$i]['id'] = $row['id'];
    //         $data[$i]['nombre'] = $row['nombre'];
    //         $data[$i]['apellido'] = $row['apellido'];
    //         $data[$i]['email'] = $row['email'];
    //         $data[$i]['password'] = $row['password'];
    //         $data[$i]['fecha_nacimiento'] = $row['fecha_nacimiento'];
    //         $data[$i]['telefono'] = $row['telefono'];
    //         $data[$i]['direccion'] = $row['direccion'];
    //         $data[$i]['foto'] = $row['foto'];
    //         $data[$i]['id_tipo'] = $row['id_tipo'];
    //         $i++;
    //     }
    //     return $data;
    // }

    //Get Usuario by id
    //     public function getById($id)
    //     {
    //         $resultSet = $this->getByIdRecord($id);
    //         $data = array();
    //         while ($row = $resultSet->fetch_assoc()) {
    //             $data['id'] = $row['id'];
    //             $data['nombre'] = $row['nombre'];
    //             $data['apellido'] = $row['apellido'];
    // }
    //     }
}
