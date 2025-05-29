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
    <title>Reporte de Pedidos</title>
    <style>
        @page { margin: 20px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11.5px;
            color: #333;
        }
        .header-section {
            text-align: center;
            border-bottom: 2px solid #316B70;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 100px;
            display: block;
            margin: 0 auto 5px;
        }
        .main-title {
            font-size: 1.4rem;
            color: #316B70;
            margin: 0;
        }
        .subtitle {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #316B70;
            color: #fff;
            padding: 6px;
            font-weight: bold;
            text-align: center;
        }
        td {
            padding: 6px;
            border: 1px solid #ccc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

<div class="container">
    <!-- Encabezado -->
    <div class="header-section">
        <?php
        $ruta_logo = '../assets/img/logo-salon.png';
        $base64_logo = file_exists($ruta_logo)
            ? 'data:image/' . pathinfo($ruta_logo, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($ruta_logo))
            : '';
        ?>
        <?php if ($base64_logo): ?>
            <img src="<?= $base64_logo ?>" class="logo" alt="Logo">
        <?php endif; ?>
        <h1 class="main-title">Pelucan - Accesorios y Peluquería</h1>
        <p class="subtitle">Av. Principal 1234 · Tel: 351-1234567 · pelucan@email.com</p>
    </div>

    <!-- Título del reporte -->
    <h2>Reporte de Pedidos</h2>
    <p class="text-center">Generado el: <?= date('d/m/Y H:i:s') ?></p>

    <table>
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="text-right">SubTotal</th>
                <th class="text-center">Desc.</th>
                <th class="text-right">Seña</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pedidos = explode("\n", trim($_SESSION['Descarga']));
            $total_general = 0;
            $total_senia = 0;
            $total_subtotal = 0;

            foreach ($pedidos as $pedido) {
                if (empty(trim($pedido))) continue;

                $datos = [];
                $partes = explode("|", $pedido);

                foreach ($partes as $parte) {
                    $item = explode(": ", trim($parte), 2);
                    if (count($item) === 2) {
                        $datos[trim($item[0])] = trim($item[1]);
                    }
                }

                $subtotal = isset($datos['SubTotal']) ? floatval(str_replace(['$', ','], '', $datos['SubTotal'])) : 0;
                $senia = isset($datos['Seña']) ? floatval(str_replace(['$', ','], '', $datos['Seña'])) : 0;
                $total = isset($datos['Total']) ? floatval(str_replace(['$', ','], '', $datos['Total'])) : 0;

                $total_subtotal += $subtotal;
                $total_general += $total;
                $total_senia += $senia;
            ?>
            <tr>
                <td><?= htmlspecialchars($datos['ID Pedido'] ?? '') ?></td>
                <td><?= htmlspecialchars($datos['Fecha'] ?? '') ?></td>
                <td><?= htmlspecialchars($datos['Cliente'] ?? '') ?></td>
                <td><?= htmlspecialchars($datos['Vendedor'] ?? '') ?></td>
                <td class="text-right">$<?= number_format($subtotal, 2) ?></td>
                <td class="text-center"><?= htmlspecialchars($datos['Descuento'] ?? '0%') ?></td>
                <td class="text-right">$<?= number_format($senia, 2) ?></td>
                <td class="text-right">$<?= number_format($total, 2) ?></td>
            </tr>
            <?php } ?>

            <tr>
                <td colspan="4" class="text-right"><strong>Totales:</strong></td>
                <td class="text-right"><strong>$<?= number_format($total_subtotal, 2) ?></strong></td>
                <td></td>
                <td class="text-right"><strong>$<?= number_format($total_senia, 2) ?></strong></td>
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
$dompdf->stream("reporte_pedidos_" . date('Ymd_His') . ".pdf", array("Attachment" => true));
unset($_SESSION['Descarga']);
?>
