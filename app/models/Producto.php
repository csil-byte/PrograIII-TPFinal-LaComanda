<?php


class Producto
{
    public $id_producto;
    public $nombre;
    public $precio;
    public $id_sector;
    public $tiempo_preparacion;
    #region ABM
    public function crearProducto()
    {
        echo "Creando producto: " . $this->id_producto . ", " . $this->nombre . ", " . $this->precio . ", " . $this->id_sector . ", " . $this->tiempo_preparacion;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO producto (id_producto, nombre, precio, id_sector, tiempo_preparacion) VALUES(:id_producto, :nombre, :precio, :id_sector, :tiempo_preparacion)');
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':id_sector', $this->id_sector, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo_preparacion', $this->tiempo_preparacion, PDO::PARAM_INT);
        $consulta->execute();
        echo "Producto creado" . $this->id_producto;

        return $this->id_producto;
    }

    public function modificarProducto($producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE producto SET nombre = :nombre, precio = :precio, id_sector = :id_sector, tiempo_preparacion = :tiempo_preparacion WHERE id_producto = :id_producto');
        $consulta->bindValue(':id_producto', $producto->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_INT);
        $consulta->bindValue(':id_sector', $producto->id_sector, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo_preparacion', $producto->tiempo_preparacion, PDO::PARAM_INT);
        $consulta->execute();
    }

    public function mostrarProductos()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM producto');
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    public function borrarProducto($id_producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('DELETE FROM producto WHERE id_producto = :id_producto');
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->execute();
    }

    #endregion

    public static function obtenerProducto_idProducto($id_producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto WHERE id_producto = :id_producto");
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('producto');
    }

    public static function obtenerTiempoPreparacion($id_producto)
    {
        $producto =  Self::obtenerProducto_idProducto($id_producto);
        // echo 'Tiempo de preparacion en PRODUCTO - obtenerTiempoPrep:  ';
        // echo $producto->tiempo_preparacion;
        return $producto->tiempo_preparacion;
    }

    public static function obtenerPor_Sector($sector)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto WHERE id_sector = :id_sector");
            // ("SELECT SE.id_sector, SE.nombre FROM pedido PE 
            // INNER JOIN producto PR ON PE.id_producto = PR.id_producto
            // INNER JOIN sector SE ON PR.id_sector = SE.id_sector
            // WHERE SE.id_sector = :sector");
            $consulta->bindValue(':id_sector', $sector, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        //  return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    public static function obtenerProductos_PorEmpleadoSector_PedidoPendiente($id_sector, $estado_pedido)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT PE.id_producto, PR.nombre, PR.id_sector from producto PR INNER JOIN pedido PE ON PR.id_producto = PE.id_producto WHERE PE.estado_pedido = :estado_pedido AND PR.id_sector = :id_sector");
            $consulta->bindValue(':estado_pedido', $estado_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':id_sector', $id_sector, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        //  return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }
}
