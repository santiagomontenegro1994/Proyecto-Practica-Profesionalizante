<?php

// Conectar a la base de datos
$conexion = new mysqli($Host = 'localhost', $User = 'root', $Password = '', $BaseDeDatos = 'proyecto');

// Verificar la conexión
if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión a la base de datos']));
}

// Obtener la acción y el filtro desde la URL
$accion = $_GET['accion'] ?? '';
$filtro = $_GET['filtro'] ?? '';

// Ejecutar la acción correspondiente
if ($accion === 'obtener_ganancia') {
    // Consulta SQL base
    $sql = "
        SELECT 
            IFNULL(SUM(ts.precio), 0) AS total
        FROM 
            turnos t
        JOIN 
            tipo_servicio ts
        ON 
            FIND_IN_SET(ts.idTipoServicio, t.idTipoServicio) > 0
        WHERE 
            t.idEstado = 3
    ";

    // Agregar la condición del filtro
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
            $sql .= " AND t.Fecha = CURDATE()"; // Por defecto, hoy
    }

    // Ejecutar la consulta
    $resultado = $conexion->query($sql);

    // Obtener el resultado
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $total = $fila['total'];
    } else {
        $total = 0;
    }

    // Devolver el resultado en formato JSON
    echo json_encode(['total' => $total]);
} else {
    // Si la acción no es válida, devolver un error
    echo json_encode(['error' => 'Acción no válida']);
}

// Cerrar la conexión
$conexion->close();
?>