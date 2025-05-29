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
$ListadoVentas = Listar_Ventas($MiConexion);
$CantidadVentas = count($ListadoVentas);

// Buscar ventas según un parámetro
if (!empty($_POST['BotonBuscar'])) {
    $parametro = $_POST['parametro'];
    $criterio = $_POST['gridRadios'];
    $ListadoVentas = Listar_Ventas_Parametro($MiConexion, $criterio, $parametro);
    $CantidadVentas = count($ListadoVentas);
}
?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Ventas</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Menú</a></li>
      <li class="breadcrumb-item">Ventas</li>
      <li class="breadcrumb-item active">Listado Ventas</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Ventas</h5>
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
                <button type="button" class="btn btn-primary btn-xs d-inline-block" 
                  data-bs-toggle="modal" data-bs-target="#reporteVentasModal">
                  Reporte por Fechas
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
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Fecha</th>
                  <th scope="col">Cliente</th>
                  <th scope="col">Vendedor</th>
                  <th scope="col">SubTotal</th>
                  <th scope="col">%Desc.</th>
                  <th scope="col">Total</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody>
              <?php 
                //BORRAR EL CONTENIDO ANTERIOR ANTES DE CARGAR NUEVO
                $_SESSION['Descarga'] = "";

                for ($i = 0; $i < $CantidadVentas; $i++) { 
                  // Calcular el saldo
                  $montoDescuento = $ListadoVentas[$i]['PRECIO_TOTAL'] * ($ListadoVentas[$i]['DESCUENTO'] / 100);
                  $saldo = ($ListadoVentas[$i]['PRECIO_TOTAL']) - $montoDescuento;

                  //Metodo para descargar
                  $_SESSION['Descarga'] .= "Id Venta: {$ListadoVentas[$i]['ID_VENTA']}|" . 
                  "Fecha: {$ListadoVentas[$i]['FECHA']}|" . 
                  "Cliente: {$ListadoVentas[$i]['CLIENTE_N']}, {$ListadoVentas[$i]['CLIENTE_A']}|" . 
                  "Vendedor: {$ListadoVentas[$i]['VENDEDOR']}|" . 
                  "SubTotal: " . number_format($ListadoVentas[$i]['PRECIO_TOTAL'], 2) . "|" .
                  "Descuento: {$ListadoVentas[$i]['DESCUENTO']}%|" .
                  "Total: " . number_format($saldo, 2) . "\n";
                ?>
                
                  <tr>
                    <td><?php echo $ListadoVentas[$i]['ID_VENTA']; ?></td>
                    <td><?php echo $ListadoVentas[$i]['FECHA']; ?></td>
                    <td><?php echo $ListadoVentas[$i]['CLIENTE_N']; ?>, <?php echo $ListadoVentas[$i]['CLIENTE_A']; ?></td>
                    <td><?php echo $ListadoVentas[$i]['VENDEDOR']; ?></td>
                    <td>$<?php echo number_format($ListadoVentas[$i]['PRECIO_TOTAL'], 2); ?></td>
                    <td class="text-center">%<?php echo $ListadoVentas[$i]['DESCUENTO']; ?></td>
                    <td>$<?php echo number_format($saldo, 2); ?></td>
                    <td>
                      <!-- Acciones -->
                      <a href="../eliminar/eliminar_venta.php?ID_VENTA=<?php echo $ListadoVentas[$i]['ID_VENTA']; ?>" 
                        title="Anular" 
                        onclick="return confirm('Confirma anular esta Venta?');">
                        <i class="bi bi-trash-fill text-danger fs-5"></i>
                      </a>

                      <a href="../modificar/modificar_venta.php?ID_VENTA=<?php echo $ListadoVentas[$i]['ID_VENTA']; ?>" 
                        title="Modificar">
                        <i class="bi bi-eye-fill text-warning fs-5"></i>
                      </a>

                      <a href="../descargas/descargar_comp_ventaPDF.php?ID_VENTA=<?php echo $ListadoVentas[$i]['ID_VENTA']; ?>" 
                        title="Imprimir">
                        <i class="bi bi-printer-fill text-primary fs-5"></i>
                      </a>
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
<div class="modal fade" id="reporteVentasModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generar Reporte por Fechas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="../reportes/generar_reporte_ventas.php" method="POST" target="_blank">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Generar PDF</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inicio = document.getElementById('fecha_inicio');
  const fin = document.getElementById('fecha_fin');
  const hoy = new Date().toISOString().split('T')[0];
  inicio.max = hoy;
  fin.max = hoy;
  fin.disabled = true;

  inicio.addEventListener('change', () => {
    if (inicio.value) {
      fin.disabled = false;
      fin.min = inicio.value;
    } else {
      fin.disabled = true;
      fin.value = '';
    }
  });
});
</script>

</main><!-- End #main -->

<?php
$_SESSION['Mensaje'] = '';
require('../footer.inc.php'); // Footer
?>

</body>
</html>