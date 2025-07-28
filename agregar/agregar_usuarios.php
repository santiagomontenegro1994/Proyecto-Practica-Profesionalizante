<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once('../encabezado.inc.php');
require_once('../barraLateral.inc.php');
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

require_once '../funciones/select_general.php';

$Mensaje = '';
$Estilo = 'warning';
if (!empty($_POST['BotonRegistrar'])) {
    $Mensaje = Validar_Usuario();
    if (empty($Mensaje)) {
        if (InsertarUsuario($MiConexion) != false) {
            $Mensaje = 'Se ha registrado correctamente.';
            $_POST = array();
            $Estilo = 'success';
        }
    }
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Usuarios</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
                <li class="breadcrumb-item">Usuarios</li>
                <li class="breadcrumb-item active">Agregar Usuario</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Agregar Usuario</h5>
                <form method='post'>
                    <?php if (!empty($Mensaje)) { ?>
                        <div class="alert alert-<?php echo $Estilo; ?> alert-dismissable">
                            <?php echo $Mensaje; ?>
                        </div>
                    <?php } ?>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nombre</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="Nombre" id="nombre"
                                   value="<?php echo !empty($_POST['Nombre']) ? $_POST['Nombre'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Apellido</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="Apellido" id="apellido"
                                   value="<?php echo !empty($_POST['Apellido']) ? $_POST['Apellido'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Usuario</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="User" id="user"
                                   value="<?php echo !empty($_POST['User']) ? $_POST['User'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Clave</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="Clave" id="clave"
                                   value="<?php echo !empty($_POST['Clave']) ? $_POST['Clave'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nivel</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="Nivel" id="nivel">
                                <option value="1" <?php echo (isset($_POST['Nivel']) && $_POST['Nivel'] == '1') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="2" <?php echo (isset($_POST['Nivel']) && $_POST['Nivel'] == '2') ? 'selected' : ''; ?>>Estilista</option>
                                <option value="3" <?php echo (isset($_POST['Nivel']) && $_POST['Nivel'] == '3') ? 'selected' : ''; ?>>Ventas</option>
                                <option value="4" <?php echo (isset($_POST['Nivel']) && $_POST['Nivel'] == '4') ? 'selected' : ''; ?>>Depósito</option>
                                <option value="5" <?php echo (isset($_POST['Nivel']) && $_POST['Nivel'] == '5') ? 'selected' : ''; ?>>Compras</option>
                                <option value="6" <?php echo (isset($_POST['Nivel']) && $_POST['Nivel'] == '6') ? 'selected' : ''; ?>>Recepcion</option>
                            </select>
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
<?php require('../footer.inc.php'); ?>
</body>
</html>