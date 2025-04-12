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
        $query = mysqli_query($MiConexion, "SELECT nombre, precio FROM productos WHERE idProducto = '$idProducto'");
        $result = mysqli_num_rows($query);
        $data = ($result > 0) ? mysqli_fetch_assoc($query) : 'error';
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Agregar producto al detalle temporal
    if ($_POST['action'] == 'agregarProductoDetalle') {
        $idProducto = $_POST['producto'];
        $cantidad = $_POST['cantidad'];
        $query = mysqli_query($MiConexion, "CALL add_detalle_temp($idProducto, $cantidad)");
        $result = mysqli_num_rows($query);
        $detalleTabla = '';
        $subtotal = 0;
        $total = 0;

        if ($result > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
                $precioTotal = $data['cantidad'] * $data['precio'];
                $subtotal += $precioTotal;
                $total += $precioTotal;
                $detalleTabla .= "<tr>
                                    <td>{$data['idProducto']}</td>
                                    <td>{$data['nombre']}</td>
                                    <td>{$data['cantidad']}</td>
                                    <td>{$data['precio']}</td>
                                    <td>{$precioTotal}</td>
                                  </tr>";
            }
            $totales = "<tr><td colspan='5'>Total</td><td>{$total}</td></tr>";
            echo json_encode(['detalle' => $detalleTabla, 'totales' => $totales], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'error';
        }
        exit;
    }

    // Procesar venta
    if ($_POST['action'] == 'procesarVenta') {
        $codCliente = $_POST['codCliente'];
        $query = mysqli_query($MiConexion, "CALL procesar_venta($codCliente)");
        echo ($query) ? 'ok' : 'error';
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