<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('../barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Pedidos</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Menu</a></li>
      <li class="breadcrumb-item">Pedidos</li>
      <li class="breadcrumb-item active">Agregar Pedido</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-start align-items-center"> 
                <h5 class="card-title mr-2">Datos del Cliente</h5> 
                <a href="#" class="btn btn-primary btn-sm m-2 btn_new_cliente">Nuevo Cliente</a>
            </div>

<!-- Horizontal Form -->
        <form class="row g-1" id="formularioClienteVenta" name="form_new_cliente_venta">

            <input type="hidden" name="action" value="addCliente">
            <input type="hidden" name="idCliente" id="idCliente">

            <div class="col-md-4 mb-1">
                <label for="dni_cliente" class="form-label">DNI</label>
                <input type="number" class="form-control form-control-sm"  name="dni_cliente" id="dni_cliente">
            </div>
            <div class="col-md-4 mb-1">
                <label for="nom_cliente" class="form-label">Nombre</label>
                <input type="text" class="form-control form-control-sm"  name="nom_cliente" id="nom_cliente" disabled required>
            </div>
            <div class="col-md-4 mb-1">
                <label for="ape_cliente" class="form-label">Apellido</label>
                <input type="text" class="form-control form-control-sm"  name="ape_cliente" id="ape_cliente" disabled required>
            </div>
            <div class="col-md-12 d-flex justify-content-center">
                <div class="col-md-6 mb-1 d-flex align-items-center">
                    <label for="tel_cliente" class="form-label me-2">Telefono</label>
                    <input type="number" class="form-control form-control-sm" name="tel_cliente" id="tel_cliente" disabled required>
                </div>
            </div>

            <div class="text-center" id="div_registro_cliente" style="display: none;">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
<!-- End Horizontal Form -->
        </div>
    </div>   
    
    <!-- Table with stripped rows -->
    <div class="card">
    <div class="card-body">
        <h5 class="card-title mr-2">Datos del Pedido</h5>
    <table class="table table-striped">
        <thead>
            <tr class="table-primary">
            <th scope="col" class="col-2 text-truncate" style="max-width: 50px;">COD.</th>
            <th scope="col" class="col-5">Producto</th>
            <th scope="col" class="col-3">Categoría</th>
            <th scope="col" class="col-2">Cantidad</th>
            <th scope="col" class="col-2">Precio</th>
            <th scope="col" class="col-5">Precio Total</th>
            <th scope="col" class="col-6">Acción</th>
            </tr>
              
            <tr class=""  data-bs-toggle="tooltip" data-bs-placement="left" >
                <th><input type="text" name="txtIdProducto" id="txtIdProducto" class="form-control form-control-sm w-75"></th>
                <td id="txt_producto">-</td>
                <td id="txt_categoria">-</td>
                <th><input type="number" name="txt_cantidad_producto" id="txt_cantidad_producto" value="0" min="0" class="form-control form-control-sm w-50" disabled></th>
                <td id="txt_precio">0.00</td>
                <td id="txt_precio_total">0.00</td>
                <td><a href="#" id="add_producto_pedido" class="text-primary fw-bold" style="display: none;"><i class="bi bi-bag-plus-fill text-primary fs-5"></i> Agregar</a></td>   
            </tr>

            <tr class="table-primary">
                <th scope="col">COD.</th>
                <th scope="col">Producto</th>
                <th scope="col">Categoría</th>
                <th scope="col" class="col-2">Cantidad</th>
                <th scope="col">Precio</th>
                <th scope="col">Precio Total</th>
                <th scope="col">Acción</th>
            </tr>
        </thead>
        <tbody id="detalleVenta"> 
        <!-- CONTENIDO AJAX-->
        </tbody>

        <tfoot id="detalleTotal">
        <!-- CONTENIDO AJAX-->
        </tfoot>
    </table>
    </div>
    </div>
    <div class="d-flex justify-content-center align-items-center"> 
        <a href="#" class="btn btn-danger btn-sm m-2" id="btn_anular_pedido">Anular</a> 
        <a href="#" class="btn btn-primario btn-sm m-2" id="btn_new_pedido" style="display: none;">Crear Pedido</a>
    </div>
          <!-- End Table with stripped rows -->

</section>            
            

</main><!-- End #main -->

<?php
$_SESSION['Mensaje']='';
require ('../footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
?>


<script type="text/javascript">//script para traer el detalle de la venta
    $(document).ready(function(){ //se ejecuta después que se cargue todo el documento
        searchforDetallePedido();
    });

</script>

</body>

</html>