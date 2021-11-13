<?php
/*
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `precio` int(11) NOT NULL,
  `id_sector` int(11) NOT NULL,
  `tiempo_preparacion` int(11) DEFAULT '1',
*/

class Producto
{
    public $id_producto;
    public $nombre;
    public $precio;
    public $id_sector;
    public $tiempo_preparacion;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO producto (id_producto, nombre, precio, id_sector, tiempo_preparacion) VALUES(:id_prodcuto, :nombre, :precio, :id_sector, :tiempo_preparacion)');
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':id_sector', $this->id_sector, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo_preparacion', $this->tiempo_preparacion, PDO::PARAM_INT);
        $consulta->execute();

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


    public static function obtenerProducto_idProducto($id_producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto WHERE id_producto = :id_producto");
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('producto');
    }
}
