<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();
ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page { margin: 20px; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.4;
        }
        .header-section {
            text-align: center;
            border-bottom: 2px solid #316B70;
            margin-bottom: 15px;
        }
        .logo {
            max-width: 100px;
            display: block;
            margin: auto;
        }
        .main-title {
            font-size: 1.6rem;
            color: #316B70;
            margin-bottom: 4px;
        }
        .subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }
        .turno-card {
            border: 1px solid #ccc;
            border-left: 5px solid #316B70;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .turno-card strong {
            color: #316B70;
        }
    </style>
</head>
<body>
    <div class="container-fluid" style="max-width: 800px; margin: auto;">
        <div class="header-section">
            <?php
            $ruta_logo = '../assets/img/logo-salon.png';
            $base64_logo = file_exists($ruta_logo)
                ? 'data:image/' . pathinfo($ruta_logo, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($ruta_logo))
                : '';
            ?>
            <?php if ($base64_logo): ?>
                <img src="<?= $base64_logo ?>" alt="Logo" class="logo">
            <?php endif; ?>
            <h2 class="main-title">Pelucan - Accesorios y Peluquería</h2>
            <p class="subtitle">Av. Principal 1234 | Tel: 351-1234567</p>
        </div>

        <h4 class="text-center mb-4">Listado de Turnos</h4>

        <?php
        if (!empty($_SESSION['Descarga'])) {
            $turnos_array = explode('Cliente:', $_SESSION['Descarga']);
            if (empty(trim($turnos_array[0]))) {
                array_shift($turnos_array);
            }
            foreach ($turnos_array as $turno) {
                $turno = trim($turno);
                if ($turno) {
                    echo "<div class='turno-card'><strong>Cliente:</strong> $turno</div>";
                }
            }
        } else {
            echo "<p class='text-danger'>No hay información de turnos disponible.</p>";
        }
        ?>
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
$dompdf->stream("listado_turnos.pdf", ["Attachment" => true]);
?>
