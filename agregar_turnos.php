<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: cerrarsesion.php');
  exit;
}

require ('encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

require_once 'funciones/conexion.php';
$MiConexion=ConexionBD(); 

require_once 'funciones/select_general.php';
$ListadoTipos = Listar_Tipos($MiConexion);
$CantidadTipos = count($ListadoTipos);

$ListadoEstilistas = Listar_Estilistas($MiConexion);
$CantidadEstilistas = count($ListadoEstilistas);

$ListadoClientes = Listar_Clientes_Turnos($MiConexion);
$CantidadClientes = count($ListadoClientes);
 
require_once 'funciones/insertar_clientes.php';

$_SESSION['Estilo'] = 'alert';

if (!empty($_POST['Registrar'])) {
    //estoy en condiciones de poder validar los datos
    $_SESSION['Mensaje']=Validar_Turno();
    if (empty($_SESSION['Mensaje'])) {
        if (InsertarTurnos($MiConexion) != false) {
            $_SESSION['Mensaje'] = 'Se ha registrado correctamente.';
            $_POST = array(); 
            $_SESSION['Estilo'] = 'success'; 
        }
    }
}

?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Turnos</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Menu</a></li>
          <li class="breadcrumb-item">Turnos</li>
          <li class="breadcrumb-item active">Agregar Turnos</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Agregar Turnos</h5>

              <!-- Horizontal Form -->
              <form class="row g-3" id='miFormulario' method='post'>
              <?php if (!empty($_SESSION['Mensaje'])) { ?>
                <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                    <?php echo $_SESSION['Mensaje']; ?>
                </div>
              <?php } ?>

                    <div class="col-12">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="text" class="form-control" name="Fecha" id="fecha" placeholder="Selecciona una fecha">
                    </div>

                    <div class="col-12">
                        <label for="hora" class="form-label">Horario</label>
                        <input type="time" class="form-control" name="Horario">
                    </div>

                    <div class="col-12">
                        <label for="selector" class="form-label">Tipo de Servicio</label>
                        <select class="js-example-basic-single form-select" aria-label="Selector" multiple="multiple" name="TipoServicio[]">
                          <option selected="">Selecciona una opcion</option>
                          <?php for ($i=0; $i<$CantidadTipos; $i++) { ?>
                            <option value="<?php echo $ListadoTipos[$i]['ID']; ?>"> 
                            <?php echo $ListadoTipos[$i]['DENOMINACION']; ?>
                            </option>
                          <?php } ?>
                        </select> 
                        
                    </div>
                    
                    <div class="col-12">
                        <label for="selector" class="form-label">Estilista</label>
                        <select class="form-select" aria-label="Selector" name="Estilista">
                          <option selected="">Selecciona una opcion</option>
                          <?php for ($i=0; $i<$CantidadEstilistas; $i++) { ?>
                            <option value="<?php echo $ListadoEstilistas[$i]['ID']; ?>">
                              <?php echo $ListadoEstilistas[$i]['APELLIDO']; ?> , 
                              <?php echo $ListadoEstilistas[$i]['NOMBRE']; ?>
                            </option>
                          <?php } ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="selector" class="form-label">Cliente</label>
                        <select class="form-select" aria-label="Selector"  name="Cliente">
                          <option selected="">Selecciona una opcion</option>
                          <?php for ($i=0; $i<$CantidadClientes; $i++) { ?>
                            <option value="<?php echo $ListadoClientes[$i]['ID']; ?>">
                              <?php echo $ListadoClientes[$i]['APELLIDO']; ?> , 
                              <?php echo $ListadoClientes[$i]['NOMBRE']; ?>
                            </option>
                          <?php } ?>
                        </select>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary" type="submit" value="Registrar" name="Registrar">Registrar</button>
                        <button type="reset" class="btn btn-secondary">Limpiar Campos</button>
                    </div>
                </form>
                <!-- Vertical Form --><!-- End Horizontal Form -->

    </section>

  </main><!-- End #main -->

  <?php
  $_SESSION['Mensaje']='';
require ('footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo

?>
<script>
  // In your Javascript (external .js resource or <script> tag) SELECT 2
  $(document).ready(function() {
  $('.js-example-basic-single').select2();
  });

  // flatpickr para Fecha
  flatpickr("#fecha", {
    dateFormat: "Y-m-d", // Formato de la fecha
    minDate: "today", // Solo permite fechas a partir de hoy
    disable: [
        function(date) {
            // Deshabilitar sábados (6) y domingos (0)
            return (date.getDay() === 0 || date.getDay() === 6);
        }
    ],
    locale: {
        weekdays: {
            shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
            longhand: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"]
        }
    },
    onReady: function(selectedDates, dateStr, instance) {
        // Aplicar estilo personalizado a los días deshabilitados
        const disabledDays = instance.calendarContainer.querySelectorAll('.flatpickr-day.disabled');
        disabledDays.forEach(day => {
            day.style.color = 'red'; // Cambiar el color de los días deshabilitados a rojo
        });
    }
  });
</script>

</body>

</html>