<?php
ob_start();
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require('../encabezado.inc.php');
require('../barraLateral.inc.php');

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion = ConexionBD();

$DatosPedidoActual = array();
$DetallesPedido = array();

// Procesar eliminación de detalle
if (!empty($_GET['eliminar_detalle'])) {
    if (Eliminar_Detalle_Pedido($MiConexion, $_GET['eliminar_detalle'])) {
        $_SESSION['Mensaje'] = "Detalle eliminado correctamente";
        $_SESSION['Estilo'] = 'success';
        header("Location: modificar_pedido.php?ID_PEDIDO=".$_GET['ID_PEDIDO']);
        exit;
    } else {
        $_SESSION['Mensaje'] = "Error al eliminar el detalle";
        $_SESSION['Estilo'] = 'danger';
    }
}

// Procesar actualización de seña
if (!empty($_POST['BotonActualizarSenia'])) {
    if (Actualizar_Senia_Pedido($MiConexion, $_POST['IdPedido'], $_POST['senia'])) {
        $_SESSION['Mensaje'] = "Seña actualizada correctamente";
        $_SESSION['Estilo'] = 'success';
    } else {
        $_SESSION['Mensaje'] = "Error al actualizar la seña";
        $_SESSION['Estilo'] = 'danger';
    }
    header("Location: modificar_pedido.php?ID_PEDIDO=".$_POST['IdPedido']);
    exit;
}

// Procesar actualización de cantidad
if (!empty($_POST['BotonActualizarCantidad'])) {
    $id_detalle = $_POST['BotonActualizarCantidad'];
    
    if (!isset($_POST['cantidad'][$id_detalle])) {
        $_SESSION['Mensaje'] = "No se recibió la cantidad";
        $_SESSION['Estilo'] = 'danger';
    } else {
        $cant = $_POST['cantidad'][$id_detalle];
        
        if (!is_numeric($cant) || $cant < 1 || !ctype_digit((string)$cant)) {
            $_SESSION['Mensaje'] = "Cantidad debe ser entero mayor a 0";
            $_SESSION['Estilo'] = 'danger';
        } else {
            $cantidad = (int)$cant;
            if (Actualizar_Cantidad_Detalle($MiConexion, $id_detalle, $cantidad)) {
                $_SESSION['Mensaje'] = "Cantidad actualizada correctamente";
                $_SESSION['Estilo'] = 'success';
            } else {
                $_SESSION['Mensaje'] = "Error al actualizar cantidad";
                $_SESSION['Estilo'] = 'danger';
            }
        }
    }
    header("Location: modificar_pedido.php?ID_PEDIDO=".$_POST['IdPedido']);
    exit;
}

// Procesar actualización de estado
if (!empty($_POST['BotonActualizarEstado'])) {
    if (Actualizar_Estado_Pedido($MiConexion, $_POST['IdPedido'], $_POST['estado'])) {
        $_SESSION['Mensaje'] = "Estado actualizado correctamente";
        $_SESSION['Estilo'] = 'success';
    } else {
        $_SESSION['Mensaje'] = "Error al actualizar el estado";
        $_SESSION['Estilo'] = 'danger';
    }
    header("Location: modificar_pedido.php?ID_PEDIDO=".$_POST['IdPedido']);
    exit;
}

