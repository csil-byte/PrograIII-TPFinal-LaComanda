<?php

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Slim\Handlers\Strategies\RequestHandler;

require_once './middlewares/AutentificadorJWT.php';
class UsuarioMiddleware
{



    /// Sólo puede acceder un empleado de tipo socio a esta característica.
    public static function ValidarSocio($request, $response, $next)
    {
        $payload = $request->getAttribute("payload")["Payload"];

        if ($payload->tipo == "Socio") {
            return $next($request, $response);
        } else {
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "No tienes permiso para realizar esta accion (Solo categoria socio).");
            $newResponse = $response->withJson($respuesta, 200);
            return $newResponse;
        }
    }

    /// Sólo puede acceder un empleado de tipo mozo o socio a esta característica.
    public static function ValidarMozo($request, $response, $next)
    {
        $payload = $request->getAttribute("payload")["Payload"];
        $tipoEmployee = $payload->tipo;
        if ($tipoEmployee == "Mozo" || $tipoEmployee == "Socio") {
            return $next($request, $response);
        } else {
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "No tienes permiso para realizar esta accion (Solo categoria mozo).");
            $newResponse = $response->withJson($respuesta, 200);
            return $newResponse;
        }
    }


    //Switch con tipo de empleados para validar token
    public static function Validar($request, $response, $next)
    {
        $payload = $request->getAttribute("payload")["Payload"];
        $tipoEmployee = $payload->tipo;
        switch ($tipoEmployee) {
            case "Mozo":
                return self::ValidarMozo($request, $response, $next);
                break;
            case "Socio":
                return self::ValidarSocio($request, $response, $next);
                break;
            default:
                $respuesta = array("Estado" => "ERROR", "Mensaje" => "No tienes permiso para realizar esta accion (Solo categoria mozo).");
                $newResponse = $response->withJson($respuesta, 200);
                return $newResponse;
                break;
        }
    }

    public static function ValidarToken($request, $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $esValido = false;
        try {

            AutentificadorJWT::VerificarToken($token);
            $esValido = true;
        } catch (Exception $e) {
            echo ("<br>error: " . $e->getMessage() . "</br>");
            $payload = json_encode(array(
                'error' => $e->getMessage()
            ));
        }
        if ($esValido) {
            $response = $handler->handle($request);
            echo ("");
            $payload = json_encode(array('valid' => $esValido));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
