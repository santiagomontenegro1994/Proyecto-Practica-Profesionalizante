<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: cerrarsesion.php');
  exit;
}
 ($_SESSION['Descarga']);

//voy a necesitar la conexion: incluyo la funcion de Conexion.
require_once 'funciones/conexion.php';

//genero una variable para usar mi conexion desde donde me haga falta
//no envio parametros porque ya los tiene definidos por defecto
$MiConexion = ConexionBD();

//ahora voy a llamar el script con la funcion que genera mi listado
require_once 'funciones/select_general.php';


//voy a ir listando lo necesario para trabajar en este script: 
$ListadoTurnos = Listar_Turnos($MiConexion);
$CantidadTurnos = count($ListadoTurnos);

//Empiezo a guardar el contenido en una variable
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Turnos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Lista de Turnos</h2>
        <div class="list-group">
            <?php
            // Separar el string en base a 'Cliente:'
            $turnos_array = explode('Cliente:', ($_SESSION['Descarga']));
            // Eliminar el primer elemento si está vacío
            if (empty($turnos_array[0])) {
                array_shift($turnos_array);
            }
            // Formatear la salida
            foreach ($turnos_array as $turno) {
                $turno = trim($turno); // Eliminar espacios en blanco al principio y al final
                echo "<div class='list-group-item'>Cliente: " . $turno . "</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>


<?php
//Termino de guardar el contenido en un variable 
$html=ob_get_clean();
//echo $html;

//creo la variable dompdf
require_once 'libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();

//activo las opciones para poder generar el pdf con imagenes
$options = $dompdf->getOptions();
$options->set(array('isRemoteEnable' => true));
$dompdf->setOptions($options);

//le paso el $html en el que guardamos toda la lista
$dompdf->loadHtml($html);

//seteo el papel en A4 vertical
$dompdf->setPaper('A4','portrait');

$dompdf->render();
//le indico el nombre del archivo y le doy true para que descargue
$dompdf->stream("archivo.pdf", array("Attachment" => true));
?>