<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

if (!empty($_GET['ID_PEDIDO'])) {
    try {
        // Obtener datos del pedido con la nueva función
        $pedido = Datos_Pedido_Para_Retiro($MiConexion, $_GET['ID_PEDIDO']);
        
        if (empty($pedido)) {
            throw new Exception("Pedido no encontrado");
        }
        
        // Verificar estado actual del pedido
        if ($pedido['idEstado'] == 3) { // 3 = Finalizado
            throw new Exception("El pedido ya está marcado como finalizado");
        }
        
        if ($pedido['idEstado'] == 4) { // 4 = Cancelado
            throw new Exception("No se puede retirar un pedido cancelado");
        }
        
        // Calcular total con descuento
        $total_con_descuento = $pedido['precioTotal'] * (1 - ($pedido['descuento'] / 100));
        
        // Actualizar el pedido
        $query = "UPDATE pedidos 
                 SET idEstado = 3, 
                     senia = ? 
                 WHERE idPedido = ?";
        
        $stmt = $MiConexion->prepare($query);
        $stmt->bind_param("di", $total_con_descuento, $_GET['ID_PEDIDO']);
        
        if ($stmt->execute()) {
            $_SESSION['Mensaje'] = "Pedido marcado como retirado y saldo cancelado";
            $_SESSION['Estilo'] = 'success';
        } else {
            throw new Exception("Error al actualizar el pedido");
        }
        
    } catch (Exception $e) {
        $_SESSION['Mensaje'] = $e->getMessage();
        $_SESSION['Estilo'] = 'danger';
    }
} else {
    $_SESSION['Mensaje'] = "No se especificó un pedido";
    $_SESSION['Estilo'] = 'danger';
}

header("Location: ../listados/listados_pedidos.php");
exit;
?>