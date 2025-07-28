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

$DatosOrdenActual = array();
$DetallesOrden = array();

// Procesar eliminación de detalle
if (!empty($_GET['eliminar_detalle'])) {
    if (Eliminar_Detalle_Orden($MiConexion, $_GET['eliminar_detalle'])) {
        $_SESSION['Mensaje'] = "Artículo eliminado de la orden";
        $_SESSION['Estilo'] = 'success';
        header("Location: modificar_orden_compra.php?ID_ORDEN=".$_GET['ID_ORDEN']);
        exit;
    }
}

// Procesar actualización de cantidad y precio
if (!empty($_POST['BotonActualizar'])) {
    $id_detalle = $_POST['BotonActualizar'];
    
    $errores = [];
    
    // Validar cantidad
    if (!isset($_POST['cantidad'][$id_detalle]) || !ctype_digit($_POST['cantidad'][$id_detalle]) || $_POST['cantidad'][$id_detalle] < 1) {
        $errores[] = "Cantidad inválida";
    }
    
    // Validar precio
    if (!isset($_POST['precio'][$id_detalle]) || !is_numeric($_POST['precio'][$id_detalle]) || $_POST['precio'][$id_detalle] <= 0) {
        $errores[] = "Precio inválido";
    }
    
    if (empty($errores)) {
        if (Actualizar_Detalle_Orden($MiConexion, $id_detalle, $_POST['cantidad'][$id_detalle], $_POST['precio'][$id_detalle])) {
            $_SESSION['Mensaje'] = "Actualización exitosa!";
            $_SESSION['Estilo'] = 'success';
        } else {
            $_SESSION['Mensaje'] = "Error al actualizar";
            $_SESSION['Estilo'] = 'danger';
        }
    } else {
        $_SESSION['Mensaje'] = implode("<br>", $errores);
        $_SESSION['Estilo'] = 'danger';
    }
    
    header("Location: modificar_orden_compra.php?ID_ORDEN=".$_POST['IdOrden']);
    exit;
}

// Procesar cambio de estado 
if (!empty($_POST['BotonCambiarEstado'])) {
    error_log("Intentando actualizar estado. ID Orden: ".$_POST['IdOrden'].", Nuevo estado: ".$_POST['nuevo_estado']);
    
    if (Actualizar_Estado_Orden($MiConexion, $_POST['IdOrden'], $_POST['nuevo_estado'])) {
        $_SESSION['Mensaje'] = "Estado de la orden actualizado correctamente a ".$_POST['nuevo_estado'];
        $_SESSION['Estilo'] = 'success';
        error_log("Estado actualizado correctamente");
    } else {
        $_SESSION['Mensaje'] = "Error al actualizar el estado: ".$MiConexion->error;
        $_SESSION['Estilo'] = 'danger';
        error_log("Error al actualizar estado: ".$MiConexion->error);
    }
    header("Location: modificar_orden_compra.php?ID_ORDEN=".$_POST['IdOrden']);
    exit;
}

