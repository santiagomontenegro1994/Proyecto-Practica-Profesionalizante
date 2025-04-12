<?php
ob_start(); // Inicia el buffering de salida
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('../barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

require_once '../funciones/conexion.php';
$MiConexion=ConexionBD(); 

require_once '../funciones/select_general.php';
$ListadoTipos = Listar_Tipos($MiConexion);
$CantidadTipos = count($ListadoTipos);

$ListadoEstilistas = Listar_Estilistas($MiConexion);
$CantidadEstilistas = count($ListadoEstilistas);

$ListadoClientes = Listar_Clientes_Turnos($MiConexion);
$CantidadClientes = count($ListadoClientes);

$ListadoEstados = Listar_Estados_Turnos($MiConexion);
$CantidadEstados = count($ListadoEstados);
 

//este array contendra los datos de la consulta original, y cuando 
//pulse el boton, mantendrá los datos ingresados hasta que se validen y se puedan modificar
$DatosTurnoActual=array();

if (!empty($_POST['ModificarTurno'])) {
    Validar_Turno();

    if (empty($_SESSION['Mensaje'])) { //ya toque el boton modificar y el mensaje esta vacio...
        
        if (Modificar_Turno($MiConexion) != false) {
            $_SESSION['Mensaje'] = "Tu cliente se ha modificado correctamente!";
            $_SESSION['Estilo']='success';
            header('Location: ../listados/listados_turnos.php');
            exit;
        }

    }else {  //ya toque el boton modificar y el mensaje NO esta vacio...
        $_SESSION['Estilo']='warning';
        $DatosTurnoActual['ID_TURNO'] = !empty($_POST['IdTurno']) ? $_POST['IdTurno'] :'';
        $DatosTurnoActual['HORARIO'] = !empty($_POST['Horario']) ? $_POST['Horario'] :'';
        $DatosTurnoActual['FECHA'] = !empty($_POST['Fecha']) ? $_POST['Fecha'] :'';
        $DatosTurnoActual['TIPO_SERVICIO'] = !empty($_POST['TipoServicio']) ? $_POST['TipoServicio'] :'';
        $DatosTurnoActual['ESTILISTA'] = !empty($_POST['Estilista']) ? $_POST['Estilista'] :'';
        $DatosTurnoActual['CLIENTE'] = !empty($_POST['Cliente']) ? $_POST['Cliente'] :'';
        $DatosTurnoActual['ESTADO'] = !empty($_POST['Estado']) ? $_POST['Estado'] :'';
    }

}else if (!empty($_GET['ID_TURNO'])) {
    //verifico que traigo el nro de consulta por GET si todabia no toque el boton de Modificar
    //busco los datos de esta consulta y los muestro
    $DatosTurnoActual = Datos_Turno($MiConexion , $_GET['ID_TURNO']);
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

                    <div class="col-12">
                        <label for="hora" class="form-label">Horario</label>
                        <input type="time" class="form-control" name="Horario"
                        value="<?php echo !empty($DatosTurnoActual['HORARIO']) ? $DatosTurnoActual['HORARIO'] : ''; ?>">
                    </div>

                    <div class="col-12">
                        <label for="selector" class="form-label">Tipo de Servicio</label>
                        <select class="js-example-basic-single form-select" aria-label="Selector" multiple="multiple" name="TipoServicio[]">
                            <option value="">Selecciona una opción</option>
                            <?php
                            // Convertir el string de opciones seleccionadas en un array
                            $opcionesSeleccionadas = !empty($DatosTurnoActual['TIPO_SERVICIO']) ? explode(',', $DatosTurnoActual['TIPO_SERVICIO']) : [];

                            // Recorrer la lista de tipos de servicio
                            for ($i = 0; $i < $CantidadTipos; $i++) {
                                // Verificar si la opción actual está en el array de seleccionados
                                $selected = in_array($ListadoTipos[$i]['ID'], $opcionesSeleccionadas) ? 'selected' : '';
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

                        <button class="btn btn-primary" type="submit" value="Modificar" name="ModificarTurno">Modificar</button>
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
  $('.js-example-basic-single').select2();
  });
</script>

</body>

</html>