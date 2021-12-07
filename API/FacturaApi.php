<?php
require_once '../app/models/Factura.php';
class FacturaApi extends Factura
{


    //0 - Mas pedidos vendidos
    //1 - Menos pedidos vendidos
    public function Traer_Mas_Menos_Facturado_Mesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $consulta = $parametros['consulta'];
        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];
        $factura = Factura::obtenerMaxMenos_MesaFacturada($fecha1, $fecha2);
        if ($factura != null) {
            if ($consulta == 0) {
                $max = max($factura);
                echo ('<tr>');
                echo ('<td>' . 'La mesa con más importe es: ' . $max['mesa'] . '</td>');
                echo ('<td>' . 'El importe es: ' . $max['importe'] . '</td>');
                echo ('</tr>');

                $payload = json_encode("Mesa mas vendido");
            } else {
                $min = min($factura);
                echo ('<tr>');
                echo ('<td>' . 'La mesa con menos importe es: ' . $min['mesa'] . '</td>');
                echo ('<td>' . 'El importe es: ' . $min['importe'] . '</td>');
                echo ('</tr>');
                $payload = json_encode("Mesa menos vendido");
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Traer_Mas_Menos_Facturado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $consulta = $parametros['consulta'];
        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];
        $factura = Factura::obtenerMaxMenos_Factura($consulta, $fecha1, $fecha2);
        if ($factura != null) {
            if ($consulta == 0) {

                echo ('<tr>');
                echo ('<td>' . 'La mesa con más importe es: ' . $factura[0]['mesa'] . '</td>');
                echo ('<td>' . 'El importe es: ' . $factura[0]['importe'] . '</td>');
                echo ('</tr>');

                $payload = json_encode("Mesa mas vendido");
            } else {

                echo ('<tr>');
                echo ('<td>' . 'La mesa con menos importe es: ' . $factura[0]['mesa'] . '</td>');
                echo ('<td>' . 'El importe es: ' . $factura[0]['importe'] . '</td>');
                echo ('</tr>');
                $payload = json_encode("Mesa menos vendido");
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
