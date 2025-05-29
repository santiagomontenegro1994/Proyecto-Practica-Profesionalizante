<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

if (empty($_GET['ID_ORDEN'])) {
    $_SESSION['Mensaje'] = "Orden no especificada";
    $_SESSION['Estilo'] = 'danger';
    header('Location: ../listados/listados_ordenes_compra.php');
    exit;
}

$id_orden = $_GET['ID_ORDEN'];
$Orden = Datos_Orden_Compra($MiConexion, $id_orden);
$Detalles = Detalles_Orden_Compra($MiConexion, $id_orden);

if (empty($Orden)) {
    $_SESSION['Mensaje'] = "Orden no encontrada";
    $_SESSION['Estilo'] = 'danger';
    header('Location: ../listados/listados_ordenes_compra.php');
    exit;
}

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra</title>
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
            margin: 10px 0 20px;
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
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
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

<h2>Orden de Compra N° <?= $Orden['idOrdenCompra'] ?></h2>

<div class="info-box">
    <div class="info-item">
        <strong>Fecha:</strong> <?= $Orden['fecha'] ?><br>
        <strong>Responsable:</strong> <?= $Orden['USUARIO'] ?>
    </div>
    <div class="info-item">
        <strong>Proveedor:</strong> <?= $Orden['PROVEEDOR'] ?><br>
        <strong>Teléfono:</strong> <?= $Orden['telefono'] ?>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Artículo</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($Detalles as $detalle): ?>
        <tr>
            <td><?= $detalle['ARTICULO'] ?></td>
            <td><?= $detalle['cantidad'] ?></td>
            <td>$<?= number_format($detalle['precio'], 2, ',', '.') ?></td>
            <td>$<?= number_format($detalle['cantidad'] * $detalle['precio'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="text-right" style="margin-top: 10px;">
    <strong>Total General: $<?= number_format($Orden['PRECIO_TOTAL'], 2, ',', '.') ?></strong>
</p>

<div class="footer">
    <p>_________________________________</p>
    <p>Firma Autorizada</p>
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
$dompdf->stream("orden_compra_" . $Orden['idOrdenCompra'] . ".pdf", ["Attachment" => true]);
