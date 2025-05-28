<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion = ConexionBD();

// Obtener datos de la venta
$DatosVentaActual = Datos_Venta($MiConexion, $_GET['ID_VENTA']);
$DetallesVenta = Detalles_Venta($MiConexion, $_GET['ID_VENTA']);

// Calcular montos
$monto_descuento = ($DatosVentaActual['PRECIO_TOTAL'] * $DatosVentaActual['DESCUENTO']) / 100;
$total = $DatosVentaActual['PRECIO_TOTAL'] - $monto_descuento;

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .details {
            margin: 20px 0;
        }
        .details h3 {
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-item {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Comprobante de Venta</h1>
            <p>Fecha: <?php echo $DatosVentaActual['FECHA'] ?></p>
        </div>

        <div class="info-box">
            <div class="info-item">
                <h3>Cliente</h3>
                <p><?php echo $DatosVentaActual['CLIENTE_N'] ?>, <?php echo $DatosVentaActual['CLIENTE_A'] ?></p>
            </div>
            <div class="info-item">
                <h3>Vendedor</h3>
                <p><?php echo $DatosVentaActual['VENDEDOR'] ?></p>
            </div>
            <div class="info-item">
                <h3>N° Venta</h3>
                <p><?php echo $DatosVentaActual['ID_VENTA'] ?></p>
            </div>
        </div>

        <div class="details">
            <h3>Detalles de la Venta</h3>
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
                    <?php foreach ($DetallesVenta as $detalle) { ?>
                        <tr>
                            <td><?php echo $detalle['PRODUCTO'] ?></td>
                            <td class="text-right">$<?php echo number_format($detalle['PRECIO_VENTA'], 2) ?></td>
                            <td class="text-right"><?php echo $detalle['CANTIDAD'] ?></td>
                            <td class="text-right">$<?php echo number_format($detalle['CANTIDAD'] * $detalle['PRECIO_VENTA'], 2) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="details">
            <table class="text-right" style="width: 300px; margin-left: auto;">
                <tr>
                    <td>Subtotal:</td>
                    <td>$<?php echo number_format($DatosVentaActual['PRECIO_TOTAL'], 2) ?></td>
                </tr>
                <tr>
                    <td>Descuento (<?php echo $DatosVentaActual['DESCUENTO'] ?>%):</td>
                    <td>$<?php echo number_format($monto_descuento, 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td><strong>$<?php echo number_format($total, 2) ?></strong></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Gracias por su compra</p>
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
$options->set(array('isRemoteEnable' => true));
$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("comprobante_venta_".$DatosVentaActual['ID_VENTA'].".pdf", array("Attachment" => true));