<?php

class Usuario
{
    public $id_usuario;
    public $usuario;
    public $clave;
    public $id_tipo;
    public $nombre_usuario;
    public $estado;
    public $fecha_registro;
    public $fecha_ultimo_login;

    #region ABM

    public function crearUsuario()
    {
        $hora_login = date("Y-m-d H:i:s");
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuario (id_usuario, usuario, clave, id_tipo, nombre_usuario,  estado, fecha_registro, fecha_ultimo_login) VALUES (:id_usuario, :usuario, :clave, :id_tipo, :nombre_usuario, :estado, :fecha_registro, :fecha_ultimo_login)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':id_tipo', $this->id_tipo, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_usuario', $this->nombre_usuario, PDO::PARAM_STR);

        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_registro', $this->fecha_registro, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_ultimo_login', $hora_login, PDO::PARAM_STR);
        $consulta->execute();


        return true;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
    public function obtenerPor_Tipo($tipo)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario WHERE id_tipo = :id_tipo");
            $consulta->bindValue(':id_tipo', $tipo, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    public static function obtenerPor_Id($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario WHERE id_usuario = :id_usuario");
        $consulta->bindValue(':id_usuario', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($objeto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET usuario = :usuario, clave = :clave, id_tipo = :id_tipo, nombre_usuario = :nombre_usuario, estado = :estado, fecha_registro = :fecha_registro, fecha_ultimo_login = :fecha_ultimo_login WHERE id_usuario = :id_usuario");
        $consulta->bindValue(':id_usuario', $objeto->id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $objeto->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $objeto->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id_tipo', $objeto->id_tipo, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_usuario', $objeto->nombre_usuario, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $objeto->estado, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_registro', $objeto->fecha_registro, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_ultimo_login', $objeto->fecha_ultimo_login, PDO::PARAM_STR);
        $consulta->execute();
        echo "Usuario modificado";
    }


    public static function cambiarEstado_Usuario($obj, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario SET estado = :estado WHERE id_usuario = :id_usuario");
        $consulta->bindValue(':id_usuario', $obj->id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
        $consulta->execute();
    }



    #endregion

    /*
    7- De los empleados:
        a- Los días y horarios que se ingresaron al sistema.
    */
    public static function get_ingresos($fecha1, $fecha2)
    {
        if ($fecha2 == null) {
            echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT fecha_ultimo_login, usuario, nombre_usuario FROM usuario WHERE fecha_ultimo_login = :fecha_ultimo_login");
            $consulta->bindValue(':fecha_ultimo_login', $fecha1, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        } else {
            echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT fecha_ultimo_login, usuario, nombre_usuario FROM usuario WHERE fecha_ultimo_login BETWEEN :fecha_ultimo_login AND :fecha_ultimo_login2");
            $consulta->bindValue(':fecha_ultimo_login', $fecha1, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_ultimo_login2', $fecha2, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }
    }

    /*          ASIGNAR EMPLEADO
    Obtiene todos los usurios del tipo pasado por parametro y estado 0 -> activo
    Luego selecciona uno al azar y lo devuelve.
    Se actualiza la operación en la tabla cantidad_operaciones con fecha actual.
*/
    public static function Asignar_Empleado($id_tipo)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuario WHERE id_tipo = :id_tipo AND estado = 0");
        $consulta->bindValue(':id_tipo', $id_tipo, PDO::PARAM_INT);
        $consulta->execute();

        $array = $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');

        $count = count($array);
        $random = rand(0, $count - 1);
        $new = $array[$random];
        self::Actualizar_Operacion($new->id_usuario, $fecha = date("Y-m-d"), $fecha2 = null);
        return $new->id_usuario;
    }
    /*      ACTUALIZAR OPERACION
    Llama get_operaciones para obtener la cantidad de operaciones realizadas por el empleado en una fecha estipulada o entre determinadas fechas.
    Si la cantidad es igual a 0, crea un nuevo registro en cantidad_operaciones
    Si la cantidad es mayor a 0, actualiza el registro agregando una operación a la cantidad anterior.
*/
    private static function Actualizar_Operacion($id_usuario, $fecha1, $fecha2)
    {
        $cantidad_operaciones = self::Get_Operacion($id_usuario, $fecha1, $fecha2);
        if ($cantidad_operaciones == 0) {
            $cantidad_operaciones += 1;

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO cantidad_operaciones (id_usuario, cantidad, fecha) VALUES (:id_usuario, :cantidad_operaciones, :fecha)");
            $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad_operaciones', $cantidad_operaciones, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', date('Y-m-d'), PDO::PARAM_STR);

            $consulta->execute();
        } else {
            $cantidad_operaciones += 1;

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE cantidad_operaciones SET cantidad = :cantidad_operaciones WHERE id_usuario = :id_usuario AND fecha = :fecha");
            $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad_operaciones', $cantidad_operaciones, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', date('Y-m-d'), PDO::PARAM_STR);

            $consulta->execute();
        }
    }
    /*      GET OPERACION
    Obtiene la cantidad de operaciones realizadas por el empleado en una fecha estipulada o entre determinadas fechas.
*/
    public static function Get_Operacion($id_usuario, $fecha1, $fecha2)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if ($fecha2 == null) {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT cantidad FROM cantidad_operaciones WHERE id_usuario = :id_usuario AND fecha = :fecha1");
            $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT cantidad FROM cantidad_operaciones WHERE id_usuario = :id_usuario AND fecha BETWEEN :fecha1 AND :fecha2");
            $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
            $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
        }
        $consulta->execute();
        $operaciones = $consulta->fetch();
        if ($operaciones == false ||  $operaciones == null) {
            $operaciones = 0;
        } else {
            $operaciones = $operaciones['cantidad'];
        }
        return $operaciones;
    }

    #region           LOGIN

    public static function Login($user, $clave)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT U.id_tipo, U.id_usuario, U.nombre_usuario, tipo.nombre FROM usuario as U INNER JOIN tipo ON U.id_tipo = tipo.id_tipo WHERE U.usuario = :user AND U.clave = :clave");
        $consulta->execute(array(":user" => $user, ":clave" => $clave));
        $resultado = $consulta->fetch();
        return $resultado;
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

    #endregion

}
