<?php
session_start();
require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

if (empty($_SESSION['Usuario_Nombre'])) {
    exit("No autorizado");
}

$conexion = ConexionBD();
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

if (!$fecha_inicio || !$fecha_fin) {
    exit("Fechas inválidas");
}

$ventas = Listar_Ventas_Fecha($conexion, $fecha_inicio, $fecha_fin);
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Ventas</title>
  <style>
    body { font-family: Arial; font-size: 12px; padding: 20px; }
    h2 { text-align: center; color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #f0f0f0; }
    .text-right { text-align: right; }
  </style>
</head>
<body>
  <h2>Reporte de Ventas</h2>
  <p>Desde: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> | Hasta: <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Vendedor</th>
        <th>Subtotal</th>
        <th>Descuento</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total_final = 0;
      foreach ($ventas as $venta) {
          $desc = $venta['PRECIO_TOTAL'] * ($venta['DESCUENTO'] / 100);
          $total = $venta['PRECIO_TOTAL'] - $desc;
          $total_final += $total;
      ?>
      <tr>
        <td><?= $venta['ID_VENTA'] ?></td>
        <td><?= $venta['FECHA'] ?></td>
        <td><?= $venta['CLIENTE_N'] ?>, <?= $venta['CLIENTE_A'] ?></td>
        <td><?= $venta['VENDEDOR'] ?></td>
        <td class="text-right">$<?= number_format($venta['PRECIO_TOTAL'], 2) ?></td>
        <td class="text-right"><?= $venta['DESCUENTO'] ?>%</td>
        <td class="text-right">$<?= number_format($total, 2) ?></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="6" class="text-right"><strong>Total general:</strong></td>
        <td class="text-right"><strong>$<?= number_format($total_final, 2) ?></strong></td>
      </tr>
    </tbody>
  </table>
</body>
</html>
<?php
$html = ob_get_clean();
require_once '../libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("reporte_ventas_" . date('Ymd_His') . ".pdf", ["Attachment" => true]);
