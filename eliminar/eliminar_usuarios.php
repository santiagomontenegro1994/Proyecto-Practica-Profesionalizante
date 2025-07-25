<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre'])) {
        header('Location: ../inicio/cerrarsesion.php');
        exit;
    }

    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
    require_once '../funciones/select_general.php';

    if (Eliminar_Usuario($MiConexion, $_GET['ID_USUARIO']) != false) {
        $_SESSION['Mensaje'] = 'Se ha eliminado el usuario seleccionado';
        $_SESSION['Estilo'] = 'success';
    } else {
        $_SESSION['Mensaje'] = 'No se pudo borrar el usuario.<br />';
        $_SESSION['Estilo'] = 'warning';
    }

    header('Location: ../listados/listados_usuarios.php');
    exit;
?>