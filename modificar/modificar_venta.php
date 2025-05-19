<?php
ob_start(); // Inicia el búfer de salida
session_start();

if (empty($_SESSION['Usuario_Nombre'])) { // Si el usuario no está logueado, redirigir
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require('../encabezado.inc.php'); // Incluir encabezado
require('../barraLateral.inc.php'); // Incluir barra lateral

require_once '../funciones/conexion.php';
require_once '../funciones/select_general.php';
$MiConexion = ConexionBD();

// Array para almacenar los datos de la venta y sus detalles
$DatosVentaActual = array();
$DetallesVenta = array();
//$estados = Listar_Estados($MiConexion);

if (!empty($_POST['BotonModificarVenta'])) {
    // Validar y procesar la modificación de la venta
    if (Modificar_Detalles_Venta($MiConexion, $_POST)) {
        $_SESSION['Mensaje'] = "La venta se ha modificado correctamente!";
        $_SESSION['Estilo'] = 'success';
        header('Location: listados_ventas.php');
        exit;
    } else {
        $_SESSION['Mensaje'] = "Error al modificar la venta.";
        $_SESSION['Estilo'] = 'danger';
    }
} else if (!empty($_GET['ID_VENTA'])) {
    // Obtener los datos de la venta y sus detalles si se pasa el ID por GET
    $DatosVentaActual = Datos_Venta($MiConexion, $_GET['ID_VENTA']);
    $DetallesVenta = Detalles_Venta($MiConexion, $_GET['ID_VENTA']);
}
ob_end_flush(); // Envía el contenido del búfer al navegador
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Detalles Venta</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Menú</a></li>
                <li class="breadcrumb-item">Ventas</li>
                <li class="breadcrumb-item active">Detalles Venta</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <!-- Mostrar mensajes de éxito o error -->
    <?php if (!empty($_SESSION['Mensaje'])) { ?>
        <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
            <?php echo $_SESSION['Mensaje']; ?>
        </div>
    <?php } ?>

    <div class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end w-100">
                    <div class="card-title">Cliente: <span id="nombreCliente" class="text-dark fs-5"><?php echo $DatosVentaActual['CLIENTE_N'] ?>, <?php echo $DatosVentaActual['CLIENTE_A'] ?></span></div>
                    <div class="card-title">Vendedor: <span id="nombreVendedor" class="text-dark fs-5"><?php echo $DatosVentaActual['VENDEDOR'] ?></span></div>
                    <div class="card-title">Fecha de Venta: <span id="fecha" class="text-dark fs-5"><?php echo $DatosVentaActual['FECHA'] ?></span></div>
                </div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <!-- Formulario para modificar la venta -->
                <form method='post'>
                    <input type='hidden' name="IdVenta" value="<?php echo $DatosVentaActual['ID_VENTA']; ?>" />
                    <!-- Detalles de la venta -->
                    <h5 class="card-title">Detalles de la Venta</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($DetallesVenta as $detalle) { ?>
                                <tr>
                                    <td><?php echo $detalle['PRODUCTO']; ?></td>
                                    <td><?php echo $detalle['PRECIO_VENTA']; ?></td>
                                    <td><?php echo $detalle['CANTIDAD']; ?></td>
                                    <td><?php echo number_format($detalle['CANTIDAD'] * $detalle['PRECIO_VENTA'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Botones de acción -->
                    <div class="text-center">
                        <a href="listados/listados_ventas.php" class="btn btn-success btn-info btn-sm">Volver al Listado</a>
                    </div>
                </form><!-- End Horizontal Form -->
            </div>
        </div>
    </section>

    <div class="section">
        <div class="card">
            <div class="card-footer text-end">
                <div class="details">
                    <table class="table w-auto ms-auto"> <!-- w-auto ajusta el ancho -->
                        <tr>
                            <td class="card-title">Sub Total:</td>
                            <td class="text-dark fs-5">$<?php echo $DatosVentaActual['PRECIO_TOTAL'] ?></td>
                        </tr>
                        <tr>
                            <td class="card-title">Descuento:</td>
                            <td class="text-dark fs-5">%<?php echo $DatosVentaActual['DESCUENTO'] ?></td>
                        </tr>
                        <tr>
                            <?php
                            // Calcula el monto del descuento
                            $monto_descuento = ($DatosVentaActual['PRECIO_TOTAL'] * $DatosVentaActual['DESCUENTO']) / 100;
                            $saldo = ($DatosVentaActual['PRECIO_TOTAL'] - $monto_descuento);
                            ?>
                            <td class="card-title">Total:</td>
                            <td class="text-dark fs-5">$<?php echo $saldo ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main><!-- End #main -->

<?php
$_SESSION['Mensaje'] = '';
require('../footer.inc.php'); // Incluir footer
?>

</body>
</html>
