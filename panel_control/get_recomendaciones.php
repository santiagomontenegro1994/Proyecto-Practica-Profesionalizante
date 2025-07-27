<?php
// Configuración inicial
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/recommendations_errors.log');

header('Content-Type: application/json; charset=utf-8');

function jsonError($message, $code = 500) {
    http_response_code($code);
    die(json_encode(['error' => true, 'message' => $message]));
}

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Método no permitido', 405);
}

// Validar parámetros
$periodo = $_GET['periodo'] ?? 'hoy';
$allowedPeriods = ['hoy', 'semana', 'mes', 'anio', 'personalizado'];
if (!in_array($periodo, $allowedPeriods)) {
    jsonError('Período no válido', 400);
}

// RUTA ABSOLUTAMENTE CORRECTA AL HELPER
$basePath = realpath(__DIR__ . '/..') . '/';
$helperPath = $basePath . 'funciones/openai_helper.php';

// Depuración: Registrar la ruta que está buscando
error_log("Buscando helper en: " . $helperPath);

if (!file_exists($helperPath)) {
    jsonError('Archivo helper no encontrado en: ' . $helperPath, 500);
}

// Incluir el helper
require_once $helperPath;

// Función para obtener datos
function getTurnosData($type, $periodo, $fechaInicio = null, $fechaFin = null) {
    $baseUrl = "http://{$_SERVER['HTTP_HOST']}/Proyecto-Practica-Profesionalizante/panel_control/get_turnos_data.php";
    $url = "$baseUrl?tipo=$type&periodo=$periodo";
    
    if ($periodo === 'personalizado') {
        $url .= "&fecha_inicio=$fechaInicio&fecha_fin=$fechaFin";
    }
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        throw new Exception("Error al obtener datos de $type desde: $url");
    }
    
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar respuesta de $type");
    }
    
    if (isset($data['error'])) {
        throw new Exception($data['message'] ?? "Error en datos de $type");
    }
    
    return $data;
}

try {
    // Obtener datos necesarios
    $datos = [
        'turnosHoy' => getTurnosData('turnosHoy', $periodo, $_GET['fecha_inicio'] ?? null, $_GET['fecha_fin'] ?? null),
        'ingresosTurnos' => getTurnosData('ingresosTurnos', $periodo, $_GET['fecha_inicio'] ?? null, $_GET['fecha_fin'] ?? null),
        'estadoChart' => getTurnosData('estadoChart', $periodo, $_GET['fecha_inicio'] ?? null, $_GET['fecha_fin'] ?? null),
        'estilistaChart' => getTurnosData('estilistaChart', $periodo, $_GET['fecha_inicio'] ?? null, $_GET['fecha_fin'] ?? null),
        'horarioChart' => getTurnosData('horarioChart', $periodo, $_GET['fecha_inicio'] ?? null, $_GET['fecha_fin'] ?? null)
    ];

    // Obtener recomendaciones
    $recomendaciones = obtenerRecomendacionesDeOpenAI($datos, $periodo);

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'recomendaciones' => $recomendaciones,
        'periodo' => $periodo
    ]);

} catch (Exception $e) {
    jsonError($e->getMessage());
}
?>