// Obtener datos principales
if (!empty($_GET['ID_ORDEN'])) {
    $DatosOrdenActual = Datos_Orden_Compra($MiConexion, $_GET['ID_ORDEN']);
    $DetallesOrden = Detalles_Orden_Compra($MiConexion, $_GET['ID_ORDEN']);
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Orden de Compra</title>
</head>
<body>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Detalles de Orden de Compra</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menú</a></li>
                <li class="breadcrumb-item">Órdenes de Compra</li>
                <li class="breadcrumb-item active">Modificar</li>
            </ol>
        </nav>
    </div>

    <?php if (!empty($_SESSION['Mensaje'])) { ?>
        <div class="alert alert-<?= $_SESSION['Estilo'] ?> alert-dismissible fade show">
            <?= $_SESSION['Mensaje'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5 class="card-title">Proveedor</h5>
                        <p class="card-text"><?= $DatosOrdenActual['PROVEEDOR'] ?></p>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">Fecha</h5>
                        <p class="card-text"><?= $DatosOrdenActual['fecha'] ?></p>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">Responsable</h5>
                        <p class="card-text"><?= $DatosOrdenActual['USUARIO'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <form method="post">
            <input type="hidden" name="IdOrden" value="<?= $DatosOrdenActual['idOrdenCompra'] ?>">
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Artículos de la Orden</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($DetallesOrden as $detalle) { ?>
                                <tr>
                                    <td><?= $detalle['ARTICULO'] ?></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" 
                                                name="cantidad[<?= $detalle['idDetalleOrdenCompra'] ?>]" 
                                                value="<?= $detalle['cantidad'] ?>" 
                                                min="1" 
                                                class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                name="precio[<?= $detalle['idDetalleOrdenCompra'] ?>]" 
                                                value="<?= $detalle['precio'] ?>" 
                                                step="0.01" 
                                                min="0.01" 
                                                class="form-control precio">
                                        </div>
                                    </td>
                                    <td class="subtotal text-end">
                                        $<?= number_format($detalle['cantidad'] * $detalle['precio'], 2) ?>
                                    </td>
                                    <td>
                                        <button type="submit" 
                                            name="BotonActualizar" 
                                            value="<?= $detalle['idDetalleOrdenCompra'] ?>" 
                                            class="btn btn-success me-2">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <a href="modificar_orden_compra.php?ID_ORDEN=<?= $DatosOrdenActual['idOrdenCompra'] ?>&eliminar_detalle=<?= $detalle['idDetalleOrdenCompra'] ?>" 
                                            class="btn btn-danger"
                                            onclick="return confirm('¿Eliminar este artículo?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total General:</td>
                                    <td class="total text-end fw-bold">
                                        $<?= number_format($DatosOrdenActual['PRECIO_TOTAL'], 2) ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <!-- Sección para cambiar estado -->
    <section class="section">
        <form method="post">
            <input type="hidden" name="IdOrden" value="<?= $DatosOrdenActual['idOrdenCompra'] ?>">
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Cambiar Estado de la Orden</h5>
                    
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Estado actual:</label>
                            <div class="h4">
                                <?= $DatosOrdenActual['ESTADO'] ?>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <select class="form-select" name="nuevo_estado" required>
                                <?php 
                                $estados = Lista_Estados_Orden($MiConexion);
                                foreach ($estados as $estado) {
                                    $selected = ($estado['id'] == $DatosOrdenActual['idEstado']) ? 'selected' : '';
                                    echo "<option value='{$estado['id']}' $selected>{$estado['denominacion']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <button type="submit" name="BotonCambiarEstado" value=1 class="btn btn-primary w-100">
                                <i class="bi bi-arrow-repeat"></i> Actualizar Estado
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <a href="../listados/listados_ordenes_compra.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </section>
</main>

<?php
$_SESSION['Mensaje'] = '';
require('../footer.inc.php');
?>

<script>
function calcularTotales() {
    let total = 0;
    
    document.querySelectorAll('tbody tr').forEach(row => {
        const cantidad = parseFloat(row.querySelector('input[name^="cantidad"]').value);
        const precio = parseFloat(row.querySelector('.precio').value) || 0;
        const subtotal = cantidad * precio;
        
        row.querySelector('.subtotal').textContent = `$${subtotal.toFixed(2)}`;
        
        if(!isNaN(subtotal)) {
            total += subtotal;
        }
    });
    
    document.querySelector('.total').textContent = `$${total.toFixed(2)}`;
}

document.addEventListener('DOMContentLoaded', calcularTotales);
document.querySelectorAll('.precio, input[name^="cantidad"]').forEach(input => {
    input.addEventListener('input', calcularTotales);
});

// Validación para cambio de estado
document.querySelector('select[name="nuevo_estado"]').addEventListener('change', function() {
    if (this.value == 3) { // 3 = Finalizado (ajusta según tus IDs de estado)
        if (!confirm('¿Confirmas que deseas marcar esta orden como FINALIZADA? Esta acción no se puede deshacer.')) {
            this.value = this.dataset.prevValue;
            return false;
        }
    }
    this.dataset.prevValue = this.value;
});
</script>

</body>
</html>