<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

$DatosCompraActual = Datos_Compra($MiConexion, $_GET['ID_COMPRA']);
$DetallesCompra = Detalles_Compra($MiConexion, $_GET['ID_COMPRA']);

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Presupuesto</title>
    <style>
        @page { margin: 10px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 10px;
            color: #333;
        }
        .header-section {
            text-align: center;
            border-bottom: 2px solid #316B70;
            margin-bottom: 15px;
        }
        .logo {
            max-width: 100px;
            display: block;
            margin: 0 auto 10px;
        }
        .main-title {
            font-size: 20px;
            color: #316B70;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 12px;
            color: #6c757d;
            margin-top: 0;
        }
        h2 {
            text-align: center;
            font-size: 16px;
            margin: 10px 0;
        }
        .info-box {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }
        .info-item {
            flex: 1;
            padding-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>

<div class="header-section">
    <?php
    $ruta_imagen = '../assets/img/logo-salon.png';
    if (file_exists($ruta_imagen)) {
        $tipo_imagen = pathinfo($ruta_imagen, PATHINFO_EXTENSION);
        $datos_imagen = file_get_contents($ruta_imagen);
        $base64_imagen = 'data:image/' . $tipo_imagen . ';base64,' . base64_encode($datos_imagen);
    } else {
        $base64_imagen = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><text x="50%" y="50%" text-anchor="middle" fill="#316B70" font-size="14">Pelucan</text></svg>');
    }
    ?>
    <img src="<?= $base64_imagen ?>" class="logo" alt="Logo">
    <h1 class="main-title">Pelucan - Accesorios y Peluquería</h1>
    <p class="subtitle">Av. Principal 1234 - Tel: 351-1234567</p>
</div>

<h2>Comprobante de Presupuesto</h2>

<div class="info-box">
    <div class="info-item"><strong>N° Presupuesto:</strong> <?= $DatosCompraActual['idCompra'] ?></div>
    <div class="info-item"><strong>Fecha:</strong> <?= $DatosCompraActual['fecha'] ?></div>
</div>
<div class="info-box">
    <div class="info-item"><strong>Proveedor:</strong> <?= $DatosCompraActual['PROVEEDOR'] ?></div>
    <div class="info-item"><strong>Responsable:</strong> <?= $DatosCompraActual['USUARIO'] ?></div>
</div>

<h3>Detalles del Presupuesto</h3>
<table>
    <thead>
        <tr>
            <th>Artículo</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($DetallesCompra as $detalle) { ?>
            <tr>
                <td><?= $detalle['ARTICULO'] ?></td>
                <td><?= $detalle['cantidad'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div class="footer">
    <p>Este comprobante corresponde a un pedido de presupuesto a proveedor.</p>
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

$dompdf->stream("comprobante_presupuesto_" . $DatosCompraActual['idCompra'] . ".pdf", array("Attachment" => true));
