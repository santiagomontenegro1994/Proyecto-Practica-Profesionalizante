<?php
// generar_comprobante_presupuesto.php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

// Obtener datos del presupuesto
$DatosCompraActual = Datos_Compra($MiConexion, $_GET['ID_COMPRA']);
$DetallesCompra = Detalles_Compra($MiConexion, $_GET['ID_COMPRA']);

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Presupuesto</title>
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
        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-item {
            flex: 1;
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
        <div class="header">
            <h1>Comprobante de Presupuesto</h1>
            <p>Fecha: <?php echo $DatosCompraActual['fecha'] ?></p>
        </div>

        <div class="info-box">
            <div class="info-item">
                <h3>Proveedor</h3>
                <p><?php echo $DatosCompraActual['PROVEEDOR'] ?></p>
            </div>
            <div class="info-item">
                <h3>Responsable</h3>
                <p><?php echo $DatosCompraActual['USUARIO'] ?></p>
            </div>
            <div class="info-item">
                <h3>N° Presupuesto</h3>
                <p><?php echo $DatosCompraActual['idCompra'] ?></p>
            </div>
        </div>

        <div class="details">
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
                            <td><?php echo $detalle['ARTICULO'] ?></td>
                            <td><?php echo $detalle['cantidad'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
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

$dompdf->stream("comprobante_presupuesto_".$DatosCompraActual['idCompra'].".pdf", array("Attachment" => true));
?>