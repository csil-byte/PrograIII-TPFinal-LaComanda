<?php
include_once '../app/models/Producto.php';
include_once '../app/models/Comanda.php';
include_once '../app/models/Pedido.php';
require_once '../app/models/Mesa.php';
/*
CREATE TABLE `factura` (
  `id_factura` int(11) DEFAULT NULL AUTO_INCREMENT,
  `id_mesa` int(11) NOT NULL,
  `id_comanda` int(11) NOT NULL,
  `importe` int(100) NOT NULL,
  PRIMARY KEY (`id_factura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/
class Factura
{
    public $id_factura;
    public $id_mesa;
    public $id_comanda;
    public $importe;
    public $fecha;

    public function crearFactura()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO factura (id_mesa, id_comanda, importe, fecha) VALUES (:id_mesa, :id_comanda, :importe, :fecha)");
            $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_comanda', $this->id_comanda, PDO::PARAM_INT);
            $consulta->bindValue(':importe', $this->importe, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
            $consulta->execute();
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function calcularImporte($id_comanda)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(PR.precio) FROM pedido PE
            INNER JOIN producto PR ON PE.id_producto = PR.id_producto
             WHERE PE.id_comanda = :id_comanda GROUP BY PE.id_comanda");
            $consulta->bindValue(':id_comanda', $id_comanda, PDO::PARAM_INT);
            $consulta->execute();
            $importe = $consulta->fetchColumn();
            return $importe;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function obtenerMaxMenos_MesaFacturada($fecha1, $fecha2)
    {
        try {   //add importe from factura with the same id_mesa where fecha between '2019-01-01' and '2019-01-31'
            if ($fecha2 == null) {
                echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta(
                    "SELECT SUM(importe) as importe, id_mesa as mesa FROM factura WHERE fecha = :fecha1 GROUP BY id_mesa"
                );
                $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);

                $consulta->execute();
                return $consulta->fetchAll();
            } else {
                echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;

                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta(
                    "SELECT SUM(importe) as importe, id_mesa as mesa FROM factura WHERE fecha BETWEEN :fecha1 AND :fecha2 GROUP BY id_mesa"
                );
                $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
                $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
                $consulta->execute();
                return $consulta->fetchAll();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    //1 menos //0 mas
    public static function obtenerMaxMenos_Factura($consulta, $fecha1, $fecha2)
    {
        try {   //add importe from factura with the same id_mesa where fecha between '2019-01-01' and '2019-01-31'
            if ($consulta == 1) {
                if ($fecha2 == null) {
                    echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
                    $objAccesoDatos = AccesoDatos::obtenerInstancia();
                    $consulta = $objAccesoDatos->prepararConsulta(
                        "SELECT MIN(importe) as importe, id_mesa as mesa FROM factura WHERE fecha = :fecha1 limit 1"
                    );
                    $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);

                    $consulta->execute();
                    return $consulta->fetchAll();
                } else {
                    echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;

                    $objAccesoDatos = AccesoDatos::obtenerInstancia();
                    $consulta = $objAccesoDatos->prepararConsulta(
                        "SELECT MIN(importe) as importe, id_mesa as mesa FROM factura WHERE fecha BETWEEN :fecha1 AND :fecha2 limit 1"
                    );
                    $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
                    $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
                    $consulta->execute();
                    return $consulta->fetchAll();
                }
            } else {
                if ($fecha2 == null) {
                    echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
                    $objAccesoDatos = AccesoDatos::obtenerInstancia();
                    $consulta = $objAccesoDatos->prepararConsulta(
                        "SELECT MAX(importe) as importe, id_mesa as mesa FROM factura WHERE fecha = :fecha1 limit 1"
                    );
                    $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);

                    $consulta->execute();
                    return $consulta->fetchAll();
                } else {
                    echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;

                    $objAccesoDatos = AccesoDatos::obtenerInstancia();
                    $consulta = $objAccesoDatos->prepararConsulta(
                        "SELECT MAX(importe) as importe, id_mesa as mesa FROM factura WHERE fecha BETWEEN :fecha1 AND :fecha2"
                    );
                    $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
                    $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
                    $consulta->execute();
                    return $consulta->fetchAll();
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
