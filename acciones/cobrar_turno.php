<?php

session_start();
require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion = ConexionBD();

if (!empty($_GET['ID_TURNO'])) {
    if (Cobrar_Turno($MiConexion, $_GET['ID_TURNO'])) {
        $_SESSION['Mensaje'] = 'El turno fue marcado como cobrado.';
        $_SESSION['Estilo'] = 'success';
    } else {
        $_SESSION['Mensaje'] = 'No se pudo marcar el turno como cobrado.';
        $_SESSION['Estilo'] = 'warning';
    }
}
header('Location: ../listados/cobrar_turnos.php');
exit;
?>