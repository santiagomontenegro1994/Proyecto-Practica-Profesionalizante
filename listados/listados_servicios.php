<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) {
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php');
require ('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion = ConexionBD();

// Listar todos los servicios por defecto
$ListadoServicios = Listar_Servicios($MiConexion);
$CantidadServicios = count($ListadoServicios);

// Buscar según parámetro
if (!empty($_POST['BotonBuscar'])) {
    $parametro = $_POST['parametro'];
    $criterio = $_POST['gridRadios'];
    $ListadoServicios = Listar_Servicios_Parametro($MiConexion, $criterio, $parametro);
    $CantidadServicios = count($ListadoServicios);
}
?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Servicios</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
      <li class="breadcrumb-item">Servicios</li>
      <li class="breadcrumb-item active">Listado Servicios</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Servicios</h5>
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
              </div>
              <div class="col-sm-5 mt-2">
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="Denominacion" checked>
                      <label class="form-check-label" for="gridRadios1">
                        Denominación
                      </label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="Precio">
                      <label class="form-check-label" for="gridRadios2">
                        Precio
                      </label>
                    </div>
                  </div>
          </div>
          </form>
          <!-- Table with stripped rows -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Denominación</th>
                <th scope="col">Precio</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<$CantidadServicios; $i++) { ?>
                    <tr>
                        <th scope="row"><?php echo $i+1; ?></th>
                        <td><?php echo $ListadoServicios[$i]['DENOMINACION']; ?></td>
                        <td><?php echo $ListadoServicios[$i]['PRECIO']; ?></td>
                        <td>
                          <a href="../eliminar/eliminar_servicio.php?ID_SERVICIO=<?php echo $ListadoServicios[$i]['ID']; ?>" 
                            title="Eliminar" 
                            onclick="return confirm('Confirma eliminar este servicio?');">
                              <i class="bi bi-trash-fill text-danger fs-5"></i>
                          </a>
                          <a href="../modificar/modificar_servicio.php?ID_SERVICIO=<?php echo $ListadoServicios[$i]['ID']; ?>" 
                            title="Modificar">
                            <i class="bi bi-pencil-fill text-warning fs-5"></i>
                          </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
          </table>
          <!-- End Table with stripped rows -->

        </div>
    </div>
 
</section>

</main><!-- End #main -->

<?php
  $_SESSION['Mensaje']='';
  require ('../footer.inc.php');
?>

</body>
</html>