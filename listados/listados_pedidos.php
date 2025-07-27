<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require('../encabezado.inc.php'); // Encabezado
require('../barraLateral.inc.php'); // Barra lateral

// Incluir la conexión a la base de datos
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

// Incluir las funciones necesarias para listar ventas
require_once '../funciones/select_general.php';

// Obtener el listado de ventas
$ListadoPedidos = Listar_Pedidos($MiConexion);
$CantidadPedidos = count($ListadoPedidos);

// Buscar ventas según un parámetro
if (!empty($_POST['BotonBuscar'])) {
    $parametro = $_POST['parametro'];
    $criterio = $_POST['gridRadios'];
    $ListadoPedidos = Listar_Pedidos_Parametro($MiConexion, $criterio, $parametro);
    $CantidadPedidos = count($ListadoPedidos);
}
?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Pedidos</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../inicio/index.php">Menú</a></li>
      <li class="breadcrumb-item">Pedidos</li>
      <li class="breadcrumb-item active">Listado Pedidos</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Pedidos</h5>
          <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
              <?php echo $_SESSION['Mensaje'] ?>
            </div>
          <?php } ?>

          <form method="POST">
          <div class="row mb-4">
            <label for="inputEmail3" class="col-sm-1 col-form-label">Buscar</label>
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
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="Cliente">
                      <label class="form-check-label" for="gridRadios2">
                      Cliente
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
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Fecha</th>
                  <th scope="col">Cliente</th>
                  <th scope="col">Vendedor</th>
                  <th scope="col">Total</th>
                  <th scope="col">%Desc.</th>
                  <th scope="col">Seña</th>
                  <th scope="col">Saldo</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                //BORRAR EL CONTENIDO ANTERIOR ANTES DE CARGAR NUEVO
                $_SESSION['Descarga'] = "";

                for ($i = 0; $i < $CantidadPedidos; $i++) { 
                  // Calcular el saldo
                  $montoDescuento = $ListadoPedidos[$i]['PRECIO_TOTAL'] * ($ListadoPedidos[$i]['DESCUENTO'] / 100);
                  $saldo = ($ListadoPedidos[$i]['PRECIO_TOTAL'] - $ListadoPedidos[$i]['SENIA']) - $montoDescuento;

                  // Método para descargar
                  $_SESSION['Descarga'] .= "ID Pedido: {$ListadoPedidos[$i]['ID_PEDIDO']}|" . 
                      "Fecha: {$ListadoPedidos[$i]['FECHA']}|" . 
                      "Cliente: {$ListadoPedidos[$i]['CLIENTE_N']}, {$ListadoPedidos[$i]['CLIENTE_A']}|" . 
                      "Vendedor: {$ListadoPedidos[$i]['VENDEDOR']}|" . 
                      "SubTotal: " . number_format($ListadoPedidos[$i]['PRECIO_TOTAL'], 2) . "|" .
                      "Descuento: {$ListadoPedidos[$i]['DESCUENTO']}%|" .
                      "Seña: " . number_format($ListadoPedidos[$i]['SENIA'], 2) . "|" .  // Añadido la seña
                      "Total: " . number_format($saldo, 2) . "\n";

                    // Color de fila según estado
                    list($Title, $Color) = ColorDeFilaPedidos( $ListadoPedidos[$i]['ID_ESTADO']);
                ?>

                  <tr class="<?php echo $Color; ?>" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php echo $Title; ?>">
                    <td><?php echo $ListadoPedidos[$i]['ID_PEDIDO']; ?></td>
                    <td><?php echo $ListadoPedidos[$i]['FECHA']; ?></td>
                    <td><?php echo $ListadoPedidos[$i]['CLIENTE_N']; ?>, <?php echo $ListadoPedidos[$i]['CLIENTE_A']; ?></td>
                    <td><?php echo $ListadoPedidos[$i]['VENDEDOR']; ?></td>
                    <td>$<?php echo number_format($ListadoPedidos[$i]['PRECIO_TOTAL'], 2); ?></td>
                    <td class="text-center">%<?php echo $ListadoPedidos[$i]['DESCUENTO']; ?></td>
                    <td>$<?php echo number_format($ListadoPedidos[$i]['SENIA'], 2); ?></td>
                    <td>$<?php echo number_format($saldo, 2); ?></td>
                    <td>
                        <!-- Acciones -->
                        <a href="../eliminar/eliminar_pedido.php?ID_PEDIDO=<?php echo $ListadoPedidos[$i]['ID_PEDIDO']; ?>" 
                            title="Anular" 
                            onclick="return confirm('Confirma anular este Pedido?');">
                            <i class="bi bi-trash-fill text-danger fs-5"></i>
                        </a>

                        <a href="../modificar/modificar_pedido.php?ID_PEDIDO=<?php echo $ListadoPedidos[$i]['ID_PEDIDO']; ?>" 
                            title="Modificar">
                            <i class="bi bi-pencil-fill text-warning fs-5"></i>
                        </a>

                        <a href="../descargas/descargar_comp_pedidoPDF.php?ID_PEDIDO=<?php echo $ListadoPedidos[$i]['ID_PEDIDO']; ?>" 
                            title="Imprimir">
                            <i class="bi bi-printer-fill text-primary fs-5"></i>
                        </a>

                        <?php if ($ListadoPedidos[$i]['ID_ESTADO'] != 3 && $ListadoPedidos[$i]['ID_ESTADO'] != 4): ?>
                            <a href="../acciones/retirar_pedido.php?ID_PEDIDO=<?php echo $ListadoPedidos[$i]['ID_PEDIDO']; ?>" 
                                title="Retirar" 
                                onclick="return confirm('¿Confirmas que el cliente retiró el pedido y pagó el saldo pendiente?');">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            </a>
                        <?php else: ?>
                            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" 
                                  title="<?php echo ($ListadoPedidos[$i]['ID_ESTADO'] == 3) ? 'Pedido ya finalizado' : 'Pedido cancelado'; ?>">
                                <i class="bi bi-check-circle-fill text-secondary fs-5" style="opacity: 0.5;"></i>
                            </span>
                        <?php endif; ?>
                    </td>
                  </tr>
                <?php 
                } 
                //le agrego un espacio cuando termino de cargar
                $_SESSION['Descarga'] .= "\n";
                ?>
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
            <form action="../reportes/generar_reporte_pedidos.php" method="POST" target="_blank">
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
require('../footer.inc.php'); // Footer
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const hoy = new Date().toISOString().split('T')[0];

    fechaInicio.max = hoy;
    fechaFin.disabled = true;
    fechaFin.placeholder = "Seleccione primero la fecha inicio";

    fechaInicio.addEventListener('change', function() {
        if (this.value) {
            fechaFin.disabled = false;
            fechaFin.min = this.value;
            fechaFin.max = hoy;

            if (fechaFin.value && fechaFin.value < this.value) {
                fechaFin.value = this.value;
            }
        } else {
            fechaFin.disabled = true;
            fechaFin.value = '';
        }
    });

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