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

$DatosCompraActual = array();
$DetallesCompra = array();

// Procesar eliminación de detalle
if (!empty($_GET['eliminar_detalle'])) {
    if (Eliminar_Detalle_Compra($MiConexion, $_GET['eliminar_detalle'])) {
        $_SESSION['Mensaje'] = "Artículo eliminado de la compra";
        $_SESSION['Estilo'] = 'success';
        header("Location: modificar_compra.php?ID_COMPRA=".$_GET['ID_COMPRA']);
        exit;
    }
}

// Procesar actualización de cantidad
if (!empty($_POST['BotonActualizarCantidad'])) {
    $id_detalle = $_POST['BotonActualizarCantidad'];
    
    if (!isset($_POST['cantidad'][$id_detalle])) {
        $_SESSION['Mensaje'] = "Error: Cantidad no recibida";
        $_SESSION['Estilo'] = 'danger';
    } else {
        $cant = $_POST['cantidad'][$id_detalle];
        
        if (!ctype_digit($cant) || $cant < 1) {
            $_SESSION['Mensaje'] = "Cantidad debe ser entero positivo";
            $_SESSION['Estilo'] = 'danger';
        } else {
            if (Actualizar_Cantidad_Detalle_Compra($MiConexion, $id_detalle, $cant)) {
                $_SESSION['Mensaje'] = "Cantidad actualizada!";
                $_SESSION['Estilo'] = 'success';
            } else {
                $_SESSION['Mensaje'] = "Error al actualizar";
                $_SESSION['Estilo'] = 'danger';
            }
        }
    }
    header("Location: modificar_compra.php?ID_COMPRA=".$_POST['IdCompra']);
    exit;
}

// Obtener datos principales
if (!empty($_GET['ID_COMPRA'])) {
    $DatosCompraActual = Datos_Compra($MiConexion, $_GET['ID_COMPRA']);
    $DetallesCompra = Detalles_Compra($MiConexion, $_GET['ID_COMPRA']);
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Presupuesto</title>
</head>
<body>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Detalles de Presupuesto</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menú</a></li>
                <li class="breadcrumb-item">Presupuesto</li>
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
                        <p class="card-text"><?= $DatosCompraActual['PROVEEDOR'] ?></p>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">Fecha</h5>
                        <p class="card-text"><?= $DatosCompraActual['fecha'] ?></p>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">Responsable</h5>
                        <p class="card-text"><?= $DatosCompraActual['USUARIO'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <form method="post">
            <input type="hidden" name="IdCompra" value="<?= $DatosCompraActual['idCompra'] ?>">
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Artículos Presupuestados</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario (para OC)</th>
                                    <th>Subtotal</th> <!-- Nueva columna -->
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($DetallesCompra as $detalle) { ?>
                                <tr>
                                    <td><?= $detalle['ARTICULO'] ?></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" 
                                                name="cantidad[<?= $detalle['idDetalleCompra'] ?>]" 
                                                value="<?= $detalle['cantidad'] ?>" 
                                                min="1" 
                                                class="form-control">
                                            <button type="submit" 
                                                name="BotonActualizarCantidad" 
                                                value="<?= $detalle['idDetalleCompra'] ?>" 
                                                class="btn btn-success">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                name="precios[<?= $detalle['idDetalleCompra'] ?>]" 
                                                id="precio_<?= $detalle['idDetalleCompra'] ?>" 
                                                min="0.01" 
                                                step="0.01" 
                                                class="form-control precio-oc" 
                                                required>
                                        </div>
                                    </td>
                                    <td 
                                        class="subtotal text-end">$0.00</td> <!-- Nueva celda -->
                                    <td>
                                    <td>
                                        <a href="modificar_compra.php?ID_COMPRA=<?= $DatosCompraActual['idCompra'] ?>&eliminar_detalle=<?= $detalle['idDetalleCompra'] ?>" 
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
                                    <td class="total text-end fw-bold">$0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <a href="../listados/listados_compras.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <button type="button" class="btn btn-primary" onclick="validarPrecios()">
                            <i class="bi bi-file-earmark-text"></i> Generar Orden de Compra
                        </button>
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
        const precio = parseFloat(row.querySelector('.precio-oc').value) || 0;
        const subtotal = cantidad * precio;
        
        row.querySelector('.subtotal').textContent = `$${subtotal.toFixed(2)}`;
        
        if(!isNaN(subtotal)) {
            total += subtotal;
        }
    });
    
    document.querySelector('.total').textContent = `$${total.toFixed(2)}`;
}

// Calcular al cargar y cuando cambien precios
document.addEventListener('DOMContentLoaded', calcularTotales);
document.querySelectorAll('.precio-oc').forEach(input => {
    input.addEventListener('input', calcularTotales);
});

function validarPrecios() {
    let preciosValidos = true;
    const precios = {};
    
    document.querySelectorAll('.precio-oc').forEach(input => {
        const valor = parseFloat(input.value);
        const idDetalle = input.name.match(/\[(.*?)\]/)[1];
        
        if (isNaN(valor) || valor <= 0) {
            preciosValidos = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
            precios[idDetalle] = valor;
        }
    });
    
    if (!preciosValidos) {
        alert('Todos los precios deben ser valores numéricos positivos');
        return;
    }
    
    // Redireccionar con parámetros
    const idCompra = <?= $DatosCompraActual['idCompra'] ?>;
    window.location.href = `../agregar/generar_orden_compra.php?ID_COMPRA=${idCompra}&PRECIOS=${JSON.stringify(precios)}`;
}
// Actualizar total antes de enviar
    calcularTotales();
</script>

</body>
</html>