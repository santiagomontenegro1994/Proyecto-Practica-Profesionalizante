<?php
// Cargar PhpSpreadsheet
require 'vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

// Declaraciones "use"
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; // Corregido: Usar Xlsx en lugar de Xls

// Iniciar sesión
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: cerrarsesion.php');
    exit;
}

// Verificar si hay datos para guardar
if (!empty($_SESSION['Descarga'])) {

    // Crear nuevo documento Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Si los datos son un string, conviértelos a un array
    $lineas = explode("\n", $_SESSION['Descarga']);
    $filaNum = 1; // Fila inicial en Excel

    foreach ($lineas as $linea) {
        // Envolver toda la fecha en comillas dobles
        $linea = preg_replace('/(Fecha:\s\d{4}-\d{2}-\d{2})/', '"$1"', $linea);

        // Dividir cada línea por " - ", asegurando que los valores se mantengan juntos
        $fila = array_map('trim', explode(' - ', $linea));

        // Insertar datos en la fila de Excel
        $colNum = 1; // Columna inicial en Excel (A = 1)
        foreach ($fila as $valor) {
            $sheet->setCellValue(chr(64 + $colNum) . $filaNum, $valor);
            $colNum++;
        }

        $filaNum++; // Avanzar a la siguiente fila
    }

    // Nombre del archivo con fecha y hora
    $FechaHoraHoy = date('Ymd_His');
    $NombreArchivo = "Lista_Turnos_$FechaHoraHoy.xlsx";

    // Crear escritor de Excel
    $writer = new Xlsx($spreadsheet); // Corregido: Usar Xlsx en lugar de Xls

    // Configurar cabeceras para la descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $NombreArchivo . '"');
    header('Cache-Control: max-age=0');

    // Guardar el archivo en la salida
    $writer->save('php://output');
    exit;
} else {
    // Si no hay datos, redirigir con un mensaje
    $_SESSION['Mensaje'] = "No hay datos para guardar en el archivo.";
    header('Location: listados_turnos.php');
    exit;
}
?>