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
    $_SESSION['Mensaje'] = Validar_Turno();
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

                    <!-- Campo de Fecha -->
                    <div class="col-12">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="text" class="form-control" name="Fecha" id="fecha" placeholder="Selecciona una fecha">
                    </div>

                    <!-- Campo de Horario -->
                    <div class="col-12">
                        <label for="hora" class="form-label">Horario</label>
                        <input type="text" class="form-control" name="Horario" id="hora" placeholder="Selecciona un horario">
                    </div>

                    <!-- Campo de Tipo de Servicio -->
                    <div class="col-12">
                        <label for="selector" class="form-label">Tipo de Servicio</label>
                        <select class="js-example-basic-single form-select" aria-label="Selector" multiple="multiple" name="TipoServicio[]">
                            <option selected>Selecciona una opción</option>
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
        $('.js-example-basic-single').select2();
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

    // Configuración de Flatpickr para el Horario
    const horaInput = flatpickr("#hora", {
        enableTime: true, // Habilitar selección de hora
        noCalendar: true, // Ocultar el calendario
        dateFormat: "H:i", // Formato de hora
        time_24hr: true, // Usar formato de 24 horas
        minuteIncrement: 30, // Intervalos de 30 minutos
        minTime: "08:00", // Hora mínima por defecto
        maxTime: "16:00", // Hora máxima
        disable: [], // Inicialmente vacío, se llenará dinámicamente
        onReady: function(selectedDates, dateStr, instance) {
            // Aplicar estilo personalizado a los horarios deshabilitados
            const disabledHours = instance.calendarContainer.querySelectorAll('.flatpickr-time .flatpickr-hour.disabled, .flatpickr-time .flatpickr-minute.disabled');
            disabledHours.forEach(hour => {
                hour.style.color = 'red'; // Cambiar el color de los horarios deshabilitados a rojo
            });
        }
    });

    // Función para actualizar los horarios deshabilitados
    function actualizarHorariosDeshabilitados(fechaSeleccionada) {
        const fechaActual = new Date().toISOString().split('T')[0]; // Fecha actual en formato YYYY-MM-DD

        // Hacer una solicitud AJAX para obtener los horarios ocupados
        fetch('../ajax.php?action=obtener_horarios_ocupados&filtro=' + fechaSeleccionada)
            .then(response => response.json())
            .then(data => {
                // Deshabilitar horarios ocupados
                horaInput.set('disable', data);

                // Si la fecha seleccionada es hoy, deshabilitar horarios anteriores a la hora actual
                if (fechaSeleccionada === fechaActual) {
                    const horaActual = new Date();
                    const horaActualFormateada = String(horaActual.getHours()).padStart(2, '0') + ':' + String(horaActual.getMinutes()).padStart(2, '0');

                    // Deshabilitar horarios anteriores a la hora actual
                    horaInput.set('minTime', horaActualFormateada);

                    // Si la hora actual es mayor que las 16:00, deshabilitar todo el día
                    if (horaActual.getHours() >= 16) {
                        horaInput.set('disable', ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00']);
                    }
                } else {
                    // Si la fecha seleccionada es en el futuro, no hay restricciones adicionales
                    horaInput.set('minTime', '08:00');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Actualizar horarios deshabilitados al cargar la página
    const fechaInicial = document.getElementById('fecha').value;
    if (fechaInicial) {
        actualizarHorariosDeshabilitados(fechaInicial);
    }

    // Actualizar horarios deshabilitados al cambiar la fecha
    document.getElementById('fecha').addEventListener('change', function() {
        const fechaSeleccionada = this.value;
        actualizarHorariosDeshabilitados(fechaSeleccionada);
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