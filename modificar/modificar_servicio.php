<?php
ob_start();
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require ('../encabezado.inc.php');
require ('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();
require_once '../funciones/select_general.php';

// Este array contendrá los datos del servicio actual
$DatosServicioActual = array();

if (!empty($_POST['BotonModificarServicio'])) {
    $Mensaje = Validar_Servicio();

    if (empty($Mensaje)) {
        if (Modificar_Servicio($MiConexion) != false) {
            $_SESSION['Mensaje'] = "¡El servicio se ha modificado correctamente!";
            $_SESSION['Estilo'] = 'success';
            header('Location: ../listados/listados_servicios.php');
            exit;
        }
    } else {
        $_SESSION['Estilo'] = 'warning';
        $_SESSION['Mensaje'] = $Mensaje;
        $DatosServicioActual['ID'] = !empty($_POST['IdServicio']) ? $_POST['IdServicio'] : '';
        $DatosServicioActual['DENOMINACION'] = !empty($_POST['Denominacion']) ? $_POST['Denominacion'] : '';
        $DatosServicioActual['PRECIO'] = !empty($_POST['Precio']) ? $_POST['Precio'] : '';
    }
} else if (!empty($_GET['ID_SERVICIO'])) {
    // Si no se ha tocado el botón, traigo los datos del servicio por GET
    $DatosServicioActual = Datos_Servicio($MiConexion, $_GET['ID_SERVICIO']);
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Servicios</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
                <li class="breadcrumb-item">Servicios</li>
                <li class="breadcrumb-item active">Modificar Servicio</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Modificar Servicio</h5>
                <form method='post'>
                    <?php if (!empty($_SESSION['Mensaje'])) { ?>
                        <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                            <?php echo $_SESSION['Mensaje']; ?>
                        </div>
                    <?php } ?>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Denominación</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="Denominacion" id="denominacion"
                                   value="<?php echo !empty($DatosServicioActual['DENOMINACION']) ? $DatosServicioActual['DENOMINACION'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Precio</label>
                        <div class="col-sm-10">
                            <input type="number" step="0.01" class="form-control" name="Precio" id="precio"
                                   value="<?php echo !empty($DatosServicioActual['PRECIO']) ? $DatosServicioActual['PRECIO'] : ''; ?>">
                        </div>
                    </div>
                    <div class="text-center">
                        <input type='hidden' name="IdServicio" value="<?php echo $DatosServicioActual['ID']; ?>" />
                        <button type="submit" class="btn btn-primario" value="Modificar" name="BotonModificarServicio">Modificar</button>
                        <a href="../listados/listados_servicios.php" class="btn btn-success btn-info" title="Listado">Volver al listado</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
<?php
    $_SESSION['Mensaje'] = '';
    require ('../footer.inc.php');
    ob_end_flush();
?>
</body>
</html>