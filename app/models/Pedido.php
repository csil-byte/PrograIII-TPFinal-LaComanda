
<?php
include_once '../app/models/Producto.php';
include_once '../app/models/Comanda.php';

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
    public $id_producto;
    public $nombre_cliente;
    public $id_empleado;
    public $id_mesa;

    #region CRUD
    //constructor
    public function crearPedido()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (id_socio_encargado, id_mozo, id_pedido, estado_pedido, fecha, hora_inicial, hora_entrega_estimada, hora_entrega_real, id_producto, nombre_cliente, id_mesa, id_comanda) VALUES (:id_socio_encargado, :id_mozo, :id_pedido, :estado_pedido, :fecha, :hora_inicial, :hora_entrega_estimada, :hora_entrega_real, :id_producto, :nombre_cliente, :id_mesa, :id_comanda)");
            $consulta->bindValue(':id_pedido', self::obtenerId(), PDO::PARAM_INT);
            $consulta->bindValue(':estado_pedido', $this->estado_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', date('Y-m-d'), PDO::PARAM_STR);
            $consulta->bindValue(':hora_inicial', date('H:i:s'), PDO::PARAM_STR);
            $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
            $consulta->bindValue(':hora_entrega_estimada', self::calcularTiempoEntrega($this->id_producto, $this->hora_inicial), PDO::PARAM_STR);
            $consulta->bindValue(':hora_entrega_real', $this->hora_entrega_real, PDO::PARAM_STR);
            $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
            $consulta->bindValue(':id_comanda',  Comanda::getLastId(), PDO::PARAM_INT);
            $consulta->bindValue(':id_socio_encargado', $this->id_socio_encargado, PDO::PARAM_INT);
            $consulta->bindValue(':id_mozo', $this->id_mozo, PDO::PARAM_INT);
            $consulta->execute();

            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        // return $objAccesoDatos->obtenerUltimoId();
    }

    /*          MODIFICAR PEDIDO 
    */
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
        $consulta->bindValue(':id_producto', $pedido->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $pedido->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':id_empleado', $pedido->id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $pedido->id_mesa, PDO::PARAM_INT);

        $consulta->execute();
    }

    /*            CAMBIAR ESTADO DEL PEDIDO
    */
    public static function cambiarEstado_Pedido($pedido, $estado_pedido)
    {
        try {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado_pedido = :estado_pedido WHERE id_pedido = :id_pedido");
            $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':estado_pedido', $estado_pedido, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    #endregion


    #region TIEMPO DEMORA


    /*          OBTENER TIEMPO DE DEMORA
*       @param $id_mesa  & $id_pedidio
*       @return $tiempo_demora
*                                           Trae de la tabla pedido el tiempo de demora de un pedido
*/
    public static function Obtener_TiempoDemora($id_mesa, $id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE id_mesa = :id_mesa AND id_pedido = :id_pedido");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();
        $pedido = $consulta->fetchObject('Pedido');

        return $pedido->hora_entrega_estimada;
    }

    /*                  CALCULAR TIEMPO ENTREGA
    *   Obtiene el tiempo de preparación de un producto, y lo suma con la hora de inicio del pedido. 
    *   Devuelve la hora de entrega estimada.
    */
    public static function calcularTiempoEntrega($id_producto, $hora_inicial)
    {
        $minutosPrep = Producto::obtenerTiempoPreparacion($id_producto);
        //hora_inicial is time in format hh:mm:ss, add minutosPrep to it
        $hora_entrega_estimada = date('H:i:s', strtotime($hora_inicial . ' + ' . $minutosPrep . ' minutes'));
        // echo 'Hora entrega estimada en PEDIDO, calcularTiempoEntrega es: ';
        // echo $hora_entrega_estimada;
        //  echo 'calcularTiempoEntrega: Hora entrega estimada es: ' . $hora_entrega_estimada;
        return $hora_entrega_estimada;
    }
    /*              Calcula el tiempo de entrega maxima por cada pedido dentro de la comanda para después establecer el tiempo de entrega estimada
                como el tiempo de entrega maxima de la comanda ya que todos los pedidos de una mesa deben ser entregados en el mismo tiempo.
*/
    public static function calcularTiempoEntregaMax_porComanda($id_comanda, $calcular)
    {

        $pedidos = self::TraerTodos_PorComanda($id_comanda);
        $flag = 0;

        $hora_estimada = null;
        $hora_estimada_max = null;
        $id_pedido_max = null;
        foreach ($pedidos as $pedido) {
            if ($calcular == 0) {
                $hora_estimada = self::calcularTiempoEntrega($pedido->id_producto, $pedido->hora_inicial);
            } else {
                $hora_estimada = $pedido->hora_entrega_estimada;
            }

            if ($flag == 0) {
                $hora_estimada_max = $hora_estimada;
                $id_pedido_max = $pedido->id_pedido;
                $flag = 1;
            } else {
                if ($hora_estimada > $hora_estimada_max) {
                    $hora_estimada_max = $hora_estimada;
                    $id_pedido_max = $pedido->id_pedido;
                }
            }
        }
        //   echo 'calcularTiempoEntregaMax_porComanda = id_pedido_max es: ' . $id_pedido_max;
        return $id_pedido_max;
    }
    //choose randomly between true or false values
    private static function EstablecerDemora($id_pedido)
    {
        $pedido = self::obtenerPedido_idPedido($id_pedido);
        if ($pedido != null) {
            $demora = rand(0, 1);
            if ($demora == 1) {
                $tiempo_de_entrega = $pedido->hora_entrega_estimada;
                $tiempo_de_entrega = strtotime($tiempo_de_entrega);
                $tiempo_de_entrega = $tiempo_de_entrega + rand(300, 900);
                $tiempo_de_entrega = date('H:i:s', $tiempo_de_entrega);
                echo "Demorado: " . $tiempo_de_entrega . 'Tiempo de entrega estimado era: ' . $pedido->hora_entrega_estimada;
            } else {
                $tiempo_de_entrega = $pedido->hora_entrega_estimada;
                echo 'No fue demorado' . $tiempo_de_entrega;
            }
        }
        return $tiempo_de_entrega;
    }
    public static function Actualizar_HoraEntrega($pedidosListos, $id_comanda)
    {
        try {
            $id_pedido_max = self::calcularTiempoEntregaMax_porComanda($id_comanda, 0);
            $hora_entrega_real = self::EstablecerDemora($id_pedido_max);
            $cantidad = count($pedidosListos);
            if ($cantidad > 0) {
                foreach ($pedidosListos as $pedido) {


                    $objAccesoDato = AccesoDatos::obtenerInstancia();
                    $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET hora_entrega_real = :hora_entrega_real WHERE id_pedido = :id_pedido");
                    $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_INT);
                    $consulta->bindValue(':hora_entrega_real', $hora_entrega_real, PDO::PARAM_INT);
                    $consulta->execute();
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }



    public static function getAllDemorados($demorados)
    {
        $comandas = Comanda::mostrarComandas();
        $listaPedidosDemorados = array();
        $listaPedidosNoDemorados = array();
        foreach ($comandas as $comanda) {
            $pedidos = self::TraerTodos_PorComanda($comanda->id_comanda);
            $max_entrega_estimada = self::getMaxEntregaEstimada($comanda->id_comanda);
            foreach ($pedidos as $pedido) {
                if ($pedido->hora_entrega_real != null) {
                    if ($max_entrega_estimada != $pedido->hora_entrega_real) {
                        //  echo 'Demorado: ' . $pedido->hora_entrega_real . ' Maxima: ' . $max_entrega_estimada;
                        array_push($listaPedidosDemorados, $pedido);
                    } else {
                        //   echo 'No demorado: ' . $pedido->hora_entrega_real . ' Maxima: ' . $max_entrega_estimada;
                        array_push($listaPedidosNoDemorados, $pedido);
                    }
                }
            }
        }
        if ($demorados == 0) {
            echo 'Demorados: ';
            return $listaPedidosDemorados;
        } else {
            echo 'No demorados';

            return $listaPedidosNoDemorados;
        }
    }

    public static function getMaxEntregaEstimada($id_comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT MAX(hora_entrega_estimada) as hora_entrega_estimada FROM pedido WHERE id_comanda = :id_comanda");
        $consulta->bindValue(':id_comanda', $id_comanda, PDO::PARAM_STR);
        $consulta->execute();
        $sector = $consulta->fetch();
        $sector = $sector[0];
        return $sector;
    }

    #endregion

    //function to print every element of pedido nicely formatted
    public static function imprimirPedido($listaPedidos)
    {
        foreach ($listaPedidos as $pedido) {
            echo '<br>';
            echo '<tr>';
            echo '<td>' . ' ID pedido: ' . $pedido->id_pedido . '- </td>';
            echo '<td>' . ' ID comanda: ' . $pedido->id_comanda . '- </td>';
            echo '<td>' . ' ID producto: ' . $pedido->id_producto . '- </td>';
            echo '<td>' . ' ID mesa: ' . $pedido->id_mesa . '- </td>';
            echo '<td>' . ' Nombre cliente: ' . $pedido->nombre_cliente . '- </td>';
            echo '<td>' . ' Hora estimada: ' . self::getMaxEntregaEstimada($pedido->id_comanda) . '- </td>';
            echo '<td>' . ' Hora real: ' . $pedido->hora_entrega_real . '- </td>';

            echo '</tr>';
        }
    }




    #region ID

    /*                 GET LAST ID - PEDIDO
    *   Obtiene el ultimo id de la tabla pedido y lo devuelve.
    */
    public static function getLastId()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(id_pedido) FROM pedido");
        $consulta->execute();
        $fila = $consulta->fetch();
        return $fila[0];
    }



    /*                  OBTENER ID
    *   Obtiene el ultimo id, genera uno nuevo con el formato PA-XXX +1, y lo devuelve.
    */
    public static function obtenerId()
    {
        $ultId = Pedido::getLastId();
        $ultId = substr($ultId, 2);
        $ultId = (int)$ultId;
        $ultId++;
        $ultId = "PA" . str_pad($ultId, 3, "0", STR_PAD_LEFT);
        return $ultId;
    }

    #endregion
    /*            ASIGNAR EMPLEADO AL PEDIDO
    *   Se le asigna un empleado a un pedido.     -----> pide ID_PEDIDO y ID_EMPLEADO 
    */
    public static function AsignarEmpleado_Pedido($id_pedido, $id_empleado)
    {
        try {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET id_empleado = :id_empleado WHERE id_pedido = :id_pedido");
            $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }


    #region TRAER ***UN*** PEDIDO POR ______________

    /*          OBTENER PEDIDO ---------------- POR ID
*/
    public static function obtenerPedido_idPedido($id_pedido)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE id_pedido = :id_pedido");
            $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
            $consulta->execute();

            return $consulta->fetchObject('Pedido');
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    public static function TraerSector_PorIdProducto($id_producto)
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT id_sector FROM producto WHERE id_producto = :id_producto");
            $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $sector = $consulta->fetch();
        //get value from $cantidad
        $sector = $sector[0];

        return $sector;
    }
    #endregion
    #region   TRAER TODOS POR _________
    public static function TraerTodos_PorComanda($comanda)
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM pedido WHERE id_comanda = :id_comanda");
            $consulta->bindValue(':id_comanda', $comanda, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }


    public static function TraerTodos_PorEstado($estado_pedido)
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM pedido WHERE estado_pedido = :estado_pedido");
            $consulta->bindValue(':estado_pedido', $estado_pedido, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function TraerTodos_PorEstado_Fecha($fecha1, $fecha2, $estado_pedido)
    {

        if ($fecha2 == null) {
            echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
            try {
                $objetoAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM pedido WHERE estado_pedido = :estado_pedido AND fecha = :fecha1");
                $consulta->bindValue(':estado_pedido', $estado_pedido, PDO::PARAM_INT);
                $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
                $consulta->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        } else {
            echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;
            try {
                $objetoAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM pedido WHERE fecha BETWEEN = :fecha1 AND :fecha2 AND estado_pedido = :estado_pedido");
                $consulta->bindValue(':estado_pedido', $estado_pedido, PDO::PARAM_INT);
                $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
                $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
                $consulta->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        }
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
    public static function obtenerPedidosPorEstado_Sector($estado, $id_producto)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE estado_pedido = :estado AND id_producto = :id_producto ORDER BY id_producto");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
            $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);

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
    public static function obtenerPedidosPorSector_Comanda($comanda, $estado)
    {
        /*
devolver las filas de la comanda con un count según parametro $comanda
de ahí obtengo los productos que son iguales por ende mismo sector
de ahí itero según la cantidad de productos
devuelvo un array que hopefully se agrega a otro array

*/
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM pedido WHERE id_comanda = :id_comanda");
        $consulta->bindValue(':id_comanda', $comanda, PDO::PARAM_INT);
        $consulta->execute();
        //$cantidad should be equal to what the query returns
        $cantidad = $consulta->fetch();
        //get value from $cantidad
        $cantidad = $cantidad[0];
        var_dump($cantidad);
    }
    #endregion

    #region METRICAS
    /*
    7- De los empleados:
         b- Cantidad de operaciones de todos por sector.
    */
    public static function getOperacionesPorSector($fecha1, $fecha2)
    {
        if ($fecha2 == null) {
            echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT COUNT(PE.id_producto) as cantidad_operaciones, SE.nombre as nombre_sector FROM pedido PE INNER JOIN producto PR ON PE.id_producto = PR.id_producto
            INNER JOIN sector SE ON SE.id_sector = PR.id_sector WHERE PE.fecha = :fecha GROUP BY PR.id_sector"
            );
            $consulta->bindValue(':fecha', $fecha1, PDO::PARAM_STR);

            $consulta->execute();
            return $consulta->fetchAll();
        } else {
            echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT COUNT(PE.id_producto) as cantidad_operaciones, SE.nombre as nombre_sector FROM pedido PE INNER JOIN producto PR ON PE.id_producto = PR.id_producto
            INNER JOIN sector SE ON SE.id_sector = PR.id_sector WHERE PE.fecha BETWEEN :fecha1 AND :fecha2 GROUP BY PR.id_sector"
            );
            $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
            $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll();
        }
    }

    public function getSectores()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_sector as id_sector FROM sector");
        $consulta->execute();
        return $consulta->fetchAll();
    }
    /*
    7- De los empleados:
        c. Cantidad de operaciones de todos por sector, listada por cada empleado.
    */
    public static function getOperacionesPorSector_empleado($fecha1, $fecha2, $sector)
    {
        if ($fecha2 == null) {
            echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT COUNT(PE.id_producto) as cantidad_operaciones, SE.nombre as nombre_sector, US.nombre_usuario as nombre_empleado, US.id_usuario as ID
                FROM pedido PE INNER JOIN producto PR ON PE.id_producto = PR.id_producto
                INNER JOIN usuario as US ON US.id_usuario = PE.id_empleado
                INNER JOIN sector SE ON SE.id_sector = PR.id_sector
                 WHERE PE.fecha = :fecha1 AND PR.id_sector = :sector"
            );
            $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);

            $consulta->execute();
            return $consulta->fetchAll();
        } else {
            echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT COUNT(PE.id_producto) as cantidad_operaciones, SE.nombre as nombre_sector, US.nombre_usuario as nombre_empleado, US.id_usuario as ID
                FROM pedido PE INNER JOIN producto PR ON PE.id_producto = PR.id_producto
                INNER JOIN usuario as US ON US.id_usuario = PE.id_empleado
                INNER JOIN sector SE ON SE.id_sector = PR.id_sector
            WHERE PE.fecha BETWEEN :fecha1 AND :fecha2  AND PR.id_sector = :sector"
            );
            $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
            $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll();
        }
    }

    public static function getMas_Menos_Pedidos($solicitud, $fecha1, $fecha2)
    {
        if ($fecha2 == null) {
            echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT COUNT(PE.id_producto) as veces_pedidas, PR.nombre as nombre_producto FROM pedido PE
                INNER JOIN producto PR ON PE.id_producto = PR.id_producto
                WHERE PE.fecha = :fecha1 GROUP BY PR.id_producto"
            );
            $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
            $consulta->execute();
            $resultado = $consulta->fetchAll();
            if ($resultado) {
                $rows = array();
                foreach ($resultado as $row) {
                    $rowObj = new stdclass();
                    $rowObj->cantidad = $row['veces_pedidas'];
                    $rowObj->nombre = $row['nombre_producto'];
                    array_push($rows, $rowObj);
                }
                //  var_dump($rows);
                return $rows;
            }
        } else {
            echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT COUNT(PE.id_producto) as veces_pedidas, PR.nombre FROM pedido PE
                INNER JOIN producto PR ON PE.id_producto = PR.id_producto
                WHERE
            PE.fecha BETWEEN :fecha1 AND :fecha2  GROUP BY PR.id_producto"
            );
            $consulta->bindValue(':fecha1', $fecha1, PDO::PARAM_STR);
            $consulta->bindValue(':fecha2', $fecha2, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll();
        }
    }
    #endregion
}
