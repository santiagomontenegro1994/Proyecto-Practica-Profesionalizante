<?php
require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

header('Content-Type: application/json');

if (!isset($_POST['IdCompra']) || !isset($_POST['nuevo_estado_compra'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$MiConexion = ConexionBD();
$idCompra = $_POST['IdCompra'];
$nuevoEstado = $_POST['nuevo_estado_compra'];

if (Actualizar_Estado_Compra($MiConexion, $idCompra, $nuevoEstado)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $MiConexion->error]);
}
?>