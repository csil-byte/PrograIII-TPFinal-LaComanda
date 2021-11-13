<?php

require_once '../app/models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoApi extends Producto implements IApiUsable
{
    public function TraerUno($request, $response, $args)
    {
    }

    public function TraerTodos($request, $response, $args)
    {
        $productos = Producto::mostrarProductos();
        $payload = json_encode(array("Lista de productos: " => $productos));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_producto = $parametros['id_producto'];
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $id_sector = $parametros['id_sector'];
        $tiempo_preparacion = $parametros['tiempo_preparacion'];

        $producto = new Producto();
        $producto->id_producto = $id_producto;
        $producto->nombre = $nombre;
        $producto->precio = $precio;
        $producto->id_sector = $id_sector;
        $producto->tiempo_preparacion = $tiempo_preparacion;

        if ($producto->crearProducto() != 0) {
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al crear el usuario"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }



    public function BorrarUno($request, $response, $args)
    {
        $productoModificar = $args['id_producto'];
        $producto = Producto::obtenerProducto_idProducto($productoModificar);

        if ($producto != null) {
            Producto::borrarProducto($producto->id_producto);
            $payload = json_encode(array("mensaPe" => "UsPario borrado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al borrar el usuario"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $productoModificar = $args['id_producto'];
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $id_sector = $parametros['id_sector'];
        $tiempo_preparacion = $parametros['tiempo_preparacion'];

        $producto = Producto::obtenerProducto_idProducto($productoModificar);

        if ($producto != null) {
            $producto->nombre = $nombre;
            $producto->precio = $precio;
            $producto->id_sector = $id_sector;
            $producto->tiempo_preparacion = $tiempo_preparacion;

            Producto::modificarProducto($producto);
            $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al modificar el usuario"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
