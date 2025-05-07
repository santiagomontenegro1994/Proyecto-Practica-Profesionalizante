<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre']) ) {
        header('Location: ../inicio/cerrarsesion.php');
        exit;
    }
    
    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
   

    require_once '../funciones/select_general.php';

    if ( Eliminar_Venta($MiConexion , $_GET['ID_VENTA']) != false ) {
        $_SESSION['Mensaje'].='Se ha eliminado la venta seleccionada';
        $_SESSION['Estilo']='success';
    }else {
        $_SESSION['Mensaje'].='No se pudo eliminar la venta. <br /> ';
        $_SESSION['Estilo']='warning';
    }
    
   
    header('Location: ../listados/listados_ventas.php');
    exit;
?>