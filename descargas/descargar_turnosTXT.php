<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

// Verifica si hay datos para guardar
if (!empty($_SESSION['Descarga'])) {
    // Nombre del archivo con fecha y hora
    $FechaHoraHoy = date('Ymd_His');
    $NombreArchivo = "Lista_Turnos_$FechaHoraHoy.txt";

    // Forzar la descarga del archivo
    header('Content-Type: text/plain'); // Tipo de contenido
    header('Content-Disposition: attachment; filename="' . $NombreArchivo . '"'); // Forzar descarga
    header('Content-Length: ' . strlen($_SESSION['Descarga'])); // Tamaño del archivo

    // Enviar el contenido del archivo
    echo $_SESSION['Descarga'];
    exit;
} else {
    // Si no hay datos, redirigir con un mensaje
    $_SESSION['Mensaje'] = "No hay datos para guardar en el archivo.";
    header('Location: ../listados/listados_turnos.php');
    exit;
}
?>