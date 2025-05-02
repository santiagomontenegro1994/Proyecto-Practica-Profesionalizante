<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo
require ('../barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

//voy a necesitar la conexion: incluyo la funcion de Conexion.
require_once '../funciones/conexion.php';

//genero una variable para usar mi conexion desde donde me haga falta
//no envio parametros porque ya los tiene definidos por defecto
$MiConexion = ConexionBD();

//ahora voy a llamar el script con la funcion que genera mi listado
require_once '../funciones/select_general.php';

//voy a ir listando lo necesario para trabajar en este script: 
$ListadoTurnos = Listar_Turnos($MiConexion);
$CantidadTurnos = count($ListadoTurnos);

$ListadoTiposTurnos = Listar_Tipos($MiConexion);
$CantidadTiposTurnos = count($ListadoTiposTurnos);

//estoy en condiciones de poder buscar segun el parametro
if (!empty($_POST['Buscar'])) {

  $parametro = $_POST['parametro'];
  $criterio = $_POST['criterio'];
  $ListadoTurnos = Listar_Turnos_Parametro($MiConexion,$criterio,$parametro);
  $CantidadTurnos = count($ListadoTurnos);

}

?>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Listado Turnos</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../inicio/index.php">Menu</a></li>
      <li class="breadcrumb-item">Turnos</li>
      <li class="breadcrumb-item active">Listado Turnos</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
    
    <div class="card">
        <div class="card-body">
        <?php 
          //el usuario Admin puede ver todos los turnos
          if ($_SESSION['Usuario_Nivel'] == 1 ) { ?>
            <h5 class="card-title">Listado Turnos</h5>
          <?php } ?>
          <?php 
          //el usuario estilista solo puede ver sus turnos
          if ($_SESSION['Usuario_Nivel'] == 2 ) { ?>
            <h5 class="card-title">Mis Turnos</h5>
          <?php } ?>
          
          <?php if (!empty($_SESSION['Mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
              <?php echo $_SESSION['Mensaje'] ?>
            </div>
          <?php } ?>
            
          <style> .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5; border-radius: 0.2rem; } </style>
          <style> .btn-hidden { display: none; } </style>

          <Form method="POST">
            <div class="row mb-4">
              <label for="inputEmail3" class="col-sm-1 col-form-label">Buscar</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control" name="parametro" id="parametro">
                  </div>
                <div class="col-sm-3 mt-2">
                  <button type="submit" class="btn btn-success btn-xs d-inline-block" value="buscar" name="Buscar">Buscar</button>
                  <button type="submit" class="btn btn-danger btn-xs d-inline-block" value="limpiar" name="Limpiar">Limpiar</button>
                  <button type="button" class="btn btn-primary btn-xs d-inline-block" onclick="toggleDownloadButtons()">Descargar</button> 
                </div>
                <div class="col-sm-5 mt-2">
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios1" value="Cliente" checked>
                        <label class="form-check-label" for="gridRadios1">
                          Cliente
                        </label>
                      </div>
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios2" value="Estilista">
                        <label class="form-check-label" for="gridRadios2">
                          Estilista
                        </label>
                      </div>
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios3" value="Fecha">
                        <label class="form-check-label" for="gridRadios3">
                          Fecha
                      </div>
                      <div class="form-check form-check-inline small-text">
                        <input class="form-check-input" type="radio" name="criterio" id="gridRadios4" value="TipoServicio">
                        <label class="form-check-label" for="gridRadios4">
                          Tipo Servicio
                      </div>
                    </div> 

                <!-- Botones de descarga ocultos --> 
                <div class="row mb-4 btn-hidden" id="downloadButtons"> 
                  <div class="col-sm-3 mt-2">
                    <a href="../descargas/descargar_turnosPDF.php" 
                      class="btn btn-danger btn-xs d-inline-block " 
                      title="PDF"> Descargar PDF  </a> 
                  </div>
                  <div class="col-sm-3 mt-2">
                    <a href="../descargas/descargar_turnosTXT.php" 
                      class="btn btn-warning btn-xs d-inline-block " 
                      title="PDF"> Descargar TXT  </a> 
                  </div> 
                  <div class="col-sm-3 mt-2"> 
                  <a href="../descargas/descargar_turnosXLS.php" 
                      class="btn btn-success btn-xs d-inline-block " 
                      title="PDF"> Descargar XLS </a></div> 
                  <div class="col-sm-3 mt-2"> 
                  <a href="../descargas/descargar_turnosCSV.php" 
                      class="btn btn-info btn-xs d-inline-block " 
                      title="PDF"> Descargar CSV </a>
                  </div> 
                </div>
            </div>

            <!-- Script para que aparezcan los botones --> 
            <script> 
              function toggleDownloadButtons() {
                var buttons = document.getElementById("downloadButtons");
                  if (buttons.classList.contains("btn-hidden")) {
                    buttons.classList.remove("btn-hidden"); 
                    } else { 
                      buttons.classList.add("btn-hidden");
                      } 
                    }
            </script>
          
          </form>
          <!-- Table with stripped rows -->
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Fecha</th>
                <th scope="col">Horario</th>
                <th scope="col">Tipo de Servicio</th>
                <th scope="col">Estilista</th>
                <th scope="col">Cliente</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
                <?php 
                //borro la variable anterior de descarga
                $_SESSION['Descarga']="";
                for ($i=0; $i<$CantidadTurnos; $i++) { 

                  //Metodo para buscar los Tipos de Servicio por el ID
                  $array_ids = explode(',', $ListadoTurnos[$i]['TIPO_SERVICIO']);
                  $cantidadIds = count($array_ids);

                  //Metodo para descargar
                  $_SESSION['Descarga'] .= "Cliente: {$ListadoTurnos[$i]['NOMBRE_C']}, {$ListadoTurnos[$i]['APELLIDO_C']} - Fecha: {$ListadoTurnos[$i]['FECHA']} - Horario: {$ListadoTurnos[$i]['HORARIO']} - Tipo de Servicio: {$ListadoTurnos[$i]['TIPO_SERVICIO']} - Estilista: {$ListadoTurnos[$i]['NOMBRE_E']}, {$ListadoTurnos[$i]['APELLIDO_E']} \n";

                  //Metodo para pintar las filas
                  list($Title, $Color) = ColorDeFila($ListadoTurnos[$i]['FECHA'],$ListadoTurnos[$i]['ESTADO']); 
                ?>
                    <tr class="<?php echo $Color; ?>"  data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?php echo $Title; ?>">
                        <th scope="row"><?php echo $i+1; ?></th>
                        <td><?php echo $ListadoTurnos[$i]['FECHA']; ?></td>
                        <td><?php echo $ListadoTurnos[$i]['HORARIO']; ?></td>
                        <td><?php for ($j = 0; $j<$cantidadIds; $j++) {
                          $indice = (int)$array_ids[$j] - 1;
                          echo $ListadoTiposTurnos[$indice]['DENOMINACION'];
                          if ($j < $cantidadIds - 1){
                            echo ", ";}
                          } 
                          ?></td>
                        <td><?php echo $ListadoTurnos[$i]['NOMBRE_E']?>, <?php echo $ListadoTurnos[$i]['APELLIDO_E']?></td>
                        <td><?php echo $ListadoTurnos[$i]['NOMBRE_C']?>, <?php echo $ListadoTurnos[$i]['APELLIDO_C']?></td>
                        <td>
                          <!-- eliminar la consulta -->
                          <a href="../eliminar/eliminar_turnos.php?ID_TURNO=<?php echo $ListadoTurnos[$i]['ID_TURNO']; ?>" 
                            title="Eliminar" 
                            onclick="return confirm('Confirma eliminar este turno?');">
                            <i class="bi bi-trash-fill text-danger fs-5"></i>
                          </a>

                          <a href="../modificar/modificar_turnos.php?ID_TURNO=<?php echo $ListadoTurnos[$i]['ID_TURNO']; ?>" 
                            title="Modificar">
                            <i class="bi bi-pencil-fill text-warning fs-5"></i>
                          </a>

                          <a href="../descargas/descargar_comp_turnosPDF.php?ID_TURNO=<?php echo $ListadoTurnos[$i]['ID_TURNO']; ?>" 
                            title="Imprimir">
                            <i class="bi bi-printer-fill text-primary fs-5"></i>
                          </a>
                      
                        </td>
                    </tr>
                <?php 
                } 
                //le agrego un espacio cuando termino de cargar
                $_SESSION['Descarga'] .= "\n";
                ?>
            </tbody>
          </table>
          <!-- End Table with stripped rows -->

        </div>
    </div>
 
</section>

</main><!-- End #main -->

<?php
  $_SESSION['Mensaje']='';
  require ('../footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo
?>


</body>

</html>