<?php

class Mesa
{

public $id_mesa;
public $estado_mesa;
public $foto;

public function crearMesa()
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta
    ('INSERT INTO mesa (id_mesa, estado_mesa, foto) VALUES (:id_mesa, :estado_mesa, :foto)');
    $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
    $consulta->bindValue(':estado_mesa', $this->estado_mesa, PDO::PARAM_INT);
    $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
    $consulta->execute();

    return $this->id_mesa;
}

public function modificarMesa($mesa)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta
    ('UPDATE mesa SET estado_mesa = :estado_mesa, foto = :foto WHERE id_mesa = :id_mesa');
    $consulta->bindValue(':id_mesa', $mesa->id_mesa, PDO::PARAM_STR);
    $consulta->bindValue(':estado_mesa', $mesa->estado_mesa, PDO::PARAM_INT);
    $consulta->bindValue(':foto', $mesa->foto, PDO::PARAM_STR);
    $consulta->execute();
}

public static function cambiarEstado_Mesa($mesa, $estado_mesa)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta
    ('UPDATE mesa SET estado_mesa = :estado_mesa WHERE id_mesa = :id_mesa');
    $consulta->bindValue(':id_mesa', $mesa->id_mesa, PDO::PARAM_STR);
    $consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_INT);
    $consulta->execute();
}

public static function mostrarMesas()
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM mesa');
    $consulta->execute();
    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
}

public static function obtenerMesa_idMesa($id_mesa)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta
    ("SELECT * FROM mesa WHERE id_mesa = :id_mesa");
    $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
    $consulta->execute();

    return $consulta->fetchObject('Mesa');
}



}