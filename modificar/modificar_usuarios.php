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

// Este array contendrá los datos del usuario actual
$DatosUsuarioActual = array();

if (!empty($_POST['BotonModificarUsuario'])) {
    $Mensaje = Validar_Usuario();

    if (empty($Mensaje)) {
        if (Modificar_Usuario($MiConexion) != false) {
            $_SESSION['Mensaje'] = "El usuario se ha modificado correctamente!";
            $_SESSION['Estilo'] = 'success';
            header('Location: ../listados/listados_usuarios.php');
            exit;
        }
    } else {
        $_SESSION['Estilo'] = 'warning';
        $_SESSION['Mensaje'] = $Mensaje;
        $DatosUsuarioActual['ID_USUARIO'] = !empty($_POST['IdUsuario']) ? $_POST['IdUsuario'] : '';
        $DatosUsuarioActual['NOMBRE'] = !empty($_POST['Nombre']) ? $_POST['Nombre'] : '';
        $DatosUsuarioActual['APELLIDO'] = !empty($_POST['Apellido']) ? $_POST['Apellido'] : '';
        $DatosUsuarioActual['USER'] = !empty($_POST['User']) ? $_POST['User'] : '';
        $DatosUsuarioActual['NIVEL'] = !empty($_POST['Nivel']) ? $_POST['Nivel'] : '';
    }
} else if (!empty($_GET['ID_USUARIO'])) {
    // Si no se ha tocado el botón, traigo los datos del usuario por GET
    $DatosUsuarioActual = Datos_Usuario($MiConexion, $_GET['ID_USUARIO']);
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Usuarios</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
                <li class="breadcrumb-item">Usuarios</li>
                <li class="breadcrumb-item active">Modificar Usuario</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Modificar Usuario</h5>
                <form method='post'>
                    <?php if (!empty($_SESSION['Mensaje'])) { ?>
                        <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                            <?php echo $_SESSION['Mensaje']; ?>
                        </div>
                    <?php } ?>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nombre</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="Nombre" id="nombre"
                                   value="<?php echo !empty($DatosUsuarioActual['NOMBRE']) ? $DatosUsuarioActual['NOMBRE'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Apellido</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="Apellido" id="apellido"
                                   value="<?php echo !empty($DatosUsuarioActual['APELLIDO']) ? $DatosUsuarioActual['APELLIDO'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Usuario</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="User" id="user"
                                   value="<?php echo !empty($DatosUsuarioActual['USER']) ? $DatosUsuarioActual['USER'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nivel</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="Nivel" id="nivel">
                                <option value="1" <?php echo (isset($DatosUsuarioActual['NIVEL']) && $DatosUsuarioActual['NIVEL'] == '1') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="2" <?php echo (isset($DatosUsuarioActual['NIVEL']) && $DatosUsuarioActual['NIVEL'] == '2') ? 'selected' : ''; ?>>Estilista</option>
                                <option value="3" <?php echo (isset($DatosUsuarioActual['NIVEL']) && $DatosUsuarioActual['NIVEL'] == '3') ? 'selected' : ''; ?>>Ventas</option>
                                <option value="4" <?php echo (isset($DatosUsuarioActual['NIVEL']) && $DatosUsuarioActual['NIVEL'] == '4') ? 'selected' : ''; ?>>Depósito</option>
                                <option value="5" <?php echo (isset($DatosUsuarioActual['NIVEL']) && $DatosUsuarioActual['NIVEL'] == '5') ? 'selected' : ''; ?>>Compras</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Clave (solo si desea cambiarla)</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="Clave" id="clave" value="">
                        </div>
                    </div>
                    <div class="text-center">
                        <input type='hidden' name="IdUsuario" value="<?php echo $DatosUsuarioActual['ID_USUARIO']; ?>" />
                        <button type="submit" class="btn btn-primary" value="Modificar" name="BotonModificarUsuario">Modificar</button>
                        <a href="../listados/listados_usuarios.php" class="btn btn-success btn-info" title="Listado">Volver al listado</a>
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