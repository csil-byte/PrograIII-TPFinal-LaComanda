
<?php


class Pedido
{

    public $id_pedido;
    public $estado_pedido;
    public $fecha;
    public $hora_inicial;
    public $hora_entrega_estimada;
    public $hora_entrega_real;
    public $id_mozo;
    public $id_socio_encargado;
    public $producto;
    public $nombre_cliente;
    public $id_empleado;
    public $id_mesa;

    #region constructor
    //constructor
    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido VALUES (:id_pedido, :estado_pedido, :fecha, :hora_inicial, :hora_entrega_estimada, :hora_entrega_real, :id_mozo, :id_socio_encargado, :id_producto, :nombre_cliente, :id_empleado, :id_mesa)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado_pedido', $this->estado_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':hora_inicial', $this->hora_inicial, PDO::PARAM_STR);
        $consulta->bindValue(':hora_entrega_estimada', $this->hora_entrega_estimada, PDO::PARAM_STR);
        $consulta->bindValue(':hora_entrega_real', $this->hora_entrega_real, PDO::PARAM_STR);
        $consulta->bindValue(':id_mozo', $this->id_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':id_socio_encargado', $this->id_socio_encargado, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $this->producto, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':id_empleado', $this->id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->execute();

        return $this->id_pedido;
        // return $objAccesoDatos->obtenerUltimoId();
    }

    #endregion
    #region getters
    //getters
    public function getId_pedido()
    {
        return $this->id_pedido;
    }

    public function getEstado_pedido()
    {
        return $this->estado_pedido;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getHora_inicial()
    {
        return $this->hora_inicial;
    }

    public function getHora_entrega_estimada()
    {
        return $this->hora_entrega_estimada;
    }

    public function getHora_entrega_real()
    {
        return $this->hora_entrega_real;
    }

    public function getId_mozo()
    {
        return $this->id_mozo;
    }

    public function getId_socio_encargado()
    {
        return $this->id_socio_encargado;
    }

    public function getProducto()
    {
        return $this->producto;
    }

    public function getNombre_cliente()
    {
        return $this->nombre_cliente;
    }

    public function getId_empleado()
    {
        return $this->id_empleado;
    }

    public function getId_mesa()
    {
        return $this->id_mesa;
    }

    #endregion
    #region setters
    //setters
    public function setId_pedido($id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function setEstado_pedido($estado_pedido)
    {
        $this->estado_pedido = $estado_pedido;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function setHora_inicial($hora_inicial)
    {
        $this->hora_inicial = $hora_inicial;
    }

    public function setHora_entrega_estimada($hora_entrega_estimada)
    {
        $this->hora_entrega_estimada = $hora_entrega_estimada;
    }

    public function setHora_entrega_real($hora_entrega_real)
    {
        $this->hora_entrega_real = $hora_entrega_real;
    }

    public function setId_mozo($id_mozo)
    {
        $this->id_mozo = $id_mozo;
    }

    public function setId_socio_encargado($id_socio_encargado)
    {
        $this->id_socio_encargado = $id_socio_encargado;
    }

    public function setProducto($producto)
    {
        $this->producto = $producto;
    }

    public function setNombre_cliente($nombre_cliente)
    {
        $this->nombre_cliente = $nombre_cliente;
    }

    public function setId_empleado($id_empleado)
    {
        $this->id_empleado = $id_empleado;
    }

    public function setId_mesa($id_mesa)
    {
        $this->id_mesa = $id_mesa;
    }

    #endregion

    //Modificar pedido
    public static function modificarPedido($pedido)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE
         pedido SET estado_pedido = :estado_pedido, fecha = :fecha, hora_inicial = :hora_inicial, 
         hora_entrega_estimada = :hora_entrega_estimada, hora_entrega_real = :hora_entrega_real, id_mozo = :id_mozo,
          id_socio_encargado = :id_socio_encargado, id_producto = :id_producto, nombre_cliente = :nombre_cliente, id_empleado = :id_empleado,
           id_mesa = :id_mesa WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado_pedido', $pedido->estado_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $pedido->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':hora_inicial', $pedido->hora_inicial, PDO::PARAM_STR);
        $consulta->bindValue(':hora_entrega_estimada', $pedido->hora_entrega_estimada, PDO::PARAM_STR);
        $consulta->bindValue(':hora_entrega_real', $pedido->hora_entrega_real, PDO::PARAM_STR);
        $consulta->bindValue(':id_mozo', $pedido->id_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':id_socio_encargado', $pedido->id_socio_encargado, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $pedido->producto, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $pedido->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':id_empleado', $pedido->id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $pedido->id_mesa, PDO::PARAM_INT);

        $consulta->execute();
    }

    public static function cambiarEstado_Pedido($pedido, $estado_pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado_pedido = :estado_pedido WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado_pedido', $estado_pedido, PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function obtenerPedido_idPedido($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }






    // Mostrar todos los pedidos de la base de datos
    public static function mostrarPedidos()
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM pedido");
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function obtenerPedidosPorUsuario($usuario)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE id_empleado = :usuario");
            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        //  return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function obtenerPedidosPorSector($sector)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT SE.id_sector, SE.nombre FROM pedido PE 
            INNER JOIN producto PR ON PE.id_producto = PR.id_producto
            INNER JOIN sector SE ON PR.id_sector = SE.id_sector
            WHERE SE.id_sector = :sector");
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        //  return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }
}
