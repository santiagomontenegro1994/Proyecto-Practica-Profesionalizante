<?php
// Cargar PhpSpreadsheet
require 'vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

// Declaraciones "use"
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

// Iniciar sesión
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: cerrarsesion.php');
    exit;
}

// Verifica si hay datos para guardar
if (!empty($_SESSION['Descarga'])) {
    // Crear una nueva instancia de Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Supongamos que $_SESSION['Descarga'] es un array de datos
    $datos = json_decode($_SESSION['Descarga'], true); // Convierte JSON a array (si es necesario)

    // Si los datos son un string, conviértelos a un array
    if (is_string($_SESSION['Descarga'])) {
        $lineas = explode("\n", $_SESSION['Descarga']);
        $datos = [];
        foreach ($lineas as $linea) {
            $datos[] = explode(',', $linea); // Divide cada línea por comas
        }
    }

    // Llenar la hoja de cálculo con los datos
    $fila = 1;
    foreach ($datos as $filaDatos) {
        $columna = 1;
        foreach ($filaDatos as $valor) {
            // Usar setCellValue en lugar de setCellValueByColumnAndRow
            $sheet->setCellValue([$columna, $fila], $valor);
            $columna++;
        }
        $fila++;
    }

    // Nombre del archivo con fecha y hora
    $FechaHoraHoy = date('Ymd_His');
    $NombreArchivo = "Lista_Turnos_$FechaHoraHoy.xls";

    // Crear un escritor para el formato XLS
    $writer = new Xls($spreadsheet);

    // Forzar la descarga del archivo
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $NombreArchivo . '"');
    header('Cache-Control: max-age=0');

    // Enviar el archivo al navegador
    $writer->save('php://output');
    exit;
} else {
    // Si no hay datos, redirigir con un mensaje
    $_SESSION['Mensaje'] = "No hay datos para guardar en el archivo.";
    header('Location: listados_turnos.php');
    exit;
}
?>