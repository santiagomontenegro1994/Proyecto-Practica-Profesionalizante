<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';

$MiConexion = ConexionBD();

// Validar fechas
if(empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
    $_SESSION['Mensaje'] = "Debe seleccionar ambas fechas";
    $_SESSION['Estilo'] = 'danger';
    header('Location: ../listados/listados_ordenes_compra.php');
    exit;
}

$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];

// Obtener órdenes en el rango
$ordenes = Listar_Ordenes_Compra_Rango($MiConexion, $fecha_inicio, $fecha_fin);

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Órdenes de Compra</title>
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
      margin-bottom: 5px;
      color: #333;
    }
    p {
      text-align: center;
      font-size: 12px;
      margin-top: 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
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
    .total-row {
      font-weight: bold;
      background-color: #f5f5f5;
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
  <img src="<?= $base64_imagen ?>" class="logo" alt="Logo Pelucan">
  <h1 class="main-title">Pelucan - Accesorios y Peluquería</h1>
  <p class="subtitle">Av. Principal 1234 - Tel: 351-1234567</p>
</div>

<h2>Reporte de Órdenes de Compra</h2>
<p>Desde: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> | Hasta: <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Proveedor</th>
      <th>Usuario</th>
      <th class="text-right">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $total_general = 0;
    foreach ($ordenes as $orden): 
      $total_general += $orden['PRECIO_TOTAL'];
    ?>
    <tr>
      <td><?= $orden['ID_ORDEN'] ?></td>
      <td><?= date('d/m/Y', strtotime($orden['FECHA'])) ?></td>
      <td><?= $orden['PROVEEDOR'] ?></td>
      <td><?= $orden['USUARIO'] ?></td>
      <td class="text-right">$<?= number_format($orden['PRECIO_TOTAL'], 2, ',', '.') ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
      <td colspan="4" class="text-right"><strong>Total General:</strong></td>
      <td class="text-right"><strong>$<?= number_format($total_general, 2, ',', '.') ?></strong></td>
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
$options = $dompdf->getOptions();
$options->set(['isRemoteEnable' => true]);
$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream("reporte_ordenes_".date('Ymd_His').".pdf", array("Attachment" => true));