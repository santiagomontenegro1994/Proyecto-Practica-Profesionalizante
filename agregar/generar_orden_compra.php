<?php
session_start();
ob_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

// Validar parámetros recibidos
if(empty($_GET['ID_COMPRA']) || empty($_GET['PRECIOS'])) {
    $_SESSION['Mensaje'] = "Datos incompletos para generar la orden";
    $_SESSION['Estilo'] = 'danger';
    header('Location: ../listados/listados_compras.php');
    exit;
}

try {
    // Obtener datos de la compra/presupuesto
    $id_compra = $_GET['ID_COMPRA'];
    $precios = json_decode($_GET['PRECIOS'], true);
    
    $compra = Datos_Compra($MiConexion, $id_compra);
    $detalles_compra = Detalles_Compra($MiConexion, $id_compra);
    
    if(empty($compra) || empty($detalles_compra)) {
        throw new Exception("Compra no encontrada");
    }

    // Iniciar transacción
    $MiConexion->autocommit(false);

    // Insertar orden de compra
    $sql_orden = "INSERT INTO orden_compra 
                 (idProveedor, fecha, idUsuario) 
                 VALUES (?, CURDATE(), ?)";
    
    $stmt_orden = $MiConexion->prepare($sql_orden);
    $stmt_orden->bind_param("ii", $compra['idProveedor'], $_SESSION['Usuario_Id']);
    $stmt_orden->execute();

    $id_orden = $MiConexion->insert_id;

    // Insertar detalles
    foreach($detalles_compra as $detalle) {
        $id_detalle_compra = $detalle['idDetalleCompra'];
        
        if(!isset($precios[$id_detalle_compra])) {
            throw new Exception("Falta precio para el artículo: ".$detalle['ARTICULO']);
        }
        
        $precio_unitario = $precios[$id_detalle_compra];
        
        $sql_detalle = "INSERT INTO detalle_orden_compra 
                       (idOrdenCompra, idArticulo, cantidad, precio) 
                       VALUES (?, ?, ?, ?)";
        
        $stmt_detalle = $MiConexion->prepare($sql_detalle);
        $stmt_detalle->bind_param("iiid", $id_orden, $detalle['idArticulo'], $detalle['cantidad'], $precio_unitario);
        $stmt_detalle->execute();
    }

    // Confirmar transacción
    $MiConexion->commit();

    $_SESSION['Mensaje'] = "Orden de compra generada exitosamente!";
    $_SESSION['Estilo'] = 'success';
    header("Location: ../listados/listados_compras.php");
    exit;

} catch(PDOException $e) {
    $MiConexion->rollBack();
    $_SESSION['Mensaje'] = "Error al generar orden: ".$e->getMessage();
    $_SESSION['Estilo'] = 'danger';
    header('Location: ../modificar/modificar_compra.php?ID_COMPRA='.$id_compra);
    exit;
} catch(Exception $e) {
    $MiConexion->rollBack();
    $_SESSION['Mensaje'] = $e->getMessage();
    $_SESSION['Estilo'] = 'danger';
    header('Location: ../modificar/modificar_compra.php?ID_COMPRA='.$id_compra);
    exit;
}

ob_end_flush();
?>