// Obtener datos del pedido
if (!empty($_GET['ID_PEDIDO'])) {
    $DatosPedidoActual = Datos_Pedido($MiConexion, $_GET['ID_PEDIDO']);
    $DetallesPedido = Detalles_Pedido($MiConexion, $_GET['ID_PEDIDO']);
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Pedido</title>
</head>
<body>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Detalles Pedido</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../inicio/index.php">Menú</a></li>
                    <li class="breadcrumb-item">Pedidos</li>
                    <li class="breadcrumb-item active">Detalles Pedido</li>
                </ol>
            </nav>
        </div>

        <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?= $_SESSION['Estilo'] ?> alert-dismissible fade show">
                <?= $_SESSION['Mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="section">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-end w-100">
                        <div class="card-title">Cliente: <span class="text-dark fs-5"><?= $DatosPedidoActual['CLIENTE_N'] ?>, <?= $DatosPedidoActual['CLIENTE_A'] ?></span></div>
                        <div class="card-title">Vendedor: <span class="text-dark fs-5"><?= $DatosPedidoActual['VENDEDOR'] ?></span></div>
                        <div class="card-title">Fecha: <span class="text-dark fs-5"><?= $DatosPedidoActual['FECHA'] ?></span></div>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Formulario para cantidades -->
            <form method="post">
                <input type="hidden" name="IdPedido" value="<?= $DatosPedidoActual['ID_PEDIDO'] ?>">
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Detalles del Pedido</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio Unitario</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $subtotal = 0;
                                    foreach ($DetallesPedido as $detalle) { 
                                        $total_detalle = $detalle['CANTIDAD'] * $detalle['PRECIO_VENTA'];
                                        $subtotal += $total_detalle;
                                    ?>
                                    <tr>
                                        <td><?= $detalle['PRODUCTO'] ?></td>
                                        <td>$<?= number_format($detalle['PRECIO_VENTA'], 2, ',', '.') ?></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" 
                                                    name="cantidad[<?= $detalle['ID_DETALLE'] ?>]" 
                                                    value="<?= $detalle['CANTIDAD'] ?>" 
                                                    min="1" 
                                                    class="form-control" 
                                                    style="width: 80px;">
                                                <button type="submit" 
                                                    name="BotonActualizarCantidad" 
                                                    value="<?= $detalle['ID_DETALLE'] ?>" 
                                                    class="btn btn-success btn-sm">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>$<?= number_format($total_detalle, 2, ',', '.') ?></td>
                                        <td>
                                            <a href="modificar_pedido.php?ID_PEDIDO=<?= $DatosPedidoActual['ID_PEDIDO'] ?>&eliminar_detalle=<?= $detalle['ID_DETALLE'] ?>" 
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar este producto del pedido?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Formulario para actualizar seña -->
            <form method="post" class="mt-4">
                <input type="hidden" name="IdPedido" value="<?= $DatosPedidoActual['ID_PEDIDO'] ?>">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Seña actual:</label>
                                <div class="h4 text-primary">$<?= number_format($DatosPedidoActual['SENIA'], 2, ',', '.') ?></div>
                            </div>
                            <div class="col-md-4">
                                <input type="number" 
                                    class="form-control" 
                                    name="senia" 
                                    value="<?= $DatosPedidoActual['SENIA'] ?>" 
                                    step="0.01" 
                                    min="0">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" name="BotonActualizarSenia" value="actualizar" class="btn btn-primario w-100">
                                    <i class="bi bi-pencil-square"></i> Actualizar Seña
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Formulario para actualizar estado -->
            <form method="post" class="mt-4">
                <input type="hidden" name="IdPedido" value="<?= $DatosPedidoActual['ID_PEDIDO'] ?>">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Estado actual:</label>
                                <div class="h4 text-primary"><?= $DatosPedidoActual['ESTADO'] ?></div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="estado" required>
                                    <?php 
                                    $estados = Lista_Estados_Pedido($MiConexion);
                                    foreach ($estados as $estado) {
                                        $selected = ($estado['ID_ESTADO'] == $DatosPedidoActual['ID_ESTADO']) ? 'selected' : '';
                                        echo "<option value='{$estado['ID_ESTADO']}' $selected>{$estado['ESTADO']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" name="BotonActualizarEstado" value="actualizar" class="btn btn-primario w-100">
                                    <i class="bi bi-arrow-repeat"></i> Actualizar Estado
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Sección de resumen -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <tr class="table-primary">
                                    <th>Subtotal:</th>
                                    <td>$<?= number_format($subtotal, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th>Descuento (<?= $DatosPedidoActual['DESCUENTO'] ?>%):</th>
                                    <td>$<?= number_format($subtotal * $DatosPedidoActual['DESCUENTO']/100, 2, ',', '.') ?></td>
                                </tr>
                                <tr class="table-secondary">
                                    <th>Total con descuento:</th>
                                    <td>$<?= number_format($subtotal - ($subtotal * $DatosPedidoActual['DESCUENTO']/100), 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th>Seña:</th>
                                    <td>$<?= number_format($DatosPedidoActual['SENIA'], 2, ',', '.') ?></td>
                                </tr>
                                <tr class="table-success">
                                    <th>Saldo a pagar:</th>
                                    <td>$<?= number_format(
                                        ($subtotal - ($subtotal * $DatosPedidoActual['DESCUENTO']/100)) - $DatosPedidoActual['SENIA'], 
                                        2, 
                                        ',', 
                                        '.'
                                    ) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="../listados/listados_pedidos.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php
    $_SESSION['Mensaje'] = '';
    require('../footer.inc.php');
    ?>
    
</body>
</html>