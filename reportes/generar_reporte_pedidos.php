<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
require_once '../libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$conexion = ConexionBD();

// Validación de fechas
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo "Fechas inválidas.";
    exit;
}

// Obtener los pedidos en el rango de fechas
$Pedidos = Listar_Pedidos_Fecha($conexion, $fecha_inicio, $fecha_fin);

// Generar contenido HTML
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pedidos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        h2 { text-align: center; margin-bottom: 10px; }
        .fecha { text-align: center; margin-bottom: 20px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .logo { text-align: center; margin-bottom: 10px; }
        .logo img { width: 100px; }
    </style>
</head>
<body>
    <div class="logo">
        <?php
        $ruta_logo = '../assets/img/logo-salon.png';
        if (file_exists($ruta_logo)) {
            $tipo = pathinfo($ruta_logo, PATHINFO_EXTENSION);
            $data = file_get_contents($ruta_logo);
            $base64 = 'data:image/' . $tipo . ';base64,' . base64_encode($data);
            echo "<img src='$base64' alt='Logo'>";
        }
        ?>
    </div>

    <h2>Reporte de Pedidos</h2>
    <div class="fecha">Desde <?= date('d/m/Y', strtotime($fecha_inicio)) ?> hasta <?= date('d/m/Y', strtotime($fecha_fin)) ?></div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="text-right">SubTotal</th>
                <th class="text-center">% Desc.</th>
                <th class="text-right">Seña</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalGeneral = 0;
            $totalSenia = 0;
            $subtotalAcumulado = 0;

            foreach ($Pedidos as $pedido) {
                $subtotal = $pedido['PRECIO_TOTAL'];
                $descuento = $pedido['DESCUENTO'];
                $senia = $pedido['SENIA'];
                $montoDescuento = $subtotal * ($descuento / 100);
                $total = $subtotal - $montoDescuento - $senia;

                $subtotalAcumulado += $subtotal;
                $totalSenia += $senia;
                $totalGeneral += $total;
            ?>
            <tr>
                <td><?= $pedido['ID_PEDIDO'] ?></td>
                <td><?= date('d/m/Y', strtotime($pedido['FECHA'])) ?></td>
                <td><?= $pedido['CLIENTE_N'] ?>, <?= $pedido['CLIENTE_A'] ?></td>
                <td><?= $pedido['VENDEDOR'] ?></td>
                <td class="text-right">$<?= number_format($subtotal, 2) ?></td>
                <td class="text-center"><?= $descuento ?>%</td>
                <td class="text-right">$<?= number_format($senia, 2) ?></td>
                <td class="text-right">$<?= number_format($total, 2) ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Totales</strong></td>
                <td class="text-right"><strong>$<?= number_format($subtotalAcumulado, 2) ?></strong></td>
                <td></td>
                <td class="text-right"><strong>$<?= number_format($totalSenia, 2) ?></strong></td>
                <td class="text-right"><strong>$<?= number_format($totalGeneral, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
<?php
$html = ob_get_clean();

// PDF
$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->set(['isRemoteEnable' => true]);
$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("reporte_pedidos_{$fecha_inicio}_{$fecha_fin}.pdf", ["Attachment" => true]);
