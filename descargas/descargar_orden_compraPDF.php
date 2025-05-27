<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

if(empty($_GET['ID_ORDEN'])) {
    $_SESSION['Mensaje'] = "Orden no especificada";
    $_SESSION['Estilo'] = 'danger';
    header('Location: ../listados/listados_ordenes_compra.php');
    exit;
}

$id_orden = $_GET['ID_ORDEN'];
$Orden = Datos_Orden_Compra($MiConexion, $id_orden);
$Detalles = Detalles_Orden_Compra($MiConexion, $id_orden);

if(empty($Orden)) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra</title>
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
        .empresa-info {
            margin-bottom: 30px;
            text-align: center;
        }
        .detalles-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .detalle-item {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="empresa-info">
            <h2>PELUCAN</h2>
            <p>Dirección: Calle Falsa 123</p>
            <p>Teléfono: (123) 456-7890</p>
        </div>

        <div class="header">
            <h1>Orden de Compra N° <?= $Orden['idOrdenCompra'] ?></h1>
            <p>Fecha: <?= $Orden['fecha'] ?></p>
        </div>

        <div class="detalles-box">
            <div class="detalle-item">
                <h3>Proveedor</h3>
                <p><?= $Orden['PROVEEDOR'] ?></p>
                <p>Tel: <?= $Orden['telefono'] ?></p>
            </div>
            
            <div class="detalle-item">
                <h3>Datos de la Orden</h3>
                <p>Responsable: <?= $Orden['USUARIO'] ?></p>
                <p>Fecha Emisión: <?= $Orden['fecha'] ?></p>
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
                <?php foreach($Detalles as $detalle): ?>
                <tr>
                    <td><?= $detalle['ARTICULO'] ?></td>
                    <td><?= $detalle['cantidad'] ?></td>
                    <td>$<?= number_format($detalle['precio'], 2) ?></td>
                    <td>$<?= number_format($detalle['cantidad'] * $detalle['precio'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <strong>Total General: $<?= number_format($Orden['PRECIO_TOTAL'], 2) ?></strong>
        </div>

        <div style="margin-top: 50px; text-align: center;">
            <p>_________________________________</p>
            <p>Firma Autorizada</p>
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

$dompdf->stream("orden_compra_".$Orden['idOrdenCompra'].".pdf", array("Attachment" => true));
?>