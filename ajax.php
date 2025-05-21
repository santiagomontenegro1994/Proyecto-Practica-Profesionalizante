<?php
session_start();
require_once 'funciones/conexion.php';
$MiConexion = ConexionBD();

// Verificar si hay datos en la solicitud
if (!empty($_POST)) {
    // Buscar cliente
    if ($_POST['action'] == 'searchCliente') {
        $dni = $_POST['cliente'];
        $query = mysqli_query($MiConexion, "SELECT idCliente, nombre, apellido, telefono FROM clientes WHERE dni = '$dni'");
        $result = mysqli_num_rows($query);
        $data = ($result > 0) ? mysqli_fetch_assoc($query) : 0;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Crear cliente
    if ($_POST['action'] == 'addCliente') {
        $dni = $_POST['dni_cliente'];
        $nombre = $_POST['nom_cliente'];
        $apellido = $_POST['ape_cliente'];
        $telefono = $_POST['tel_cliente'];
        $query_insert = mysqli_query($MiConexion, "INSERT INTO clientes (nombre, apellido, dni, telefono) VALUES ('$nombre', '$apellido', '$dni', '$telefono')");
        echo ($query_insert) ? mysqli_insert_id($MiConexion) : 'error';
        exit;
    }

    // Buscar producto
    if ($_POST['action'] == 'infoProducto') {
        $idProducto = $_POST['producto'];
        $query = mysqli_query($MiConexion, "SELECT nombre, descripcion, precio FROM productos WHERE idProducto = '$idProducto'");
        $result = mysqli_num_rows($query);
        $data = ($result > 0) ? mysqli_fetch_assoc($query) : 'error';
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Agregar producto al detalle temporal ventas
    if ($_POST['action'] == 'agregarProductoDetalle') {
        $idProducto = $_POST['producto'];
        $cantidad = $_POST['cantidad'];
        $usuario = $_SESSION['Usuario_Id'];

        $query = mysqli_query($MiConexion, "CALL add_detalle_temp($idProducto, $cantidad, $usuario)");
        $result = mysqli_num_rows($query);
        $detalleTabla = '';
        $detalleTotales = '';
        $subtotal = 0;
        $total = 0;

        if ($result > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
                $precioTotal = $data['cantidad'] * $data['precio'];
                $subtotal += $precioTotal;
                $total += $precioTotal;
                $detalleTabla .= '<tr data-bs-toggle="tooltip" data-bs-placement="left">
                                    <th>' . $data['idProducto'] . '</th>
                                    <td>' . $data['nombre'] . '</td>
                                    <td>' . $data['categoria'] . '</td>
                                    <th>' . $data['cantidad'] . '</th>
                                    <td>' . number_format($data['precio'], 2, '.', '') . '</td>
                                    <td>' . number_format($precioTotal, 2, '.', '') . '</td>
                                    <td>
                                        <a href="#" onclick="event.preventDefault();del_producto_detalle(' . $data['correlativo'] . ');">
                                            <i class="bi bi-trash-fill text-danger fs-5"></i></a>
                                    </td>   
                                </tr>';
            }
            $detalleTotales = '<tr>
                                <td colspan="5" class="text-end">SUBTOTAL</td>
                                <td colspan="5" class="text-end">' . number_format($subtotal, 2, '.', '') . '</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">DESCUENTO</td>
                                <td colspan="5" class="text-end"><input type="number" id="descuentoPedido" value="0" min="1"></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">TOTAL</td>
                                <td colspan="5" class="text-end" id="total_pedido">' . number_format($total, 2, '.', '') . '</td>
                                <td colspan="5" class="text-end" id="total_pedido_original" style="display: none;">' . $total . '</td>
                            </tr>';
            echo json_encode(['detalle' => $detalleTabla, 'totales' => $detalleTotales], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'error';
        }
        exit;
    }

    // Agregar producto al detalle temporal pedidos
    if ($_POST['action'] == 'agregarProductoDetallePedido') {
        $idProducto = $_POST['producto'];
        $cantidad = $_POST['cantidad'];
        $usuario = $_SESSION['Usuario_Id'];

        $query = mysqli_query($MiConexion, "CALL add_detalle_temp($idProducto, $cantidad, $usuario)");
        $result = mysqli_num_rows($query);
        $detalleTabla = '';
        $detalleTotales = '';
        $subtotal = 0;
        $total = 0;

        if ($result > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
                $precioTotal = $data['cantidad'] * $data['precio'];
                $subtotal += $precioTotal;
                $total += $precioTotal;
                $detalleTabla .= '<tr data-bs-toggle="tooltip" data-bs-placement="left">
                                    <th>' . $data['idProducto'] . '</th>
                                    <td>' . $data['nombre'] . '</td>
                                    <td>' . $data['categoria'] . '</td>
                                    <th>' . $data['cantidad'] . '</th>
                                    <td>' . number_format($data['precio'], 2, '.', '') . '</td>
                                    <td>' . number_format($precioTotal, 2, '.', '') . '</td>
                                    <td>
                                        <a href="#" onclick="event.preventDefault();del_producto_detalle_pedido(' . $data['correlativo'] . ');">
                                            <i class="bi bi-trash-fill text-danger fs-5"></i></a>
                                    </td>   
                                </tr>';
            }
            $detalleTotales = '<tr>
                                <td colspan="5" class="text-end">SUBTOTAL</td>
                                <td colspan="5" class="text-end">' . number_format($subtotal, 2, '.', '') . '</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">DESCUENTO</td>
                                <td colspan="5" class="text-end"><input type="number" id="descuentoPedido" value="0" min="1"></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">SEÑA</td>
                                <td colspan="5" class="text-end"><input type="text" id="seniaPedido" value="0" min="1"></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">TOTAL</td>
                                <td colspan="5" class="text-end" id="total_pedido">' . number_format($total, 2, '.', '') . '</td>
                                <td colspan="5" class="text-end" id="total_pedido_original" style="display: none;">' . $total . '</td>
                            </tr>';
            echo json_encode(['detalle' => $detalleTabla, 'totales' => $detalleTotales], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'error';
        }
        exit;
    }

    // Muestra datos del detalle temp Venta
    if ($_POST['action'] == 'searchforDetalle') {
            $usuario = $_SESSION['Usuario_Id'];
            $query = mysqli_query($MiConexion, "SELECT 
                                                    tmp.idProducto AS idProducto, 
                                                    tmp.correlativo,
                                                    p.nombre AS nombre,
                                                    p.descripcion AS categoria,
                                                    tmp.cantidad AS cantidad, 
                                                    tmp.precio_pedido AS precio
                                                FROM detalle_temp tmp
                                                JOIN productos p ON tmp.idProducto = p.idProducto
                                                WHERE tmp.idUsuario = $usuario
                                                ORDER BY tmp.correlativo;");

            $result = mysqli_num_rows($query);

            // Declaro variables que voy a usar
            $detalleTabla = '';
            $subtotal = 0;
            $total = 0;
            $arrayData = array();

            if ($result > 0) {
                // Recorro todos los detalle_temp
                while ($data = mysqli_fetch_assoc($query)) {
                    $precioTotal = round($data['cantidad'] * $data['precio'], 2);
                    $subtotal = round($subtotal + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr data-bs-toggle="tooltip" data-bs-placement="left">
                                        <th>' . $data['idProducto'] . '</th>
                                        <td>' . $data['nombre'] . '</td>
                                        <td>' . $data['categoria'] . '</td>
                                        <th>' . $data['cantidad'] . '</th>
                                        <td>' . number_format($data['precio'], 2, '.', '') . '</td>
                                        <td>' . number_format($precioTotal, 2, '.', '') . '</td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault();del_producto_detalle(' . $data['correlativo'] . ');">
                                                <i class="bi bi-trash-fill text-danger fs-5"></i></a>
                                        </td>   
                                    </tr>';
                }

                $detalleTotales = '<tr>
                                    <td colspan="5" class="text-end">SUBTOTAL</td>
                                    <td colspan="5" class="text-end">' . number_format($subtotal, 2, '.', '') . '</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">DESCUENTO</td>
                                    <td colspan="5" class="text-end"><input type="number" id="descuentoPedido" value="0" min="1"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">TOTAL</td>
                                    <td colspan="5" class="text-end" id="total_pedido">' . number_format($total, 2, '.', '') . '</td>
                                    <td colspan="5" class="text-end" id="total_pedido_original" style="display: none;">' . $total . '</td>
                                </tr>';

                echo json_encode([
                    'success' => true,
                    'detalle' => $detalleTabla,
                    'totales' => $detalleTotales,
                    'subtotal' => $subtotal,
                    'total' => $total
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No hay productos en el carrito',
                    'detalle' => '<tr><td colspan="7" class="text-center">No hay productos agregados</td></tr>',
                    'totales' => '',
                    'subtotal' => 0,
                    'total' => 0
                ], JSON_UNESCAPED_UNICODE);
            }
            mysqli_close($MiConexion);
            exit;
    }

    // Muestra datos del detalle temp Pedido 
    if ($_POST['action'] == 'searchforDetallePedido') {
            $usuario = $_SESSION['Usuario_Id'];
            $query = mysqli_query($MiConexion, "SELECT 
                                                    tmp.idProducto AS idProducto, 
                                                    tmp.correlativo,
                                                    p.nombre AS nombre,
                                                    p.descripcion AS categoria,
                                                    tmp.cantidad AS cantidad, 
                                                    tmp.precio_pedido AS precio
                                                FROM detalle_temp tmp
                                                JOIN productos p ON tmp.idProducto = p.idProducto
                                                WHERE tmp.idUsuario = $usuario
                                                ORDER BY tmp.correlativo;");

            $result = mysqli_num_rows($query);

            // Declaro variables que voy a usar
            $detalleTabla = '';
            $subtotal = 0;
            $total = 0;
            $arrayData = array();

            if ($result > 0) {
                // Recorro todos los detalle_temp
                while ($data = mysqli_fetch_assoc($query)) {
                    $precioTotal = round($data['cantidad'] * $data['precio'], 2);
                    $subtotal = round($subtotal + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr data-bs-toggle="tooltip" data-bs-placement="left">
                                        <th>' . $data['idProducto'] . '</th>
                                        <td>' . $data['nombre'] . '</td>
                                        <td>' . $data['categoria'] . '</td>
                                        <th>' . $data['cantidad'] . '</th>
                                        <td>' . number_format($data['precio'], 2, '.', '') . '</td>
                                        <td>' . number_format($precioTotal, 2, '.', '') . '</td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault();del_producto_detalle_pedido(' . $data['correlativo'] . ');">
                                                <i class="bi bi-trash-fill text-danger fs-5"></i></a>
                                        </td>   
                                    </tr>';
                }

                $detalleTotales = '<tr>
                                    <td colspan="5" class="text-end">SUBTOTAL</td>
                                    <td colspan="5" class="text-end">' . number_format($subtotal, 2, '.', '') . '</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">DESCUENTO</td>
                                    <td colspan="5" class="text-end"><input type="number" id="descuentoPedido" value="0" min="1"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">SEÑA</td>
                                    <td colspan="5" class="text-end"><input type="text" id="seniaPedido" value="0" min="1"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">TOTAL</td>
                                    <td colspan="5" class="text-end" id="total_pedido">' . number_format($total, 2, '.', '') . '</td>
                                    <td colspan="5" class="text-end" id="total_pedido_original" style="display: none;">' . $total . '</td>
                                </tr>';

                echo json_encode([
                    'success' => true,
                    'detalle' => $detalleTabla,
                    'totales' => $detalleTotales,
                    'subtotal' => $subtotal,
                    'total' => $total
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No hay productos en el carrito',
                    'detalle' => '<tr><td colspan="7" class="text-center">No hay productos agregados</td></tr>',
                    'totales' => '',
                    'subtotal' => 0,
                    'total' => 0
                ], JSON_UNESCAPED_UNICODE);
            }
            mysqli_close($MiConexion);
            exit;
    }

    // Elimina datos del detalle temp Venta
    if ($_POST['action'] == 'delProductoDetalle') {
            if (empty($_POST['id_detalle'])) {
                echo 'error'; // Si el ID del detalle está vacío, retorna error
            } else {
                $id_detalle = $_POST['id_detalle'];
                $usuario = $_SESSION['Usuario_Id'];

                // Llamar al procedimiento almacenado para eliminar un detalle de la tabla detalle_temp
                $query_detalle_temp = mysqli_query($MiConexion, "CALL del_detalle_temp($id_detalle, $usuario)");

                if (!$query_detalle_temp) {
                    echo json_encode(['error' => mysqli_error($MiConexion)]); // Mostrar error de MySQL
                    exit;
                }

                // Liberar todos los resultados del procedimiento almacenado
                while (mysqli_more_results($MiConexion) && mysqli_next_result($MiConexion)) {
                    // Este bucle asegura que todos los resultados sean procesados
                }

                $detalleTabla = '';
                $subtotal = 0;
                $total = 0;

                // Consultar los registros restantes en detalle_temp
                $query = mysqli_query($MiConexion, "SELECT 
                                                        tmp.correlativo, 
                                                        tmp.idProducto, 
                                                        p.nombre AS nombre,
                                                        p.descripcion AS categoria,
                                                        tmp.cantidad, 
                                                        tmp.precio_pedido
                                                    FROM detalle_temp tmp
                                                    LEFT JOIN productos p ON tmp.idProducto = p.idProducto
                                                    WHERE tmp.idUsuario = $usuario");

                if (!$query) {
                    echo json_encode(['error' => mysqli_error($MiConexion)]); // Mostrar error de MySQL
                    exit;
                }

                $result = mysqli_num_rows($query);

                if ($result > 0) { // Si hay registros en detalle_temp
                    while ($data = mysqli_fetch_assoc($query)) {
                        $precioTotal = round($data['cantidad'] * $data['precio_pedido'], 2); // Calcular el precio total
                        $subtotal += $precioTotal;
                        $total += $precioTotal;

                        // Generar la fila de la tabla con el botón para eliminar
                        $detalleTabla .= '<tr data-bs-toggle="tooltip" data-bs-placement="left">
                                            <th>' . $data['idProducto'] . '</th>
                                            <td>' . $data['nombre'] . '</td>
                                            <td>' . $data['categoria'] . '</td>
                                            <th>' . $data['cantidad'] . '</th>
                                            <td>' . number_format($data['precio_pedido'], 2, '.', '') . '</td>
                                            <td>' . number_format($precioTotal, 2, '.', '') . '</td>
                                            <td>
                                                <a href="#" onclick="event.preventDefault();del_producto_detalle(' . $data['correlativo'] . ');">
                                                    <i class="bi bi-trash-fill text-danger fs-5"></i>
                                                </a>
                                            </td>
                                        </tr>';
                    }

                    // Generar los totales
                    $detalleTotales = '<tr>
                                        <td colspan="5" class="text-end">SUBTOTAL</td>
                                        <td colspan="5" class="text-end">' . number_format($subtotal, 2, '.', '') . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">DESCUENTO</td>
                                        <td colspan="5" class="text-end"><input type="text" id="descuentoPedido" value="0" min="1"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">TOTAL</td>
                                        <td colspan="5" class="text-end" id="total_pedido">' . number_format($total, 2, '.', '') . '</td>
                                        <td colspan="5" class="text-end" id="total_pedido_original" style="display: none;">' . $total . '</td>
                                    </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData, JSON_UNESCAPED_UNICODE); // Retornar en formato JSON
                } else {
                    echo 'error'; // Si no hay registros en detalle_temp
                }

                mysqli_close($MiConexion);
            }
            exit;
    }

    // Elimina datos del detalle temp Pedido 
    if ($_POST['action'] == 'delProductoDetallePedido') {
            if (empty($_POST['id_detalle'])) {
                echo 'error'; // Si el ID del detalle está vacío, retorna error
            } else {
                $id_detalle = $_POST['id_detalle'];
                $usuario = $_SESSION['Usuario_Id'];

                // Llamar al procedimiento almacenado para eliminar un detalle de la tabla detalle_temp
                $query_detalle_temp = mysqli_query($MiConexion, "CALL del_detalle_temp($id_detalle, $usuario)");

                if (!$query_detalle_temp) {
                    echo json_encode(['error' => mysqli_error($MiConexion)]); // Mostrar error de MySQL
                    exit;
                }

                // Liberar todos los resultados del procedimiento almacenado
                while (mysqli_more_results($MiConexion) && mysqli_next_result($MiConexion)) {
                    // Este bucle asegura que todos los resultados sean procesados
                }

                $detalleTabla = '';
                $subtotal = 0;
                $total = 0;

                // Consultar los registros restantes en detalle_temp
                $query = mysqli_query($MiConexion, "SELECT 
                                                        tmp.correlativo, 
                                                        tmp.idProducto, 
                                                        p.nombre AS nombre,
                                                        p.descripcion AS categoria,
                                                        tmp.cantidad, 
                                                        tmp.precio_pedido
                                                    FROM detalle_temp tmp
                                                    LEFT JOIN productos p ON tmp.idProducto = p.idProducto
                                                    WHERE tmp.idUsuario = $usuario");

                if (!$query) {
                    echo json_encode(['error' => mysqli_error($MiConexion)]); // Mostrar error de MySQL
                    exit;
                }

                $result = mysqli_num_rows($query);

                if ($result > 0) { // Si hay registros en detalle_temp
                    while ($data = mysqli_fetch_assoc($query)) {
                        $precioTotal = round($data['cantidad'] * $data['precio_pedido'], 2); // Calcular el precio total
                        $subtotal += $precioTotal;
                        $total += $precioTotal;

                        // Generar la fila de la tabla con el botón para eliminar
                        $detalleTabla .= '<tr data-bs-toggle="tooltip" data-bs-placement="left">
                                            <th>' . $data['idProducto'] . '</th>
                                            <td>' . $data['nombre'] . '</td>
                                            <td>' . $data['categoria'] . '</td>
                                            <th>' . $data['cantidad'] . '</th>
                                            <td>' . number_format($data['precio_pedido'], 2, '.', '') . '</td>
                                            <td>' . number_format($precioTotal, 2, '.', '') . '</td>
                                            <td>
                                                <a href="#" onclick="event.preventDefault();del_producto_detalle_pedido(' . $data['correlativo'] . ');">
                                                    <i class="bi bi-trash-fill text-danger fs-5"></i>
                                                </a>
                                            </td>
                                        </tr>';
                    }

                    // Generar los totales
                    $detalleTotales = '<tr>
                                        <td colspan="5" class="text-end">SUBTOTAL</td>
                                        <td colspan="5" class="text-end">' . number_format($subtotal, 2, '.', '') . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">DESCUENTO</td>
                                        <td colspan="5" class="text-end"><input type="text" id="descuentoPedido" value="0" min="1"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">SEÑA</td>
                                        <td colspan="5" class="text-end"><input type="text" id="seniaPedido" value="0" min="1"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">TOTAL</td>
                                        <td colspan="5" class="text-end" id="total_pedido">' . number_format($total, 2, '.', '') . '</td>
                                        <td colspan="5" class="text-end" id="total_pedido_original" style="display: none;">' . $total . '</td>
                                    </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData, JSON_UNESCAPED_UNICODE); // Retornar en formato JSON
                } else {
                    echo 'error'; // Si no hay registros en detalle_temp
                }

                mysqli_close($MiConexion);
            }
            exit;
    }

    // Anular pedido -----------------
    if ($_POST['action'] == 'anularVenta') {
            // Eliminar todos los registros de la tabla detalle_temp
            $usuario = $_SESSION['Usuario_Id'];
            $query_del = mysqli_query($MiConexion, "DELETE FROM detalle_temp WHERE idUsuario = $usuario");

            // Cerrar la conexión a la base de datos
            mysqli_close($MiConexion);

            // Verificar si la consulta fue exitosa
            if ($query_del) {
                echo 'ok'; // Respuesta exitosa
            } else {
                echo 'error'; // Error al ejecutar la consulta
            }
            exit;
    }

    // Procesar venta
    if ($_POST['action'] == 'procesarVenta') {
            // Validar que se envíen los parámetros necesarios
            if (empty($_POST['codCliente']) || !isset($_POST['descuento'])) {
                echo 'error'; // Si faltan parámetros, retorna error
                exit;
            }

            $codCliente = $_POST['codCliente'];
            $descuento = $_POST['descuento'];
            $usuario = $_SESSION['Usuario_Id'];

            // Llamar al procedimiento almacenado registrar_venta
            $query = mysqli_query($MiConexion, "CALL registrar_venta($codCliente, $descuento, $usuario)");

            if (!$query) {
                echo json_encode(['error' => mysqli_error($MiConexion)]); // Mostrar error de MySQL
                exit;
            }

            // Procesar la respuesta del procedimiento almacenado
            $result = mysqli_num_rows($query);

            if ($result > 0) {
                $data = mysqli_fetch_assoc($query); // Obtener los datos de la venta recién creada
                echo json_encode($data, JSON_UNESCAPED_UNICODE); // Retornar los datos en formato JSON
            } else {
                echo 'error'; // Si no se generó la venta, retorna error
            }

            // Liberar los resultados del procedimiento almacenado
            while (mysqli_more_results($MiConexion) && mysqli_next_result($MiConexion)) {
                // Este bucle asegura que todos los resultados sean procesados
            }

            mysqli_close($MiConexion);
            exit;
    }

    // Procesar pedido--------
    if ($_POST['action'] == 'procesarPedido') {
            // Validar que se envíen los parámetros necesarios
            if (empty($_POST['codCliente']) || !isset($_POST['senia']) || !isset($_POST['descuento'])) {
                echo 'error'; // Si faltan parámetros, retorna error
                exit;
            }

            $codCliente = $_POST['codCliente'];
            $senia = $_POST['senia'];
            $descuento = $_POST['descuento'];
            $usuario = $_SESSION['Usuario_Id'];

            // Llamar al procedimiento almacenado registrar_venta
            $query = mysqli_query($MiConexion, "CALL registrar_pedido($codCliente, $descuento, $senia, $usuario)");

            if (!$query) {
                echo json_encode(['error' => mysqli_error($MiConexion)]); // Mostrar error de MySQL
                exit;
            }

            // Procesar la respuesta del procedimiento almacenado
            $result = mysqli_num_rows($query);

            if ($result > 0) {
                $data = mysqli_fetch_assoc($query); // Obtener los datos de la venta recién creada
                echo json_encode($data, JSON_UNESCAPED_UNICODE); // Retornar los datos en formato JSON
            } else {
                echo 'error'; // Si no se generó la venta, retorna error
            }

            // Liberar los resultados del procedimiento almacenado
            while (mysqli_more_results($MiConexion) && mysqli_next_result($MiConexion)) {
                // Este bucle asegura que todos los resultados sean procesados
            }

            mysqli_close($MiConexion);
            exit;
    }
}

// Métodos adicionales del archivo externo
if (!empty($_GET)) {
    $accion = $_GET['accion'] ?? '';
    $filtro = $_GET['filtro'] ?? '';

    switch ($accion) {
        case 'obtener_turnos':
            obtenerTurnos($MiConexion, $filtro);
            break;
        case 'obtener_ganancia':
            obtenerGanancia($MiConexion, $filtro);
            break;
        case 'obtener_reportes':
            obtenerReportes($MiConexion, $filtro);
            break;
        case 'obtener_horarios_ocupados':
            $fecha = $_GET['fecha'] ?? '';
            obtenerHorariosOcupados($MiConexion, $fecha);
            break;
        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}

// Método para obtener los turnos
function obtenerTurnos($conexion, $filtro) {
    $sql = "SELECT COUNT(*) AS total FROM turnos WHERE IdEstado = 3";
    switch ($filtro) {
        case 'hoy':
            $sql .= " AND Fecha = CURDATE()";
            break;
        case 'semana':
            $sql .= " AND YEARWEEK(Fecha) = YEARWEEK(CURDATE())";
            break;
        case 'mes':
            $sql .= " AND MONTH(Fecha) = MONTH(CURDATE()) AND YEAR(Fecha) = YEAR(CURDATE())";
            break;
        case 'año':
            $sql .= " AND YEAR(Fecha) = YEAR(CURDATE())";
            break;
        default:
            $sql .= " AND Fecha = CURDATE()";
    }

    $resultado = $conexion->query($sql);
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $total = $fila['total'];
    } else {
        $total = 0;
    }

    echo json_encode(['total' => $total]);
}

// Método para obtener la ganancia
function obtenerGanancia($conexion, $filtro) {
    $sql = "SELECT IFNULL(SUM(ts.precio), 0) AS total FROM turnos t JOIN tipo_servicio ts ON FIND_IN_SET(ts.idTipoServicio, t.idTipoServicio) > 0 WHERE t.idEstado = 3";
    switch ($filtro) {
        case 'hoy':
            $sql .= " AND t.Fecha = CURDATE()";
            break;
        case 'semana':
            $sql .= " AND YEARWEEK(t.Fecha) = YEARWEEK(CURDATE())";
            break;
        case 'mes':
            $sql .= " AND MONTH(t.Fecha) = MONTH(CURDATE()) AND YEAR(t.Fecha) = YEAR(CURDATE())";
            break;
        case 'año':
            $sql .= " AND YEAR(t.Fecha) = YEAR(CURDATE())";
            break;
        default:
            $sql .= " AND t.Fecha = CURDATE()";
    }

    $resultado = $conexion->query($sql);
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $total = $fila['total'];
    } else {
        $total = 0;
    }

    echo json_encode(['total' => $total]);
}

// Método para obtener los reportes
function obtenerReportes($conexion, $filtro) {
    // Consulta SQL según el filtro
    switch ($filtro) {
        case 'hoy':
            $sql = "
                SELECT 
                    horario,
                    SUM(CASE WHEN FIND_IN_SET('1', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS baño,
                    SUM(CASE WHEN FIND_IN_SET('2', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS corte,
                    SUM(CASE WHEN FIND_IN_SET('3', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS peinado
                FROM 
                    turnos
                WHERE 
                    Fecha = CURDATE()
                GROUP BY 
                    horario
                ORDER BY 
                    horario;
            ";
            break;
        case 'semana':
            $sql = "
                SELECT 
                    horario,
                    SUM(CASE WHEN FIND_IN_SET('1', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS baño,
                    SUM(CASE WHEN FIND_IN_SET('2', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS corte,
                    SUM(CASE WHEN FIND_IN_SET('3', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS peinado
                FROM 
                    turnos
                WHERE 
                    YEARWEEK(Fecha) = YEARWEEK(CURDATE())
                GROUP BY 
                    horario
                ORDER BY 
                    horario;
            ";
            break;
        case 'mes':
            $sql = "
                SELECT 
                    horario,
                    SUM(CASE WHEN FIND_IN_SET('1', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS baño,
                    SUM(CASE WHEN FIND_IN_SET('2', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS corte,
                    SUM(CASE WHEN FIND_IN_SET('3', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS peinado
                FROM 
                    turnos
                WHERE 
                    MONTH(Fecha) = MONTH(CURDATE()) AND YEAR(Fecha) = YEAR(CURDATE())
                GROUP BY 
                    horario
                ORDER BY 
                    horario;
            ";
            break;
        case 'año':
            $sql = "
                SELECT 
                    horario,
                    SUM(CASE WHEN FIND_IN_SET('1', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS baño,
                    SUM(CASE WHEN FIND_IN_SET('2', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS corte,
                    SUM(CASE WHEN FIND_IN_SET('3', idTipoServicio) > 0 THEN 1 ELSE 0 END) AS peinado
                FROM 
                    turnos
                WHERE 
                    YEAR(Fecha) = YEAR(CURDATE())
                GROUP BY 
                    horario
                ORDER BY 
                    horario;
            ";
            break;
        default:
            echo json_encode(['error' => 'Filtro no válido']);
            return;
    }

    // Ejecutar la consulta
    $resultado = $conexion->query($sql);

    // Procesar los datos
    $categorias = []; // Horarios
    $baño = [];      // Datos para Baño
    $corte = [];     // Datos para Corte
    $peinado = [];   // Datos para Peinado

    while ($fila = $resultado->fetch_assoc()) {
        $categorias[] = $fila['horario'];
        $baño[] = $fila['baño'];
        $corte[] = $fila['corte'];
        $peinado[] = $fila['peinado'];
    }

    // Formatear los datos para ApexCharts
    $series = [
        [
            'name' => 'Baño',
            'data' => $baño
        ],
        [
            'name' => 'Corte',
            'data' => $corte
        ],
        [
            'name' => 'Peinado',
            'data' => $peinado
        ]
    ];

    // Devolver los datos en formato JSON
    echo json_encode([
        'series' => $series,
        'categorias' => $categorias
    ]);
}

// Método para obtener los horarios ocupados
function obtenerHorariosOcupados($conexion, $fecha) {
    $query = "SELECT Horario FROM turnos WHERE Fecha = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();

    $horariosOcupados = [];
    while ($row = $result->fetch_assoc()) {
        $horariosOcupados[] = $row['Horario'];
    }

    echo json_encode($horariosOcupados);
}
?>