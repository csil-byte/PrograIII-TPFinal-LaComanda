<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;

    if ($usr->crearUsuario() != 0) {
      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error al crear el usuario"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $usrModificar = $args['usuario'];
    $nombre = $parametros['nombre'];
    $clave = $parametros['clave'];

    $usr = Usuario::obtenerUsuario($usrModificar);

    if ($usr != null) {
      $usr->usuario = $nombre;
      $usr->clave = $clave;

      Usuario::modificarUsuario($usr);
      $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error al modificar el usuario"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    //  $parametros = $request->getParsedBody();
    $usrModificar = $args['usuario'];
    $usr = Usuario::obtenerUsuario($usrModificar);

    if ($usr != null) {
      Usuario::borrarUsuario($usr->id);
      var_dump($usr->id);
      $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Error al borrar el usuario"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function Login($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];

    $user = Usuario::obtenerUsuario($usuario);

    if ($user != null && $user->clave == $clave) {
      $payload = json_encode(array("mensaje" => "Login exitoso"));
    } else {
      $payload = json_encode(array("mensaje" => "Login fallido"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
