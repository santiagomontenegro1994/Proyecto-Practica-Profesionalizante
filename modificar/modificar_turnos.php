<?php
ob_start(); // Inicia el buffering de salida
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require('../encabezado.inc.php');
require('../barraLateral.inc.php');

require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

require_once '../funciones/select_general.php';
$ListadoTipos = Listar_Tipos($MiConexion);
$CantidadTipos = count($ListadoTipos);

$ListadoEstilistas = Listar_Estilistas($MiConexion);
$CantidadEstilistas = count($ListadoEstilistas);

$ListadoClientes = Listar_Clientes_Turnos($MiConexion);
$CantidadClientes = count($ListadoClientes);

$ListadoEstados = Listar_Estados_Turnos($MiConexion);
$CantidadEstados = count($ListadoEstados);

$DatosTurnoActual = array();

if (!empty($_POST['ModificarTurno'])) {
    // Limpiar mensaje anterior
    $_SESSION['Mensaje'] = '';
    $_SESSION['Estilo'] = 'warning';
    
    // Validar el turno
    $valido = Validar_Turno_Modificar($MiConexion);
    
    if ($valido) {        
        if (Modificar_Turno($MiConexion)) {
            $_SESSION['Mensaje'] = "El turno se ha modificado correctamente!";
            $_SESSION['Estilo'] = 'success';
            ob_end_clean(); // Limpiar buffer antes de redirigir
            header('Location: ../listados/listados_turnos.php');
            exit;
        }
    }
    
    // Si hay errores, guardar los datos del formulario
    $DatosTurnoActual = [
        'ID_TURNO' => $_POST['IdTurno'] ?? '',
        'HORARIO' => $_POST['Horario'] ?? '',
        'FECHA' => $_POST['Fecha'] ?? '',
        'TIPO_SERVICIO' => $_POST['TipoServicio'] ?? [],
        'ESTILISTA' => $_POST['Estilista'] ?? '',
        'CLIENTE' => $_POST['Cliente'] ?? '',
        'ESTADO' => $_POST['Estado'] ?? ''
    ];
    
    // Convertir servicios en array para select múltiple
    $serviciosSeleccionados = is_array($DatosTurnoActual['TIPO_SERVICIO']) ? 
                            $DatosTurnoActual['TIPO_SERVICIO'] : 
                            explode(',', $DatosTurnoActual['TIPO_SERVICIO']);

} elseif (!empty($_GET['ID_TURNO'])) {
    $DatosTurnoActual = Datos_Turno($MiConexion, $_GET['ID_TURNO']);
    $serviciosSeleccionados = !empty($DatosTurnoActual['servicios_seleccionados']) ? 
                            explode(',', $DatosTurnoActual['servicios_seleccionados']) : 
                            [];
}
?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Turnos</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
          <li class="breadcrumb-item">Turnos</li>
          <li class="breadcrumb-item active">Modificar Turnos</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Modificar Turnos</h5>

              <!-- Horizontal Form -->
              <form class="row g-3" id='miFormulario' method='post'>
              <?php if (!empty($_SESSION['Mensaje'])) { ?>
                <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                    <?php echo $_SESSION['Mensaje']; ?>
                </div>
              <?php } ?>

                    <div class="col-12">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control"  name="Fecha" id="fecha"
                        value="<?php echo !empty($DatosTurnoActual['FECHA']) ? $DatosTurnoActual['FECHA'] : ''; ?>">
                    </div>

                    <!-- Campo de Horario -->
                    <div class="col-12">
                        <label for="horario" class="form-label">Horario</label>
                        <select class="form-select" id="horario" name="Horario">
                            <option value="">Seleccione un horario</option>
                            <?php
                            // Definimos los horarios disponibles
                            $horarios = [
                                '08:00:00' => '8:00',
                                '08:30:00' => '8:30',
                                '09:00:00' => '9:00',
                                '09:30:00' => '9:30',
                                '10:00:00' => '10:00',
                                '10:30:00' => '10:30',
                                '11:00:00' => '11:00',
                                '11:30:00' => '11:30',
                                '12:00:00' => '12:00',
                                '12:30:00' => '12:30',
                                '16:00:00' => '16:00',
                                '16:30:00' => '16:30',
                                '17:00:00' => '17:00',
                                '17:30:00' => '17:30',
                                '18:00:00' => '18:00',
                                '18:30:00' => '18:30',
                                '19:00:00' => '19:00',
                                '19:30:00' => '19:30'
                            ];
                            
                            // Obtenemos el horario actual del turno (en formato HH:MM:SS)
                            $horaActual = !empty($DatosTurnoActual['HORARIO']) ? $DatosTurnoActual['HORARIO'] : '';
                            
                            // Si el horario viene sin segundos (por algún formato previo), lo normalizamos
                            if (!empty($horaActual) && strlen($horaActual) == 5) {
                                $horaActual .= ':00';
                            }
                            
                            // Generamos las opciones del select
                            foreach ($horarios as $valor => $texto) {
                                $selected = ($horaActual == $valor) ? 'selected' : '';
                                echo "<option value=\"$valor\" $selected>$texto</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="selector" class="form-label">Tipo de Servicio</label>
                        <select class="js-example-basic-multiple form-select" multiple="multiple" name="TipoServicio[]">
                            <?php for ($i = 0; $i < $CantidadTipos; $i++) { 
                                $selected = in_array($ListadoTipos[$i]['ID'], $serviciosSeleccionados) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $ListadoTipos[$i]['ID']; ?>" <?php echo $selected; ?>>
                                    <?php echo $ListadoTipos[$i]['DENOMINACION']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label for="selector" class="form-label">Estilista</label>
                        <select class="form-select" aria-label="Selector"  name="Estilista">
                            <option value="">Selecciona una opcion</option>
                            <?php 
                                $Selected='';
                                for ($i=0; $i<$CantidadEstilistas; $i++) { 
                                    $Selected = (!empty($DatosTurnoActual['ESTILISTA']) && $DatosTurnoActual['ESTILISTA'] == $ListadoEstilistas[$i]['ID'] )?'selected':'';
                            ?>
                            <option value="<?php echo $ListadoEstilistas[$i]['ID']; ?>"   <?php echo $Selected; ?> >
                            <?php echo $ListadoEstilistas[$i]['APELLIDO']; ?> , 
                            <?php echo $ListadoEstilistas[$i]['NOMBRE']; ?>
                        </option>
                        <?php } ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="selector" class="form-label">Cliente</label>
                        <select class="form-select" aria-label="Selector"  name="Cliente">
                            <option value="">Selecciona una opcion</option>
                            <?php 
                                $Selected='';
                                for ($i=0; $i<$CantidadClientes; $i++) { 
                                    $Selected = (!empty($DatosTurnoActual['CLIENTE']) && $DatosTurnoActual['CLIENTE'] == $ListadoClientes[$i]['ID'] )?'selected':'';
                            ?>
                            <option value="<?php echo $ListadoClientes[$i]['ID']; ?>"   <?php echo $Selected; ?> >
                            <?php echo $ListadoClientes[$i]['APELLIDO']; ?> , 
                            <?php echo $ListadoClientes[$i]['NOMBRE']; ?>
                        </option>
                        <?php } ?>

                        </select>
                    </div>

                    <div class="col-12">
                        <label for="selector" class="form-label">Estado</label>
                        <select class="form-select" aria-label="Selector"  name="Estado">
                            <option value="">Selecciona una opcion</option>
                            <?php 
                                $Selected='';
                                for ($i=0; $i<$CantidadEstados; $i++) { 
                                    $Selected = (!empty($DatosTurnoActual['ESTADO']) && $DatosTurnoActual['ESTADO'] == $ListadoEstados[$i]['ID'] )?'selected':'';
                            ?>
                            <option value="<?php echo $ListadoEstados[$i]['ID']; ?>"   <?php echo $Selected; ?> >
                            <?php echo $ListadoEstados[$i]['DENOMINACION']; ?> 
                        </option>
                        <?php } ?>

                        </select>
                    </div>

                    <div class="text-center">

                        <input type='hidden' name="IdTurno" value="<?php echo $DatosTurnoActual['ID_TURNO']; ?>" />

                        <button class="btn btn-primario" type="submit" value="Modificar" name="ModificarTurno">Modificar</button>
                        <a href="../listados/listados_turnos.php" 
                        class="btn btn-success btn-info " 
                        title="Listado"> Volver al listado  </a>
                    </div>
                </form>
                <!-- Vertical Form --><!-- End Horizontal Form -->

    </section>

  </main><!-- End #main -->

  <?php
  $_SESSION['Mensaje']='';
require ('../footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo

ob_end_flush(); // Envía la salida al navegador
?>

<script>
  // In your Javascript (external .js resource or <script> tag) SELECT 2
  $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            placeholder: "Seleccione los servicios",
            allowClear: true
        });
    });
</script>

</body>

</html>