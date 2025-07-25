<?php
ob_start();
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php');
require ('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
$MiConexion=ConexionBD();

// Llamar script gral para usar las funciones necesarias
require_once '../funciones/select_general.php';

// Este array contendrá los datos de la consulta original, y cuando 
// pulse el botón, mantendrá los datos ingresados hasta que se validen y se puedan modificar
$DatosProveedorActual = array();

if (!empty($_POST['BotonModificarProveedor'])) {
    Validar_Proveedor();

    if (empty($_SESSION['Mensaje'])) { //ya toque el boton modificar y el mensaje esta vacio...
        if (Modificar_Proveedor($MiConexion) != false) {
            $_SESSION['Mensaje'] = "¡El proveedor se ha modificado correctamente!";
            $_SESSION['Estilo']='success';
            header('Location: ../listados/listados_proveedores.php');
            exit;
        }
    } else {  //ya toque el boton modificar y el mensaje NO esta vacio...
        $_SESSION['Estilo']='warning';
        $DatosProveedorActual['ID_PROVEEDOR'] = !empty($_POST['IdProveedor']) ? $_POST['IdProveedor'] :'';
        $DatosProveedorActual['RAZON_SOCIAL'] = !empty($_POST['RazonSocial']) ? $_POST['RazonSocial'] :'';
        $DatosProveedorActual['CUIT'] = !empty($_POST['CUIT']) ? $_POST['CUIT'] :'';
        $DatosProveedorActual['TELEFONO'] = !empty($_POST['Telefono']) ? $_POST['Telefono'] :'';
        $DatosProveedorActual['EMAIL'] = !empty($_POST['Email']) ? $_POST['Email'] :'';
    }

} else if (!empty($_GET['ID_PROVEEDOR'])) {
    //verifico que traigo el nro de consulta por GET si todavia no toque el boton de Modificar
    //busco los datos de este proveedor y los muestro
    $DatosProveedorActual = Datos_Proveedor($MiConexion , $_GET['ID_PROVEEDOR']);
}

?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Proveedores</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
          <li class="breadcrumb-item">Proveedores</li>
          <li class="breadcrumb-item active">Modificar Proveedor</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Modificar Proveedor</h5>

              <!-- Horizontal Form -->
                <form method='post'>
                <?php if (!empty($_SESSION['Mensaje'])) { ?>
                    <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                        <?php echo $_SESSION['Mensaje']; ?>
                    </div>
                <?php } ?>

                <div class="row mb-3">
                  <label for="razon_social" class="col-sm-2 col-form-label">Razón Social</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="RazonSocial" id="razon_social"
                    value="<?php echo !empty($DatosProveedorActual['RAZON_SOCIAL']) ? $DatosProveedorActual['RAZON_SOCIAL'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="cuit" class="col-sm-2 col-form-label">CUIT</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="CUIT" id="cuit"
                    value="<?php echo !empty($DatosProveedorActual['CUIT']) ? $DatosProveedorActual['CUIT'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="Telefono" id="telefono"
                    value="<?php echo !empty($DatosProveedorActual['TELEFONO']) ? $DatosProveedorActual['TELEFONO'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="email" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="email" class="form-control" name="Email" id="email"
                    value="<?php echo !empty($DatosProveedorActual['EMAIL']) ? $DatosProveedorActual['EMAIL'] : ''; ?>">
                  </div>
                </div>

                <div class="text-center">
                    <input type='hidden' name="IdProveedor" value="<?php echo $DatosProveedorActual['ID_PROVEEDOR']; ?>" />
                    <button type="submit" class="btn btn-primario" value="Modificar" name="BotonModificarProveedor">Modificar</button>
                    <a href="../listados/listados_proveedores.php" 
                    class="btn btn-success btn-info " 
                    title="Listado"> Volver al listado  </a>
                </div>
              </form><!-- End Horizontal Form -->

    </section>

  </main><!-- End #main -->

<?php
    $_SESSION['Mensaje']='';
    require ('../footer.inc.php');
    ob_end_flush();
?>

</body>
</html>