<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

require_once '../funciones/select_general.php';
$ListadoProductos = Listar_Productos_Bajo_Stock($MiConexion);

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos con Bajo Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page { margin: 20px; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            color: #333;
        }
        .header-section {
            text-align: center;
            border-bottom: 2px solid #316B70;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            display: block;
            margin: auto;
        }
        .main-title {
            font-size: 1.5rem;
            color: #316B70;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .table th {
            background-color: #316B70;
            color: white;
            text-align: center;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>

<div class="container-fluid" style="max-width: 800px; margin: auto;">
    <!-- Encabezado con logo -->
    <div class="header-section">
        <?php
        $ruta_logo = '../assets/img/logo-salon.png';
        $base64_logo = file_exists($ruta_logo)
            ? 'data:image/' . pathinfo($ruta_logo, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($ruta_logo))
            : '';
        ?>
        <?php if ($base64_logo): ?>
            <img src="<?= $base64_logo ?>" alt="Logo" class="logo mb-2">
        <?php endif; ?>
        <h2 class="main-title">Pelucan - Accesorios y Peluquería</h2>
        <p class="subtitle">Av. Principal 1234 · Tel: 351-1234567 · pelucan@email.com</p>
    </div>

    <h4 class="text-center mb-4">Listado de Productos con Bajo Stock</h4>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ListadoProductos)): ?>
                <?php foreach ($ListadoProductos as $index => $producto): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $producto['nombre'] ?></td>
                        <td><?= $producto['descripcion'] ?></td>
                        <td><?= $producto['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-danger">No se encontraron productos con bajo stock.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$html = ob_get_clean();

require_once '../libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->set(['isRemoteEnable' => true]);
$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("productos_bajo_stock.pdf", ["Attachment" => true]);
?>
