<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

if (empty($_GET['ID_TURNO'])) {
    echo "Error: No se recibió el ID del turno.";
    exit;
}

$ID_TURNO = $_GET['ID_TURNO'];

require_once '../funciones/conexion.php';
$MiConexion = ConexionBD();

require_once '../funciones/select_general.php';
$DatosTurno = Datos_Turno_Comprobante($MiConexion, $ID_TURNO);

if (empty($DatosTurno)) {
    echo "Error: No se encontraron datos para el turno con ID $ID_TURNO.";
    exit;
}

// Obtener servicios y precios
$SQL = "SELECT ts.Denominacion, ts.precio
        FROM detalle_turno dt
        INNER JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
        WHERE dt.idTurno = $ID_TURNO";
$rs = mysqli_query($MiConexion, $SQL);

$servicios = [];
$total = 0;
while ($row = mysqli_fetch_assoc($rs)) {
    $servicios[] = $row['Denominacion'] . ' ($' . number_format($row['precio'], 2, ',', '.') . ')';
    $total += $row['precio'];
}

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Cobro <?= $ID_TURNO ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page { margin: 10px; }
        body { 
            font-family: 'Arial', sans-serif;
            background: #ffffff; 
            font-size: 14px;
            line-height: 1.4;
        }
        .detail-label { 
            font-weight: 600; 
            color: #4a4a4a;
            min-width: 120px;
        }
        .detail-value { color: #2c3e50; }
        .header-section {
            border-bottom: 2px solid #316B70;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
        }
        .logo { 
            max-width: 120px; 
            height: auto;
            display: block;
            margin: 0 auto 10px;
        }
        .main-title {
            font-size: 1.8rem;
            color: #316B70;
            margin-bottom: 5px;
        }
        .subtitle { 
            color: #6c757d;
            margin-bottom: 0;
        }
        .watermark {
            opacity: 0.15;
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 60px;
            color: #316B70;
            pointer-events: none;
        }
        .align-baseline {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        .service-list {
            padding-left: 20px;
            margin: 5px 0 0 0;
        }
        .text-primary-color {
            color: #316B70;
        }
        .total-row {
            font-weight: bold;
            border-top: 2px solid #316B70;
            margin-top: 10px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid" style="max-width: 800px; margin: auto;">
        <!-- Encabezado con logo -->
        <div class="header-section text-center">
            <?php
            $ruta_imagen = '../assets/img/logo-salon.png';
            if (file_exists($ruta_imagen)) {
                $tipo_imagen = pathinfo($ruta_imagen, PATHINFO_EXTENSION);
                $datos_imagen = file_get_contents($ruta_imagen);
                $base64_imagen = 'data:image/' . $tipo_imagen . ';base64,' . base64_encode($datos_imagen);
            } else {
                $base64_imagen = 'data:image/svg+xml;base64,' . base64_encode('
                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120">
                        <text x="50%" y="50%" font-family="Arial" font-size="14" fill="#316B70" text-anchor="middle" dominant-baseline="middle">Pelucan</text>
                    </svg>
                ');
            }
            ?>
            
            <img src="<?= $base64_imagen ?>" alt="Logo Pelucan" class="logo">
            
            <h2 class="main-title">Pelucan - Accesorios y Peluqueria</h2>
            <p class="subtitle">Av. Principal 1234 | Tel: 351-1234567</p>
        </div>

        <!-- Contenido principal -->
        <div class="row mb-3">
            <div class="col-6 align-baseline">
                <span class="detail-label">Ticket N°:</span>
                <span class="detail-value"><?= $ID_TURNO ?></span>
            </div>
            <div class="col-6 align-baseline justify-content-end">
                <span class="detail-label">Fecha cobro:</span>
                <span class="detail-value"><?= date('d/m/Y H:i') ?></span>
            </div>
        </div>

        <div class="row mb-3 align-baseline">
            <div class="col-12">
                <span class="detail-label">Cliente:</span>
                <span class="detail-value"><?= $DatosTurno['CLIENTE'] ?></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <span class="detail-label">Servicios:</span>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th class="text-end">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        $SQL = "SELECT ts.Denominacion, ts.precio
                                FROM detalle_turno dt
                                INNER JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
                                WHERE dt.idTurno = $ID_TURNO";
                        $rs = mysqli_query($MiConexion, $SQL);
                        while ($servicio = mysqli_fetch_assoc($rs)): 
                            $total += $servicio['precio'];
                        ?>
                        <tr>
                            <td><?= $servicio['Denominacion'] ?></td>
                            <td class="text-end">$<?= number_format($servicio['precio'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <tr class="total-row">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-end"><strong>$<?= number_format($total, 2, ',', '.') ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 align-baseline">
                <span class="detail-label">Profesional:</span>
                <span class="detail-value"><?= $DatosTurno['ESTILISTA'] ?></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4" style="color: #6c757d; font-size: 0.9rem;">
            <p class="mb-1">¡Gracias por su visita!</p>
            <p class="mb-0">Conserve este ticket como comprobante</p>
        </div>

        <div class="watermark">COBRADO</div>
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

$dompdf->stream("ticket_cobro_$ID_TURNO.pdf", array("Attachment" => true));
?>