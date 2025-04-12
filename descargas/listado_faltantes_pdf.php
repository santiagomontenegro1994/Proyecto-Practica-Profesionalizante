<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, no lo deja entrar
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

// Incluir la conexión a la base de datos
require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

// Incluir el archivo con las funciones necesarias
require_once '../funciones/select_general.php';

// Obtener el listado de productos ordenados por stock (de menor a mayor)
$ListadoProductos = Listar_Productos_Ordenados_Stock($MiConexion);

// Iniciar el almacenamiento del contenido HTML
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos con Bajo Stock</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Listado de Productos con Bajo Stock</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ListadoProductos as $index => $producto) { ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo $producto['NOMBRE']; ?></td>
                        <td><?php echo $producto['DESCRIPCION']; ?></td>
                        <td><?php echo $producto['STOCK']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
// Terminar de capturar el contenido HTML
$html = ob_get_clean();

// Incluir Dompdf
require_once '../libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Crear una instancia de Dompdf
$dompdf = new Dompdf();

// Configurar opciones para permitir imágenes remotas
$options = $dompdf->getOptions();
$options->set(array('isRemoteEnable' => true));
$dompdf->setOptions($options);

// Cargar el contenido HTML en Dompdf
$dompdf->loadHtml($html);

// Configurar el tamaño y la orientación del papel
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Descargar el archivo PDF
$dompdf->stream("listado_productos_bajo_stock.pdf", array("Attachment" => true));
?>