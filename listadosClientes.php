<?php
require ('encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

//voy a necesitar la conexion: incluyo la funcion de Conexion.
require_once 'funciones/conexion.php';

//genero una variable para usar mi conexion desde donde me haga falta
//no envio parametros porque ya los tiene definidos por defecto
$MiConexion = ConexionBD();

//ahora voy a llamar el script con la funcion que genera mi listado
require_once 'funciones/select_clientes.php';


//voy a ir listando lo necesario para trabajar en este script: 
$ListadoNiveles = Listar_Clientes($MiConexion);
$CantidadNiveles = count($ListadoNiveles);


?>



<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Clientes</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Menu</a></li>
      <li class="breadcrumb-item">Clientes</li>
      <li class="breadcrumb-item active">Listado Clientes</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    
    <div class="card">
        <div class="card-body">
          <h5 class="card-title">Listado Clientes</h5>

          <!-- Table with stripped rows -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido</th>
                <th scope="col">Direccion</th>
                <th scope="col">Telefono</th>
                <th scope="col">Email</th>
              </tr>
            </thead>
            <tbody>
                <?php for ($i=0; $i<$CantidadNiveles; $i++) { ?>
                    <tr>
                        <th scope="row"><?php echo $ListadoNiveles[$i]['ID']; ?></th>
                        <td><?php echo $ListadoNiveles[$i]['NOMBRE']; ?></td>
                        <td><?php echo $ListadoNiveles[$i]['APELLIDO']; ?></td>
                        <td><?php echo $ListadoNiveles[$i]['DIRECCION']; ?></td>
                        <td><?php echo $ListadoNiveles[$i]['TELEFONO']; ?></td>
                        <td><?php echo $ListadoNiveles[$i]['EMAIL']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
          </table>
          <!-- End Table with stripped rows -->

        </div>
    </div>
 
</section>

</main><!-- End #main -->

  <?php
require ('footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo

?>


</body>

</html>