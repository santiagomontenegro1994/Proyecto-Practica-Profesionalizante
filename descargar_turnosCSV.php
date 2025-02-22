<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: cerrarsesion.php');
    exit;
}

// Verifica si hay datos para guardar
if (!empty($_SESSION['Descarga']) && is_string($_SESSION['Descarga'])) {
    // Convertir el string a un array
    $lineas = explode("\n", $_SESSION['Descarga']); // Divide el string por saltos de línea
    $datos = [];
    foreach ($lineas as $linea) {
        $datos[] = explode(',', $linea); // Divide cada línea por comas
    }

    // Convertir los datos a formato CSV
    $csvContent = '';

    // Encabezados (opcional)
    $csvContent .= "Turno,Hora\n";

    // Datos
    foreach ($datos as $fila) {
        $csvContent .= implode(',', $fila) . "\n";
    }

    // Nombre del archivo con fecha y hora
    $FechaHoraHoy = date('Ymd_His');
    $NombreArchivo = "Lista_Turnos_$FechaHoraHoy.csv";

    // Forzar la descarga del archivo
    header('Content-Type: text/csv'); // Tipo de contenido para CSV
    header('Content-Disposition: attachment; filename="' . $NombreArchivo . '"'); // Forzar descarga
    header('Content-Length: ' . strlen($csvContent)); // Tamaño del archivo

    // Enviar el contenido del archivo
    echo $csvContent;
    exit;
} else {
    // Si no hay datos, redirigir con un mensaje
    $_SESSION['Mensaje'] = "No hay datos para guardar en el archivo.";
    header('Location: listados_turnos.php');
    exit;
}
?>