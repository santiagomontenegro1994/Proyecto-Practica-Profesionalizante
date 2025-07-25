<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once ('../encabezado.inc.php');
require_once ('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

require_once '../funciones/select_general.php';

$Mensaje = '';
$Estilo = 'warning';
if (!empty($_POST['BotonRegistrar'])) {
    $Mensaje = Validar_Servicio();
    if (empty($Mensaje)) {
        if (InsertarServicio($MiConexion) != false) {
            $Mensaje = 'Se ha registrado el servicio correctamente.';
            $_POST = array();
            $Estilo = 'success';
        }
    }
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Servicios</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
                <li class="breadcrumb-item">Servicios</li>
                <li class="breadcrumb-item active">Agregar Servicio</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Agregar Servicio</h5>
                <form method='post'>
                    <?php if (!empty($Mensaje)) { ?>
                        <div class="alert alert-<?php echo $Estilo; ?> alert-dismissable">
                            <?php echo $Mensaje; ?>
                        </div>
                    <?php } ?>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Denominación</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="Denominacion" id="denominacion"
                                value="<?php echo !empty($_POST['Denominacion']) ? $_POST['Denominacion'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Precio</label>
                        <div class="col-sm-10">
                            <input type="number" step="0.01" class="form-control" name="Precio" id="precio"
                                value="<?php echo !empty($_POST['Precio']) ? $_POST['Precio'] : ''; ?>">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primario" value="Registrar" name="BotonRegistrar">Agregar</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
<?php require ('../footer.inc.php'); ?>
</body>
</html>