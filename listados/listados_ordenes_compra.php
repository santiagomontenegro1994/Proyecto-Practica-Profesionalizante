<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require('../encabezado.inc.php');
require('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();
require_once '../funciones/select_general.php';

// Obtener el listado de órdenes de compra
$ListadoOrdenes = Listar_Ordenes_Compra($MiConexion);
$CantidadOrdenes = count($ListadoOrdenes);

// Buscar órdenes según un parámetro
if (!empty($_POST['BotonBuscar'])) {
    $parametro = $_POST['parametro'];
    $criterio = $_POST['gridRadios'];
    $ListadoOrdenes = Listar_Ordenes_Compra_Parametro($MiConexion, $criterio, $parametro);
    $CantidadOrdenes = count($ListadoOrdenes);
}
?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Órdenes de Compra</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../inicio/index.php">Menú</a></li>
      <li class="breadcrumb-item">Compras</li>
      <li class="breadcrumb-item active">Listado Órdenes de Compra</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Órdenes de Compra</h5>
          <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
              <?php echo $_SESSION['Mensaje'] ?>
            </div>
          <?php } ?>

          <form method="POST">
          <div class="row mb-4">
            <label for="inputBuscar" class="col-sm-1 col-form-label">Buscar</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" name="parametro" id="parametro">
              </div>
              <style> .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5; border-radius: 0.2rem; } </style>
              <div class="col-sm-3 mt-2">
                <button type="submit" class="btn btn-success btn-xs d-inline-block" value="buscar" name="BotonBuscar">Buscar</button>
                <button type="submit" class="btn btn-danger btn-xs d-inline-block" value="limpiar" name="BotonLimpiar">Limpiar</button>
                <button type="button" 
                        class="btn btn-primary btn-xs d-inline-block" 
                        data-bs-toggle="modal" 
                        data-bs-target="#reporteModal">
                    Generar Reporte
                </button>
              </div>    
              <div class="col-sm-5 mt-2">
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="Fecha" checked>
                      <label class="form-check-label" for="gridRadios1">
                        Fecha
                      </label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="Proveedor">
                      <label class="form-check-label" for="gridRadios2">
                        Proveedor
                      </label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="Id">
                      <label class="form-check-label" for="gridRadios3">
                        ID
                      </label>
                    </div>
              </div>
          </div>
          </form>

          <!-- Table with stripped rows -->
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Fecha</th>
                  <th scope="col">Proveedor</th>
                  <th scope="col">Usuario</th>
                  <th scope="col">Precio Total</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                for ($i = 0; $i < $CantidadOrdenes; $i++) { ?>
                  <tr>
                    <td><?php echo $ListadoOrdenes[$i]['ID_ORDEN']; ?></td>
                    <td><?php echo $ListadoOrdenes[$i]['FECHA']; ?></td>
                    <td><?php echo $ListadoOrdenes[$i]['PROVEEDOR']; ?></td>
                    <td><?php echo $ListadoOrdenes[$i]['USUARIO']; ?></td>
                    <td><?php echo number_format($ListadoOrdenes[$i]['PRECIO_TOTAL'], 2, ',', '.'); ?></td>
                    <td>
                      <a href="../eliminar/eliminar_orden_compra.php?ID_ORDEN=<?php echo $ListadoOrdenes[$i]['ID_ORDEN']; ?>" 
                        title="Anular" 
                        onclick="return confirm('Confirma anular esta orden de compra');">
                        <i class="bi bi-trash-fill text-danger fs-5"></i>
                      </a>
                      <a href="../modificar/modificar_orden_compra.php?ID_ORDEN=<?php echo $ListadoOrdenes[$i]['ID_ORDEN']; ?>" 
                        title="Modificar">
                        <i class="bi bi-pencil-fill text-warning fs-5"></i>
                      </a>
                      <a href="../descargas/descargar_orden_compraPDF.php?ID_ORDEN=<?php echo $ListadoOrdenes[$i]['ID_ORDEN']; ?>" 
                        title="Imprimir">
                        <i class="bi bi-printer-fill text-primary fs-5"></i>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <!-- End Table with stripped rows -->

        </div>
    </div>
</section>

<!-- Modal Reporte por Fechas -->
<div class="modal fade" id="reporteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generar Reporte por Fechas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../reportes/generar_reporte_ordenes.php" method="POST" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" 
                              class="form-control" 
                              name="fecha_inicio" 
                              id="fecha_inicio"
                              required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" 
                              class="form-control" 
                              name="fecha_fin" 
                              id="fecha_fin"
                              required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Generar PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

</main><!-- End #main -->

<?php
$_SESSION['Mensaje'] = '';
require('../footer.inc.php');
?>

<!-- Script para manejar las fechas en el modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const hoy = new Date().toISOString().split('T')[0];

    // Configuración inicial
    fechaInicio.max = hoy;
    fechaFin.disabled = true; // Deshabilitar fecha fin inicialmente
    fechaFin.placeholder = "Seleccione primero la fecha inicio";

    // Cuando cambia la fecha inicio
    fechaInicio.addEventListener('change', function() {
        if (this.value) {
            // Habilitar y configurar fecha fin
            fechaFin.disabled = false;
            fechaFin.min = this.value;
            fechaFin.max = hoy;
            
            // Si había un valor previo en fecha fin que ahora es inválido
            if (fechaFin.value && fechaFin.value < this.value) {
                fechaFin.value = this.value;
            }
        } else {
            // Si borran la fecha inicio
            fechaFin.disabled = true;
            fechaFin.value = '';
        }
    });

    // Validación fecha fin
    fechaFin.addEventListener('change', function() {
        if (this.value < fechaInicio.value) {
            this.value = fechaInicio.value;
        }
        if (this.value > hoy) {
            this.value = hoy;
        }
    });
});
</script>

</body>
</html>