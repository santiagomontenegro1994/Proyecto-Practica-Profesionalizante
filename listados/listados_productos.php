<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('../barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

//voy a necesitar la conexion: incluyo la funcion de Conexion.
require_once '../funciones/conexion.php';

//genero una variable para usar mi conexion desde donde me haga falta
//no envio parametros porque ya los tiene definidos por defecto
$MiConexion = ConexionBD();

//ahora voy a llamar el script con la funcion que genera mi listado
require_once '../funciones/select_general.php';

//voy a ir listando lo necesario para trabajar en este script: 
$ListadoProductos = Listar_Productos($MiConexion);
$CantidadProductos = count($ListadoProductos);

//estoy en condiciones de poder buscar segun el parametro
if (!empty($_POST['BotonBuscar'])) {
    $parametro = $_POST['parametro'];
    $criterio = $_POST['gridRadios'];
    $ListadoProductos = Listar_Productos_Parametro($MiConexion, $criterio, $parametro);
    $CantidadProductos = count($ListadoProductos);
}
?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Productos</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Menu</a></li>
      <li class="breadcrumb-item">Productos</li>
      <li class="breadcrumb-item active">Listado Productos</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Productos</h5>
          <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
              <?php echo $_SESSION['Mensaje'] ?>
            </div>
          <?php } ?>

          <form method="POST">
          <div class="row mb-4">
            <label for="parametro" class="col-sm-1 col-form-label">Buscar</label>
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
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="Nombre" checked>
                      <label class="form-check-label" for="gridRadios1">
                        Nombre
                      </label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="Descripcion">
                      <label class="form-check-label" for="gridRadios2">
                        Descripción
                      </label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="Precio">
                      <label class="form-check-label" for="gridRadios3">
                        Precio
                      </label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios4" value="Stock">
                      <label class="form-check-label" for="gridRadios4">
                        Stock
                      </label>
                    </div>
                  </div>
          </div>
          </form>

          <!-- Report Buttons -->
          <div class="row mb-4">
            <div class="col-sm-12 text-end">
              <a href="../descargas/listado_faltantes_pdf.php" class="btn btn-primary btn-xs">Listado de productos faltantes</a>
            </div>
          </div>
          <!-- End Report Buttons -->

          <!-- Table with stripped rows -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Descripción</th>
                <th scope="col">Precio</th>
                <th scope="col">Stock</th>
                <th scope="col">Fecha Registro</th>
                <th scope="col">Activo</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < $CantidadProductos; $i++) { ?>
                    <tr>
                        <th scope="row"><?php echo $i + 1; ?></th>
                        <td><?php echo $ListadoProductos[$i]['NOMBRE']; ?></td>
                        <td><?php echo $ListadoProductos[$i]['DESCRIPCION']; ?></td>
                        <td><?php echo $ListadoProductos[$i]['PRECIO']; ?></td>
                        <td><?php echo $ListadoProductos[$i]['STOCK']; ?></td>
                        <td><?php echo $ListadoProductos[$i]['FECHA_REGISTRO']; ?></td>
                        <td><?php echo $ListadoProductos[$i]['ACTIVO'] ? 'Sí' : 'No'; ?></td>
                        <td>
                          <a href="../eliminar/eliminar_productos.php?ID_PRODUCTO=<?php echo $ListadoProductos[$i]['ID_PRODUCTO']; ?>" 
                            title="Eliminar" 
                            onclick="return confirm('Confirma eliminar este producto?');">
                            <i class="bi bi-trash-fill text-danger fs-5"></i>
                          </a>

                          <a href="../modificar/modificar_productos.php?ID_PRODUCTO=<?php echo $ListadoProductos[$i]['ID_PRODUCTO']; ?>" 
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
  $_SESSION['Mensaje'] = '';
  require ('../footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
?>

</body>

</html>