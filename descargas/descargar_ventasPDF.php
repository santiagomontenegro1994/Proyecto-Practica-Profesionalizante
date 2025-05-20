<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .header-info {
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-info">
            <h1>Reporte de Ventas</h1>
            <p>Generado el: <?= date('d/m/Y H:i:s') ?></p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th class="text-right">SubTotal</th>
                    <th class="text-center">Desc.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ventas = explode("\n", trim($_SESSION['Descarga']));
                $total_general = 0;
                
                foreach ($ventas as $venta) {
                    if (empty(trim($venta))) continue;
                    
                    // Parsear cada línea usando el nuevo separador "|"
                    $datos = [];
                    $partes = explode("|", $venta);
                    
                    foreach ($partes as $parte) {
                        $item = explode(": ", trim($parte), 2);
                        if (count($item) === 2) {
                            $datos[$item[0]] = $item[1];
                        }
                    }
                    
                    // Calcular total para sumatoria
                    $total = isset($datos['Total']) ? floatval(str_replace(',', '', $datos['Total'])) : 0;
                    $total_general += $total;
                ?>
                <tr>
                    <td><?= htmlspecialchars($datos['Id Venta'] ?? '') ?></td>
                    <td><?= htmlspecialchars($datos['Fecha'] ?? '') ?></td>
                    <td><?= htmlspecialchars($datos['Cliente'] ?? '') ?></td>
                    <td><?= htmlspecialchars($datos['Vendedor'] ?? '') ?></td>
                    <td class="text-right">$<?= htmlspecialchars($datos['SubTotal'] ?? '0.00') ?></td>
                    <td class="text-center"><?= htmlspecialchars($datos['Descuento'] ?? '0%') ?></td>
                    <td class="text-right">$<?= htmlspecialchars($datos['Total'] ?? '0.00') ?></td>
                </tr>
                <?php } ?>
                
                <!-- Fila de totales -->
                <tr>
                    <td colspan="6" class="text-right"><strong>Total General:</strong></td>
                    <td class="text-right"><strong>$<?= number_format($total_general, 2) ?></strong></td>
                </tr>
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
$options->set(array('isRemoteEnable' => true));
$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream("reporte_ventas_".date('Ymd_His').".pdf", array("Attachment" => true));
unset($_SESSION['Descarga']);
?>