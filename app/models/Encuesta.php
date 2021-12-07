<?php

class Encuesta
{

    public $id_encuesta;
    public $rating_mesa;
    public $rating_restaurante;
    public $rating_mozo;
    public $rating_cocinero;
    public $comentario;
    public $id_mesa;
    public $id_pedido;
    public $id_comanda;

    #region constructor
    //constructor
    public function crearEncuesta()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta (rating_mesa, rating_restaurante, rating_mozo, rating_cocinero, comentario, id_mesa, id_pedido, id_comanda) VALUES (:rating_mesa, :rating_restaurante, :rating_mozo, :rating_cocinero, :comentario, :id_mesa, :id_pedido, :id_comanda)");
            $consulta->bindValue(':rating_mesa', $this->rating_mesa, PDO::PARAM_INT);
            $consulta->bindValue(':rating_restaurante', $this->rating_restaurante, PDO::PARAM_INT);
            $consulta->bindValue(':rating_mozo', $this->rating_mozo, PDO::PARAM_INT);
            $consulta->bindValue(':rating_cocinero', $this->rating_cocinero, PDO::PARAM_INT);
            $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
            $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':id_comanda', $this->id_comanda, PDO::PARAM_INT);
            $consulta->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        // return $objAccesoDatos->obtenerUltimoId();
    }

    #endregion

    public function obtenerEncuestas_Mejores()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta WHERE rating_mesa > 6 AND rating_mozo > 6 AND rating_cocinero > 6 ORDER BY rating_mesa ASC");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
