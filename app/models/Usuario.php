<?php

class Usuario
{
    public $id_usuario;
    public $usuario;
    public $clave;
    public $nombre_usuario;
    public $cantidad_operaciones;
    public $estado; // 0 = inactivo, 1 = activo 
    public $tipo_usuario; // 0 = administrador, 1 = usuario normal
    public $fecha_registro;
    public $fecha_ultimo_ingreso;
    public $fecha_ultima_salida;
    public $ultimo_login;

    public static function Login($user, $clave)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT U.id_tipo, U.id_usuario, U.nombre_usuario, tipo.nombre FROM usuario as U INNER JOIN tipo ON U.id_tipo = tipo.id_tipo WHERE U.usuario = :user AND U.clave = :clave");

        $consulta->execute(array(":user" => $user, ":clave" => $clave));

        $resultado = $consulta->fetch();
        return $resultado;
    }

    public static function Echo()
    {
        echo ("<br>Usuario: " . 'hola, funciona' . "<br>");
    }

    ///Actualiza la ultima fecha de logueo de los empleados.
    public static function ActualizarFechaLogin($id_usuario)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();

        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $fecha = date('Y-m-d H:i:s');

        $consulta = $objetoAccesoDato->prepararConsulta("UPDATE usuario SET fecha_ultimo_login = :fecha WHERE id_usuario = :id");

        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id_usuario, PDO::PARAM_INT);

        $consulta->execute();
    }

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_empleado, usuario, clave, id_tipo, nombre_empleado, cantidad_operaciones, estado  FROM empleado");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_empleado, usuario, clave FROM empleado WHERE usuario = :empleado");
        $consulta->bindValue(':empleado', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}
