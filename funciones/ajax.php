<?php
// Conectar a la base de datos
$conexion = new mysqli("localhost", "usuario", "contraseña", "basededatos");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener la acción desde la URL
$accion = $_GET['accion'];

// Ejecutar la acción correspondiente
switch ($accion) {
    case 'obtener_ganancia':
        obtenerGanancia($conexion);
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}

// Método para obtener la ganancia
function obtenerGanancia($conexion) {
    // Obtener el filtro desde la URL
    $filtro = $_GET['filtro'];

    // Consultar la base de datos según el filtro
    switch ($filtro) {
        case 'hoy':
            $sql = "SELECT SUM(ganancia) AS total FROM ventas WHERE fecha = CURDATE()";
            break;
        case 'semana':
            $sql = "SELECT SUM(ganancia) AS total FROM ventas WHERE YEARWEEK(fecha) = YEARWEEK(CURDATE())";
            break;
        case 'mes':
            $sql = "SELECT SUM(ganancia) AS total FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
            break;
        case 'año':
            $sql = "SELECT SUM(ganancia) AS total FROM ventas WHERE YEAR(fecha) = YEAR(CURDATE())";
            break;
        default:
            $sql = "SELECT SUM(ganancia) AS total FROM ventas WHERE fecha = CURDATE()"; // Por defecto, hoy
    }

    // Ejecutar la consulta
    $resultado = $conexion->query($sql);

    // Obtener el total de ganancias
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $total = $fila['total'];
    } else {
        $total = 0;
    }

    // Devolver el resultado en formato JSON
    echo json_encode(['total' => $total]);
}

// Cerrar la conexión
$conexion->close();
?>