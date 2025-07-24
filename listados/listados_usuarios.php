<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) {
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php');
require ('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();
require_once '../funciones/select_general.php';

// Listado de usuarios
$ListadoUsuarios = Listar_Usuarios($MiConexion);
$CantidadUsuarios = count($ListadoUsuarios);

if (!empty($_POST['BotonBuscar'])) {
    $parametro = $_POST['parametro'];
    $criterio = $_POST['gridRadios'];
    $ListadoUsuarios = Listar_Usuarios_Parametro($MiConexion, $criterio, $parametro);
    $CantidadUsuarios = count($ListadoUsuarios);
}
?>

<main id="main" class="main">
<div class="pagetitle">
  <h1>Listado Usuarios</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
      <li class="breadcrumb-item">Usuarios</li>
      <li class="breadcrumb-item active">Listado Usuarios</li>
    </ol>
  </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Usuarios</h5>
          <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
              <?php echo $_SESSION['Mensaje'] ?>
            </div>
          <?php } ?>

          <form method="POST">
          <div class="row mb-4">
            <label class="col-sm-1 col-form-label">Buscar</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" name="parametro" id="parametro">
              </div>
              <div class="col-sm-3 mt-2">
                <button type="submit" class="btn btn-success btn-xs d-inline-block" value="buscar" name="BotonBuscar">Buscar</button>
                <button type="submit" class="btn btn-danger btn-xs d-inline-block" value="limpiar" name="BotonLimpiar">Limpiar</button>
              </div>
              <div class="col-sm-5 mt-2">
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="Nombre" checked>
                      <label class="form-check-label" for="gridRadios1">Nombre</label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="Apellido">
                      <label class="form-check-label" for="gridRadios2">Apellido</label>
                    </div>
                    <div class="form-check form-check-inline small-text">
                      <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="Usuario">
                      <label class="form-check-label" for="gridRadios3">Usuario</label>
                    </div>
                  </div>
          </div>
          </form>
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido</th>
                <th scope="col">Usuario</th>
                <th scope="col">Nivel</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<$CantidadUsuarios; $i++) { ?>
                    <tr>
                        <th scope="row"><?php echo $i+1; ?></th>
                        <td><?php echo $ListadoUsuarios[$i]['NOMBRE']; ?></td>
                        <td><?php echo $ListadoUsuarios[$i]['APELLIDO']; ?></td>
                        <td><?php echo $ListadoUsuarios[$i]['USER']; ?></td>
                        <td><?php echo $ListadoUsuarios[$i]['NIVEL']; ?></td>
                        <td>
                          <a href="../eliminar/eliminar_usuarios.php?ID_USUARIO=<?php echo $ListadoUsuarios[$i]['ID_USUARIO']; ?>" 
                            title="Eliminar" 
                            onclick="return confirm('Confirma eliminar este usuario?');">
                              <i class="bi bi-trash-fill text-danger fs-5"></i>
                          </a>
                          <a href="../modificar/modificar_usuarios.php?ID_USUARIO=<?php echo $ListadoUsuarios[$i]['ID_USUARIO']; ?>" 
                            title="Modificar">
                          <i class="bi bi-pencil-fill text-warning fs-5"></i>
                          </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
          </table>
        </div>
    </div>
</section>
</main>
<?php
  $_SESSION['Mensaje']='';
  require ('../footer.inc.php');
?>
</body>
</html>