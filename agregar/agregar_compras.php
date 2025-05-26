<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require('../encabezado.inc.php');
require('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();
$Mensaje = '';
$Estilo = 'warning';

// Obtener listados para selects
$Proveedores = Listado_Proveedores($MiConexion);
$Productos = Listado_Productos($MiConexion);

if (isset($_POST['BotonRegistrarCompra'])) {
    $Mensaje = Validar_Compra();
    if (empty($Mensaje)) {
        if (Insertar_Compra($MiConexion)) {
            $Mensaje = '¡Orden registrada correctamente!';
            $_POST = array();
            $Estilo = 'success';
        } else {
            $Mensaje = 'Error: ' . ($GLOBALS['error_compra'] ?? 'Error desconocido'); // Mostrar error real
            $Estilo = 'danger';
        }
    }
}

?>

<main id="main" class="main">
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Nueva Orden de Compra</h5>

                <form method="post">
                    <?php if (!empty($Mensaje)) : ?>
                        <div class="alert alert-<?= $Estilo ?>"><?= $Mensaje ?></div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Proveedor</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="idProveedor" required>
                                <option value="">Seleccionar proveedor...</option>
                                <?php while($prov = mysqli_fetch_assoc($Proveedores)) : ?>
                                    <option value="<?= $prov['idProveedor'] ?>" <?= (!empty($_POST['idProveedor']) && $_POST['idProveedor'] == $prov['idProveedor'] ? 'selected' : '') ?>>
                                        <?= $prov['razon_social'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Fecha</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" 
                                   name="fecha" value="<?= $_POST['fecha'] ?? date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Artículos</label>
                        <div class="col-sm-10">
                            <div id="articulos-container">
                                <?php $articulos = $_POST['idArticulo'] ?? []; ?>
                                <?php foreach($articulos as $index => $idArticulo): ?>
                                <div class="articulo-fila mb-2">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <select class="form-select" name="idArticulo[]" required>
                                                <option value="">Seleccionar producto...</option>
                                                <?php mysqli_data_seek($Productos, 0); ?>
                                                <?php while($prod = mysqli_fetch_assoc($Productos)) : ?>
                                                    <option value="<?= $prod['idProducto'] ?>" <?= ($idArticulo == $prod['idProducto'] ? 'selected' : '') ?>>
                                                        <?= $prod['nombre'] ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" class="form-control" 
                                                   name="cantidad[]" min="1" 
                                                   value="<?= $_POST['cantidad'][$index] ?? '' ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger quitar-fila">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if(empty($articulos)): ?>
                                <div class="articulo-fila mb-2">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <select class="form-select" name="idArticulo[]" required>
                                                <option value="">Seleccionar producto...</option>
                                                <?php mysqli_data_seek($Productos, 0); ?>
                                                <?php while($prod = mysqli_fetch_assoc($Productos)) : ?>
                                                    <option value="<?= $prod['idProducto'] ?>">
                                                        <?= $prod['nombre'] ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" class="form-control" 
                                                   name="cantidad[]" min="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger quitar-fila">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="button" id="agregar-articulo" class="btn btn-success mt-2">
                                <i class="bi bi-plus-circle"></i> Agregar Artículo
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Descripción</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="descripcion" 
                                      rows="3"><?= $_POST['descripcion'] ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="BotonRegistrarCompra" value="1">
                          Registrar Compra
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php require('../footer.inc.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('articulos-container');
    const btnAgregar = document.getElementById('agregar-articulo');
    
    // Plantilla para nuevas filas
    const nuevaFilaHTML = `
    <div class="articulo-fila mb-2">
        <div class="row g-3">
            <div class="col-md-5">
                <select class="form-select" name="idArticulo[]" required>
                    <option value="">Seleccionar producto...</option>
                    <?php mysqli_data_seek($Productos, 0); ?>
                    <?php while($prod = mysqli_fetch_assoc($Productos)) : ?>
                        <option value="<?= $prod['idProducto'] ?>">
                            <?= $prod['nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-5">
                <input type="number" class="form-control" 
                       name="cantidad[]" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger quitar-fila">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>`;

    btnAgregar.addEventListener('click', function() {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = nuevaFilaHTML;
        const nuevaFila = tempDiv.firstElementChild;
        container.appendChild(nuevaFila);
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.quitar-fila')) {
            const filas = container.querySelectorAll('.articulo-fila');
            if (filas.length > 1) {
                e.target.closest('.articulo-fila').remove();
            }
        }
    });
});
</script>

</body>
</html>