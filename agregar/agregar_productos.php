<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo
require ('../barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

require_once '../funciones/conexion.php';
$MiConexion = ConexionBD(); 

require_once '../funciones/select_general.php';

$Mensaje = '';
$Estilo = 'warning';

if (!empty($_POST['BotonRegistrar'])) {
    // Estoy en condiciones de poder validar los datos
    $Mensaje = Validar_Producto();
    if (empty($Mensaje)) {
        if (InsertarProductos($MiConexion) != false) {
            $Mensaje = 'El producto se ha registrado correctamente.';
            $_POST = array(); 
            $Estilo = 'success'; 
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Productos</title>
</head>
<body>

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Productos</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
          <li class="breadcrumb-item">Productos</li>
          <li class="breadcrumb-item active">Agregar Productos</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <section class="section">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Agregar Productos</h5>

          <!-- Horizontal Form -->
          <form method='post'>
            <?php if (!empty($Mensaje)) { ?>
                <div class="alert alert-<?php echo $Estilo; ?> alert-dismissible fade show">
                    <?php echo $Mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
            
            <div class="row mb-3">
              <label for="codigo" class="col-sm-2 col-form-label">Código</label>
              <div class="col-sm-10">
                <input type="number" class="form-control" name="Codigo" id="codigo" required
                value="<?php echo !empty($_POST['Codigo']) ? htmlspecialchars($_POST['Codigo']) : ''; ?>">
              </div>
            </div>
            
            <div class="row mb-3">
              <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="Nombre" id="nombre" required
                value="<?php echo !empty($_POST['Nombre']) ? htmlspecialchars($_POST['Nombre']) : ''; ?>">
              </div>
            </div>
            
            <div class="row mb-3">
              <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
              <div class="col-sm-10">
                <textarea class="form-control" name="Descripcion" id="descripcion"><?php echo !empty($_POST['Descripcion']) ? htmlspecialchars($_POST['Descripcion']) : ''; ?></textarea>
              </div>
            </div>
            
            <div class="row mb-3">
              <label for="precio" class="col-sm-2 col-form-label">Precio</label>
              <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" name="Precio" id="precio" required
                value="<?php echo !empty($_POST['Precio']) ? htmlspecialchars($_POST['Precio']) : ''; ?>">
              </div>
            </div>
            
            <div class="row mb-3">
              <label for="stock" class="col-sm-2 col-form-label">Stock</label>
              <div class="col-sm-10">
                <input type="number" class="form-control" name="Stock" id="stock" required
                value="<?php echo !empty($_POST['Stock']) ? htmlspecialchars($_POST['Stock']) : ''; ?>">
              </div>
            </div>
            
            <div class="text-center">
              <button type="submit" class="btn btn-primario" value="Registrar" name="BotonRegistrar">Agregar</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
          </form><!-- End Horizontal Form -->
        </div>
      </div>
    </section>

</main><!-- End #main -->

<?php
require ('../footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
?>

</body>
</html>