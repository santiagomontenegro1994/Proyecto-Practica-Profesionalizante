<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'proyecto');

// Verificar la conexión
if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión a la base de datos']));
}

// Obtener la acción y el filtro desde la URL
$accion = $_GET['accion'] ?? '';
$filtro = $_GET['filtro'] ?? '';

// Ejecutar la acción correspondiente
switch ($accion) {
    case 'obtener_turnos':
        obtenerTurnos($conexion, $filtro);
        break;
    case 'obtener_ganancia':
        obtenerGanancia($conexion, $filtro);
        break;
    case 'obtener_reportes':
        obtenerReportes($conexion, $filtro);
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
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
    // Aquí puedes agregar la lógica para obtener los datos de los reportes
    echo json_encode(['total' => 0]); // Ejemplo
}

// Cerrar la conexión
$conexion->close();
?>