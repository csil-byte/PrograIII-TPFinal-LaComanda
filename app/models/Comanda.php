<?php
class Comanda
{
    public $id_comanda;
    public $id_mesa;
    public $id_mozo;
    public $id_socio_encargado;
    public $foto_mesa;
    public $fecha;



    public $id_producto;

    public function crearComanda()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO comanda (fecha, id_mesa, id_mozo, id_socio_encargado) VALUES (:fecha, :id_mesa, :id_mozo, :id_socio_encargado)');
            // $consulta->bindValue(':foto_mesa', $this->foto_mesa, PDO::PARAM_STR);
            $fecha = date("Y-m-d");
            $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_mozo', $this->id_mozo, PDO::PARAM_INT);
            $consulta->bindValue(':id_socio_encargado', $this->id_socio_encargado, PDO::PARAM_INT);

            $consulta->execute();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function adjudicarImagen($imagen)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE comanda SET foto_mesa = :foto_mesa WHERE id_comanda = :id_comanda');
        $consulta->bindValue(':id_comanda', self::getLastId(), PDO::PARAM_INT);
        $consulta->bindValue(':foto_mesa', $imagen, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function getLastId()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(id_comanda) FROM comanda");
        $consulta->execute();
        $fila = $consulta->fetch();
        return $fila[0];
    }

    public static function mostrarComandas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comanda");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }


    public static function mostrarComandas_Fecha($fecha1, $fecha2)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            if ($fecha2 == null) {
                $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comanda WHERE fecha = :fecha1");
                $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
            } else {
                $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comanda WHERE fecha BETWEEN :fecha1 AND :fecha2");
                $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
                $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
            }
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
