<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion = ConexionBD();

// Obtener datos del pedido
$DatosPedidoActual = Datos_Pedido($MiConexion, $_GET['ID_PEDIDO']);
$DetallesPedido = Detalles_Pedido($MiConexion, $_GET['ID_PEDIDO']);

// Calcular montos
$monto_descuento = ($DatosPedidoActual['PRECIO_TOTAL'] * $DatosPedidoActual['DESCUENTO']) / 100;
$total = $DatosPedidoActual['PRECIO_TOTAL'] - $monto_descuento;
$saldo = $total - $DatosPedidoActual['SENIA'];

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pedido</title>
    <style>
        @page { margin: 10px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .container {
            max-width: 750px;
            margin: auto;
            padding: 20px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #316B70;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin: 0 auto 10px;
            display: block;
        }
        .main-title {
            font-size: 20px;
            color: #316B70;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #6c757d;
            font-size: 14px;
        }
        .info-box {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .info-item {
            width: 32%;
        }
        .info-item h4 {
            margin-bottom: 5px;
            font-size: 14px;
            color: #444;
        }
        .info-item p {
            margin: 0;
            color: #222;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px;
        }
        th, td {
            padding: 7px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals td {
            padding: 6px;
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
    <div class="container">

        <!-- ENCABEZADO -->
        <div class="header-section">
            <?php
            $ruta_imagen = '../assets/img/logo-salon.png';
            if (file_exists($ruta_imagen)) {
                $tipo_imagen = pathinfo($ruta_imagen, PATHINFO_EXTENSION);
                $datos_imagen = file_get_contents($ruta_imagen);
                $base64_imagen = 'data:image/' . $tipo_imagen . ';base64,' . base64_encode($datos_imagen);
            } else {
                $base64_imagen = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="50"><text x="10" y="30" fill="#316B70" font-size="20">Pelucan</text></svg>');
            }
            ?>
            <img src="<?= $base64_imagen ?>" class="logo" alt="Logo">
            <h2 class="main-title">Pelucan - Accesorios y Peluquería</h2>
            <p class="subtitle">Av. Principal 1234 - Tel: 351-1234567</p>
        </div>

        <!-- INFORMACIÓN -->
        <div class="info-box">
            <div class="info-item">
                <h4>Cliente</h4>
                <p><?= $DatosPedidoActual['CLIENTE_N'] ?>, <?= $DatosPedidoActual['CLIENTE_A'] ?></p>
            </div>
            <div class="info-item">
                <h4>Vendedor</h4>
                <p><?= $DatosPedidoActual['VENDEDOR'] ?></p>
            </div>
            <div class="info-item">
                <h4>N° Pedido</h4>
                <p><?= $DatosPedidoActual['ID_PEDIDO'] ?></p>
                <p><strong>Fecha:</strong> <?= $DatosPedidoActual['FECHA'] ?></p>
            </div>
        </div>

        <!-- DETALLES -->
        <h4 style="margin-top: 30px;">Detalles del Pedido</h4>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($DetallesPedido as $detalle): ?>
                <tr>
                    <td><?= $detalle['PRODUCTO'] ?></td>
                    <td class="text-right">$<?= number_format($detalle['PRECIO_VENTA'], 2) ?></td>
                    <td class="text-right"><?= $detalle['CANTIDAD'] ?></td>
                    <td class="text-right">$<?= number_format($detalle['CANTIDAD'] * $detalle['PRECIO_VENTA'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TOTALES -->
        <table class="totals">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">$<?= number_format($DatosPedidoActual['PRECIO_TOTAL'], 2) ?></td>
            </tr>
            <tr>
                <td>Descuento (<?= $DatosPedidoActual['DESCUENTO'] ?>%):</td>
                <td class="text-right">$<?= number_format($monto_descuento, 2) ?></td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td class="text-right"><strong>$<?= number_format($total, 2) ?></strong></td>
            </tr>
            <tr>
                <td>Seña:</td>
                <td class="text-right">$<?= number_format($DatosPedidoActual['SENIA'], 2) ?></td>
            </tr>
            <tr>
                <td><strong>Saldo:</strong></td>
                <td class="text-right"><strong>$<?= number_format($saldo, 2) ?></strong></td>
            </tr>
        </table>

        <div class="footer">
            <p>Gracias por su pedido</p>
        </div>
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

$dompdf->stream("comprobante_pedido_".$DatosPedidoActual['ID_PEDIDO'].".pdf", array("Attachment" => true));
?>
