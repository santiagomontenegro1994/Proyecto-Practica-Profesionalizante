<?php
    session_start();
    if (empty($_SESSION['Usuario_Nombre']) ) {
        header('Location: ../inicio/cerrarsesion.php');
        exit;
    }
    
    require_once '../funciones/conexion.php';
    $MiConexion = ConexionBD();
   

    require_once '../funciones/select_general.php';

    if ( Eliminar_Pedido($MiConexion , $_GET['ID_PEDIDO']) != false ) {
        $_SESSION['Mensaje'].='Se ha eliminado el pedido seleccionado';
        $_SESSION['Estilo']='success';
    }else {
        $_SESSION['Mensaje'].='No se pudo eliminar el pedido. <br /> ';
        $_SESSION['Estilo']='warning';
    }
    
   
    header('Location: ../listados/listados_pedidos.php');
    exit;
?>