<?php
require_once '../app/models/Encuesta.php';
require_once '../app/models/Pedido.php';
class EncuestaApi extends Encuesta implements IApiUsable
{


    public function getMejoresComentarios($request, $response, $args)
    {
        $encuestas = Encuesta::obtenerEncuestas_Mejores();
        $cantidadEncuestas = count($encuestas);
        if ($cantidadEncuestas > 0) {
            echo 'Las mejores encuestas son: ' . PHP_EOL;
            foreach ($encuestas as $encuesta) {
                echo 'El id es: ' . $encuesta->id_encuesta . ' Rating de mesa: ' . $encuesta->rating_mesa . ' Rating restaurante: ' . $encuesta->rating_restaurante . ' Rating de mozo: ' . $encuesta->rating_mozo . ' Rating de cocinero: ' . $encuesta->rating_cocinero . ' Comentario: ' . $encuesta->comentario . PHP_EOL      ;

            }
            $payload = json_encode(array("mensaje" => "Se obtuvieron las mejores encuestas"));
        } else {
            $payload = json_encode(array("mensaje" => "No hay encuestas"));
        };
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }




    #region ABM
    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
    }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $rating_mesa = $parametros['rating_mesa'];
        $rating_restaurante = $parametros['rating_restaurante'];
        $rating_mozo = $parametros['rating_mozo'];
        $rating_cocinero = $parametros['rating_cocinero'];
        $comentario = $parametros['comentario'];
        $id_mesa = $parametros['id_mesa'];
        $id_pedido = $parametros['id_pedido'];

        $encuesta = new Encuesta();
        $encuesta->rating_mesa = $rating_mesa;
        $encuesta->rating_restaurante = $rating_restaurante;
        $encuesta->rating_mozo = $rating_mozo;
        $encuesta->rating_cocinero = $rating_cocinero;
        $encuesta->comentario = $comentario;
        $encuesta->id_mesa = $id_mesa;
        $encuesta->id_pedido = $id_pedido;

        $pedido = Pedido::obtenerPedido_idPedido($id_pedido);
        $encuesta->id_comanda = $pedido->id_comanda;
        if ($encuesta->crearEncuesta()) {
            $payload = json_encode(array("mensaje" => "Encuesta creado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al crear encuesta"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
    }

    public function ModificarUno($request, $response, $args)
    {
    }
    #endregion
}
