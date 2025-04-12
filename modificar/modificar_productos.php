<?php
ob_start();
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require_once '../funciones/conexion.php';
$MiConexion=ConexionBD();

//ahora voy a llamar el script gral para usar las funciones necesarias
require_once '../funciones/select_general.php';
 
//este array contendra los datos de la consulta original, y cuando 
//pulse el boton, mantendrá los datos ingresados hasta que se validen y se puedan modificar
$DatosProductoActual=array();

if (!empty($_POST['BotonModificarProducto'])) {
    $mensajeValidacion = Validar_Producto(); // Captura el mensaje de validación

    if (empty($mensajeValidacion)) { // Si no hay errores, procede con la modificación
        if (Modificar_Producto($MiConexion) != false) {
            $_SESSION['Mensaje'] = "El producto se ha modificado correctamente!";
            $_SESSION['Estilo'] = 'success';
            header('Location: ../listados/listados_productos.php');
            exit;
        } else {
            $_SESSION['Mensaje'] = "Hubo un error al intentar modificar el producto.";
            $_SESSION['Estilo'] = 'danger';
        }
    } else { // Si hay errores, muestra el mensaje y no modifica
        $_SESSION['Mensaje'] = $mensajeValidacion;
        $_SESSION['Estilo'] = 'warning';
        $DatosProductoActual['ID_PRODUCTO'] = !empty($_POST['IdProducto']) ? $_POST['IdProducto'] : '';
        $DatosProductoActual['NOMBRE'] = !empty($_POST['Nombre']) ? $_POST['Nombre'] : '';
        $DatosProductoActual['DESCRIPCION'] = !empty($_POST['Descripcion']) ? $_POST['Descripcion'] : '';
        $DatosProductoActual['PRECIO'] = !empty($_POST['Precio']) ? $_POST['Precio'] : '';
        $DatosProductoActual['STOCK'] = !empty($_POST['Stock']) ? $_POST['Stock'] : '';
        $DatosProductoActual['ACTIVO'] = isset($_POST['Activo']) ? $_POST['Activo'] : '';
    }
} else if (!empty($_GET['ID_PRODUCTO'])) {
    //verifico que traigo el nro de consulta por GET si todabia no toque el boton de Modificar
    //busco los datos de esta consulta y los muestro
    $DatosProductoActual = Datos_Producto($MiConexion , $_GET['ID_PRODUCTO']);
}

?>

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Productos</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Menu</a></li>
          <li class="breadcrumb-item">Productos</li>
          <li class="breadcrumb-item active">Modificar Productos</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Modificar Productos</h5>

              <!-- Horizontal Form -->
                <form method='post'>
                <?php if (!empty($_SESSION['Mensaje'])) { ?>
                    <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                        <?php echo $_SESSION['Mensaje']; ?>
                    </div>
                <?php } ?>

                <div class="row mb-3">
                  <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="Nombre" id="nombre"
                    value="<?php echo !empty($DatosProductoActual['NOMBRE']) ? $DatosProductoActual['NOMBRE'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="Descripcion" id="descripcion"><?php echo !empty($DatosProductoActual['DESCRIPCION']) ? $DatosProductoActual['DESCRIPCION'] : ''; ?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="precio" class="col-sm-2 col-form-label">Precio</label>
                  <div class="col-sm-10">
                    <input type="number" step="0.01" class="form-control" name="Precio" id="precio"
                    value="<?php echo !empty($DatosProductoActual['PRECIO']) ? $DatosProductoActual['PRECIO'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="stock" class="col-sm-2 col-form-label">Stock</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="Stock" id="stock"
                    value="<?php echo !empty($DatosProductoActual['STOCK']) ? $DatosProductoActual['STOCK'] : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="activo" class="col-sm-2 col-form-label">Activo</label>
                  <div class="col-sm-10">
                    <select class="form-control" name="Activo" id="activo">
                      <option value="1" <?php echo (isset($DatosProductoActual['ACTIVO']) && $DatosProductoActual['ACTIVO'] == '1') ? 'selected' : ''; ?>>Sí</option>
                      <option value="0" <?php echo (isset($DatosProductoActual['ACTIVO']) && $DatosProductoActual['ACTIVO'] == '0') ? 'selected' : ''; ?>>No</option>
                    </select>
                  </div>
                </div>

                <div class="text-center">
                    <input type='hidden' name="IdProducto" value="<?php echo $DatosProductoActual['ID_PRODUCTO']; ?>" />
                    <button type="submit" class="btn btn-primary" value="Modificar" name="BotonModificarProducto">Modificar</button>
                    <a href="../listados/listados_productos.php" 
                    class="btn btn-success btn-info " 
                    title="Listado"> Volver al listado  </a>
                </div>
              </form><!-- End Horizontal Form -->

    </section>

</main><!-- End #main -->

<?php
    $_SESSION['Mensaje']='';
    require ('footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
    ob_end_flush();
?>

</body>

</html>