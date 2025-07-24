<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require('../encabezado.inc.php'); // Encabezado
require('../barraLateral.inc.php'); // Barra lateral

require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

require_once '../funciones/select_general.php';
$ListadoTipos = Listar_Tipos($MiConexion);
$CantidadTipos = count($ListadoTipos);

$ListadoEstilistas = Listar_Estilistas($MiConexion);
$CantidadEstilistas = count($ListadoEstilistas);

$ListadoClientes = Listar_Clientes_Turnos($MiConexion);
$CantidadClientes = count($ListadoClientes);

require_once '../funciones/insertar_clientes.php';

$_SESSION['Estilo'] = 'alert';

if (!empty($_POST['Registrar'])) {
    // Validar los datos
    Validar_Turno();
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
                <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
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

                    <div class="row">
                        <!-- Campo de Fecha -->
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="text" class="form-control" name="Fecha" id="fecha" placeholder="Selecciona una fecha">
                        </div>

                        <!-- Campo de Horario -->
                        <div class="col-md-6">
                            <label class="form-label" for="horario">Hora Entrega</label>
                            <select class="form-select" id="horario" name="Horario">
                                <option value="08:30">8:30</option>
                                <option value="09:00">9:00</option>
                                <option value="09:30">9:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="12:00">12:00</option>
                                <option value="12:30">12:30</option>
                                <option value="16:00">16:00</option>
                                <option value="16:30">16:30</option>
                                <option value="17:00">17:00</option>
                                <option value="17:30">17:30</option>
                                <option value="18:00">18:00</option>
                                <option value="18:30">18:30</option>
                                <option value="19:00">19:00</option>
                                <option value="19:30">19:30</option>
                            </select>
                        </div>
                    </div>

                    <!-- Campo de Tipo de Servicio -->
                    <div class="col-12">
                        <label for="selector" class="form-label">Tipo de Servicio</label>
                        <select class="js-example-basic-multiple form-select" multiple="multiple" name="TipoServicio[]">
                            <?php for ($i = 0; $i < $CantidadTipos; $i++) { ?>
                                <option value="<?php echo $ListadoTipos[$i]['ID']; ?>">
                                    <?php echo $ListadoTipos[$i]['DENOMINACION']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Campo de Estilista -->
                    <div class="col-12">
                        <label for="selector" class="form-label">Estilista</label>
                        <select class="form-select" aria-label="Selector" name="Estilista" id="estilista">
                            <option selected>Selecciona una opción</option>
                            <?php for ($i = 0; $i < $CantidadEstilistas; $i++) { ?>
                                <option value="<?php echo $ListadoEstilistas[$i]['ID']; ?>">
                                    <?php echo $ListadoEstilistas[$i]['APELLIDO']; ?>,
                                    <?php echo $ListadoEstilistas[$i]['NOMBRE']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Campo de Cliente -->
                    <div class="col-12">
                        <label for="selector" class="form-label">Cliente</label>
                        <select class="form-select" aria-label="Selector" name="Cliente">
                            <option selected>Selecciona una opción</option>
                            <?php for ($i = 0; $i < $CantidadClientes; $i++) { ?>
                                <option value="<?php echo $ListadoClientes[$i]['ID']; ?>">
                                    <?php echo $ListadoClientes[$i]['APELLIDO']; ?>,
                                    <?php echo $ListadoClientes[$i]['NOMBRE']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="text-center">
                        <button class="btn btn-primary" type="submit" value="Registrar" name="Registrar">Registrar</button>
                        <button type="reset" class="btn btn-secondary">Limpiar Campos</button>
                    </div>
                </form>
                <!-- End Horizontal Form -->
            </div>
        </div>
    </section>
</main><!-- End #main -->

<?php
$_SESSION['Mensaje'] = '';
require('../footer.inc.php'); // Footer
?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/minuteIncrement.min.js"></script>
<script>
    // Inicializar Select2
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

    // Configuración de Flatpickr para la Fecha
    const fechaInput = flatpickr("#fecha", {
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

    // Validación adicional en el formulario
    document.getElementById('miFormulario').addEventListener('submit', function(event) {
        const fechaSeleccionada = document.getElementById('fecha').value;
        const fechaActual = new Date().toISOString().split('T')[0];
        const horaSeleccionada = document.getElementById('hora').value;

        if (fechaSeleccionada === fechaActual) {
            const horaActual = new Date();
            const horaActualFormateada = String(horaActual.getHours()).padStart(2, '0') + ':' + String(horaActual.getMinutes()).padStart(2, '0');

            if (horaSeleccionada < horaActualFormateada) {
                alert('No puedes seleccionar un horario anterior a la hora actual.');
                event.preventDefault(); // Evitar que el formulario se envíe
            }
        }
    });
</script>
</body>
</html>