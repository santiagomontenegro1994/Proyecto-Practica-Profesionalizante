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


// Cerrar la conexión
$conexion->close();
?>