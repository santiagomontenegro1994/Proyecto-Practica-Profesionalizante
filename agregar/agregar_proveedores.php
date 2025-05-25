<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require_once ('../encabezado.inc.php');
require_once ('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion=ConexionBD(); 

$Mensaje='';
$Estilo='warning';
if (!empty($_POST['BotonRegistrar'])) {
    // Validar los datos del proveedor
    $Mensaje=Validar_Proveedor();
    if (empty($Mensaje)) {
        if (InsertarProveedor($MiConexion) != false) {
            $Mensaje = 'Se ha registrado correctamente.';
            $_POST = array(); 
            $Estilo = 'success'; 
        }
    }
}
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Proveedores</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
        <li class="breadcrumb-item">Proveedores</li>
        <li class="breadcrumb-item active">Agregar Proveedor</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Agregar Proveedor</h5>

            <!-- Horizontal Form -->
            <form method='post'>
              <?php if (!empty($Mensaje)) { ?>
                  <div class="alert alert-<?php echo $Estilo; ?> alert-dismissable">
                  <?php echo $Mensaje; ?>
                  </div>
              <?php } ?>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Razón Social</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="RazonSocial" id="razon_social"
                  value="<?php echo !empty($_POST['RazonSocial']) ? $_POST['RazonSocial'] : ''; ?>">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">CUIT</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" name="CUIT" id="cuit"
                  value="<?php echo !empty($_POST['CUIT']) ? $_POST['CUIT'] : ''; ?>">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Teléfono</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" name="Telefono" id="telefono"
                  value="<?php echo !empty($_POST['Telefono']) ? $_POST['Telefono'] : ''; ?>">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" name="Email" id="email"
                  value="<?php echo !empty($_POST['Email']) ? $_POST['Email'] : ''; ?>">
                </div>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary" value="Registrar" name="BotonRegistrar">Agregar</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
              </div>
            </form><!-- End Horizontal Form -->

  </section>

</main><!-- End #main -->

<?php
require ('../footer.inc.php');
?>

</body>
</html>