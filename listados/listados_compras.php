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

// Obtener el listado de compras
$ListadoCompras = Listar_Compras($MiConexion);
$CantidadCompras = count($ListadoCompras);

// Buscar compras según un parámetro
if (!empty($_POST['BotonBuscar'])) {
    $parametro = $_POST['parametro'];
    $criterio = $_POST['gridRadios'];
    $ListadoCompras = Listar_Compras_Parametro($MiConexion, $criterio, $parametro);
    $CantidadCompras = count($ListadoCompras);
}
?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Presupuestos</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../inicio/index.php">Menú</a></li>
      <li class="breadcrumb-item">Compras</li>
      <li class="breadcrumb-item active">Listado Presupuestos</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Presupuestos</h5>
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
                <a href="../descargas/descargar_comprasPDF.php" 
                  class="btn btn-primary btn-xs d-inline-block " 
                  title="PDF"> Descargar </a>
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
                  <th scope="col">Descripción</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                for ($i = 0; $i < $CantidadCompras; $i++) { ?>
                  <tr>
                    <td><?php echo $ListadoCompras[$i]['ID_COMPRA']; ?></td>
                    <td><?php echo $ListadoCompras[$i]['FECHA']; ?></td>
                    <td><?php echo $ListadoCompras[$i]['PROVEEDOR']; ?></td>
                    <td><?php echo $ListadoCompras[$i]['USUARIO']; ?></td>
                    <td><?php echo $ListadoCompras[$i]['DESCRIPCION']; ?></td>
                    <td>
                      <a href="../eliminar/eliminar_compra.php?ID_COMPRA=<?php echo $ListadoCompras[$i]['ID_COMPRA']; ?>" 
                        title="Anular" 
                        onclick="return confirm('Confirma anular esta orden de compra');">
                        <i class="bi bi-trash-fill text-danger fs-5"></i>
                      </a>
                      <a href="../modificar/modificar_compra.php?ID_COMPRA=<?php echo $ListadoCompras[$i]['ID_COMPRA']; ?>" 
                        title="Modificar">
                        <i class="bi bi-pencil-fill text-warning fs-5"></i>
                      </a>
                      <a href="../descargas/descargar_comp_compraPDF.php?ID_COMPRA=<?php echo $ListadoCompras[$i]['ID_COMPRA']; ?>" 
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

</main><!-- End #main -->

<?php
$_SESSION['Mensaje'] = '';
require('../footer.inc.php');
?>

</body>
</html>