<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre']) ) {
        header('Location: ../inicio/cerrarsesion.php');
        exit;
    }
    
    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
   

    require_once '../funciones/select_general.php';

    if ( Eliminar_Orden_Compra($MiConexion , $_GET['ID_ORDEN']) != false ) {
        $_SESSION['Mensaje'].='Se ha eliminado la orden de compra seleccionada';
        $_SESSION['Estilo']='success';
    }else {
        $_SESSION['Mensaje'].='No se pudo eliminar orden de compra. <br /> ';
        $_SESSION['Estilo']='warning';
    }
    
   
    header('Location: ../listados/listados_ordenes_compra.php');
    exit;
?>