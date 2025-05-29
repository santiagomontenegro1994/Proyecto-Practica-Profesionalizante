<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion = ConexionBD();

if (empty($_GET['ID_VENTA'])) {
    exit("ID de venta no proporcionado.");
}

$ID_VENTA = $_GET['ID_VENTA'];
$DatosVenta = Datos_Venta($MiConexion, $ID_VENTA);
$DetallesVenta = Detalles_Venta($MiConexion, $ID_VENTA);

$monto_descuento = ($DatosVenta['PRECIO_TOTAL'] * $DatosVenta['DESCUENTO']) / 100;
$total = $DatosVenta['PRECIO_TOTAL'] - $monto_descuento;

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comprobante de Venta</title>
  <style>
    @page { margin: 10px; }
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      color: #333;
      padding: 10px;
    }
    .header-section {
      text-align: center;
      border-bottom: 2px solid #316B70;
      margin-bottom: 15px;
    }
    .logo {
      max-width: 100px;
      margin: 0 auto 10px;
      display: block;
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
    .section-title {
      font-size: 14px;
      margin-top: 20px;
      border-bottom: 1px solid #ccc;
      padding-bottom: 5px;
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
      background-color: #f2f2f2;
    }
    .text-right {
      text-align: right;
    }
    .text-center {
      text-align: center;
    }
    .info-box {
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
    }
    .info-item {
      flex: 1;
      padding-right: 10px;
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

<h2 class="text-center">Comprobante de Venta</h2>

<div class="info-box">
  <div class="info-item"><strong>N° Venta:</strong> <?= $DatosVenta['ID_VENTA'] ?></div>
  <div class="info-item"><strong>Fecha:</strong> <?= $DatosVenta['FECHA'] ?></div>
</div>
<div class="info-box">
  <div class="info-item"><strong>Cliente:</strong> <?= $DatosVenta['CLIENTE_N'] ?>, <?= $DatosVenta['CLIENTE_A'] ?></div>
  <div class="info-item"><strong>Vendedor:</strong> <?= $DatosVenta['VENDEDOR'] ?></div>
</div>

<h3 class="section-title">Detalle de la Venta</h3>
<table>
  <thead>
    <tr>
      <th>Producto</th>
      <th class="text-right">Precio</th>
      <th class="text-right">Cantidad</th>
      <th class="text-right">Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($DetallesVenta as $detalle): 
      $subtotal = $detalle['CANTIDAD'] * $detalle['PRECIO_VENTA'];
    ?>
    <tr>
      <td><?= $detalle['PRODUCTO'] ?></td>
      <td class="text-right">$<?= number_format($detalle['PRECIO_VENTA'], 2, ',', '.') ?></td>
      <td class="text-right"><?= $detalle['CANTIDAD'] ?></td>
      <td class="text-right">$<?= number_format($subtotal, 2, ',', '.') ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3 class="section-title">Resumen</h3>
<table style="width: 300px; margin-left: auto;">
  <tr>
    <td>Subtotal:</td>
    <td class="text-right">$<?= number_format($DatosVenta['PRECIO_TOTAL'], 2, ',', '.') ?></td>
  </tr>
  <tr>
    <td>Descuento (<?= $DatosVenta['DESCUENTO'] ?>%):</td>
    <td class="text-right">$<?= number_format($monto_descuento, 2, ',', '.') ?></td>
  </tr>
  <tr>
    <td><strong>Total:</strong></td>
    <td class="text-right"><strong>$<?= number_format($total, 2, ',', '.') ?></strong></td>
  </tr>
</table>

<p class="text-center" style="margin-top: 20px; color: #666;">¡Gracias por su compra!</p>

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
$dompdf->stream("comprobante_venta_" . $ID_VENTA . ".pdf", ["Attachment" => true]);
