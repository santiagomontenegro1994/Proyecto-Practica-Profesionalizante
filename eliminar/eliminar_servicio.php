<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre']) ) {
        header('Location: ../inicio/cerrarsesion.php');
        exit;
    }
    
    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
    require_once '../funciones/select_general.php';

    if (Eliminar_Servicio($MiConexion, $_GET['ID_SERVICIO']) != false) {
        $_SESSION['Mensaje'] = 'Se ha eliminado el servicio seleccionado';
        $_SESSION['Estilo'] = 'success';
    } else {
        $_SESSION['Mensaje'] = 'No se pudo borrar el servicio.<br />';
        $_SESSION['Estilo'] = 'warning';
    }
    
    header('Location: ../listados/listados_servicios.php');
    exit;
?>