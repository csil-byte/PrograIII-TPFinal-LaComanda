<?php
use FastRoute\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;

class Logger
{
    public static function LogOperacion($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }

    //verifica según el tipo de metodo HTTP
    public static function VerificadorDeCredenciales(Request $request, RequestHandler $handler)
    {
        //if request is get
        if ($request->getMethod() == 'GET') {
            $response = $handler->handle($request);
            $response->getBody()->write("<h1>No se puede acceder a este recurso</h1>"); 
            return $response;
        }      
        else
        {      
            $arrayParametros = $request->getParsedBody();
            $mail = $arrayParametros['mail'];
            $tipo = $arrayParametros['tipo'];
            if($tipo=="admin")
            {
                $response = $handler->handle($request);
                $response->getBody()->write("<h1>Acceso permitido</h1>");
                return $response;
          }
            else
            {
                $response = $handler->handle($request);

                $response->getBody()->write("<h1>Acceso denegado</h1>");
                return $response;
            }
        }   
        $response = $handler->handle($request);
      $response->getBody()->write("<h1>Acceso permitido</h1>"); 
        return $response;
    }

    public function ValidarPost($request, $handler){
        $parametros = $request->getParsedBody();
        if($parametros['perfil'] == "administrador"){
            $response = $handler->handle($request);
            $response->getBody()->write($parametros['nombre']);
        }else{
            $response = new Response();
            $response->getBody()->write('No tienes habilitado el acceso.');
        }
        return $response;
    }

    public function ValidarPostJSON($request, $handler){
        $parametros = $request->getParsedBody();
        if($parametros['perfil'] == "administrador"){
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, usuario sin permisos", "status" => 403));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
        return $response;
    }
}
