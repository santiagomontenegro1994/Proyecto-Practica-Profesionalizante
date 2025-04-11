<?php
// Iniciar sesión
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: ../cerrarsesion.php');
    exit;
}

// Verifica si hay datos para guardar
if (!empty($_SESSION['Descarga'])) {
    // Convertir los datos a un array
    $datos = [];

    // Si los datos son un string, conviértelos a un array
    if (is_string($_SESSION['Descarga'])) {
        $lineas = explode("\n", $_SESSION['Descarga']);
        foreach ($lineas as $linea) {
            // Envolver toda la fecha en comillas dobles
            $linea = preg_replace('/(Fecha:\s\d{4}-\d{2}-\d{2})/', '"$1"', $linea);

            // Dividir cada línea por el guion (-), asegurando que los valores se mantengan juntos
            $fila = array_map('trim', explode(' - ', $linea));

            $datos[] = $fila; // Agregar la fila procesada al array de datos
        }
    }

    // Nombre del archivo con fecha y hora
    $FechaHoraHoy = date('Ymd_His');
    $NombreArchivo = "Lista_Turnos_$FechaHoraHoy.csv";

    // Forzar la descarga del archivo CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $NombreArchivo . '"');
    header('Cache-Control: max-age=0');

    // Abrir un flujo de salida para escribir el archivo CSV
    $output = fopen('php://output', 'w');

    // Escribir cada fila en el archivo CSV
    foreach ($datos as $fila) {
        fputcsv($output, $fila, ';'); // Escribir la fila con ';' como separador
    }

    // Cerrar el flujo de salida
    fclose($output);
    exit;
} else {
    // Si no hay datos, redirigir con un mensaje
    $_SESSION['Mensaje'] = "No hay datos para guardar en el archivo.";
    header('Location: ../listados/listados_turnos.php');
    exit;
}
?>
