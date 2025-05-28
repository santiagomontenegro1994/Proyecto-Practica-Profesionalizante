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
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .periodo { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Órdenes de Compra</h2>
        <h3>PELUCAN</h3>
        <p>Periodo: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Usuario</th>
                <th>Total</th>
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
                <td>$<?= number_format($orden['PRECIO_TOTAL'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="total">Total General:</td>
                <td class="total">$<?= number_format($total_general, 2) ?></td>
            </tr>
        </tfoot>
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

$dompdf->stream("reporte_ordenes_".date('Ymd').".pdf", array("Attachment" => true));
?>