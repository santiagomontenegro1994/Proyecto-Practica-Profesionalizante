<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: cerrarsesion.php');
  exit;
}

require ('encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo

?>


<main id="main" class="main">

<div class="pagetitle">
  <h1>Turnos</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Panel Turnos</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->

      <div class="row">

        <!-- Turnos Card -->
        <div class="col-6 col-md-6">
          <div class="card info-card sales-card">
              <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                      <li class="dropdown-header text-start">
                          <h6>Filtro</h6>
                      </li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('hoy', 'turnos')">Hoy</a></li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('semana', 'turnos')">Semana</a></li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('mes', 'turnos')">Mes</a></li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('año', 'turnos')">Año</a></li>
                  </ul>
              </div>

              <div class="card-body">
                  <h5 class="card-title">Turnos Completados<span id="periodo-turnos">|--</span></h5>
                  <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-scissors"></i>
                      </div>
                      <div class="ps-3">
                          <h6 id="totalTurnos">--</h6>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        <!-- End Turnos Card -->

        <!-- Ganancia Card -->
        <div class="col-6 col-md-6">
          <div class="card info-card revenue-card">
              <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                      <li class="dropdown-header text-start">
                          <h6>Filtro</h6>
                      </li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('hoy', 'ganancia')">Hoy</a></li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('semana', 'ganancia')">Semana</a></li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('mes', 'ganancia')">Mes</a></li>
                      <li><a class="dropdown-item" href="#" onclick="cambiarFiltro('año', 'ganancia')">Año</a></li>
                  </ul>
              </div>

              <div class="card-body">
                  <h5 class="card-title">Ganancia <span id="periodo-ganancia">| --</span></h5>
                  <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-currency-dollar"></i>
                      </div>
                      <div class="ps-3">
                          <h6 id="totalGanancia">--</h6>
                      </div>
                  </div>
              </div>
          </div>
        </div><!-- End Ganancia Card -->

        <!-- Reports -->
        <div class="col-12">
            <div class="card">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filtro</h6>
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="cambiarFiltroReporte('hoy', 'reportes')">Hoy</a></li>
                        <li><a class="dropdown-item" href="#" onclick="cambiarFiltroReporte('semana', 'reportes')">Semana</a></li>
                        <li><a class="dropdown-item" href="#" onclick="cambiarFiltroReporte('mes', 'reportes')">Mes</a></li>
                        <li><a class="dropdown-item" href="#" onclick="cambiarFiltroReporte('año', 'reportes')">Año</a></li>
                    </ul>
                </div>

                <div class="card-body">
                    <h5 class="card-title">Reportes <span id="periodo-reportes">/--</span></h5>
                    <!-- Line Chart -->
                    <div id="reportsChart"></div>
                </div>
            </div>
        </div>
        <!-- End Reports -->

        <!-- Top Selling 
        <div class="col-12">
          <div class="card top-selling overflow-auto">

            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filtro</h6>
                </li>

                <li><a class="dropdown-item" href="#">Hoy</a></li>
                <li><a class="dropdown-item" href="#">Semana</a></li>
                <li><a class="dropdown-item" href="#">Mes</a></li>
                <li><a class="dropdown-item" href="#">Año</a></li>
              </ul>
            </div>

            <div class="card-body pb-0">
              <h5 class="card-title">Servicio Mas pedido<span>| Hoy</span></h5>

              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th scope="col">Preview</th>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Sold</th>
                    <th scope="col">Revenue</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row"><a href="#"><img src="" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas nulla</a></td>
                    <td>$64</td>
                    <td class="fw-bold">124</td>
                    <td>$5,828</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Exercitationem similique doloremque</a></td>
                    <td>$46</td>
                    <td class="fw-bold">98</td>
                    <td>$4,508</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Doloribus nisi exercitationem</a></td>
                    <td>$59</td>
                    <td class="fw-bold">74</td>
                    <td>$4,366</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum error</a></td>
                    <td>$32</td>
                    <td class="fw-bold">63</td>
                    <td>$2,016</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus repellendus</a></td>
                    <td>$79</td>
                    <td class="fw-bold">41</td>
                    <td>$3,239</td>
                  </tr>
                </tbody>
              </table>

            </div>

          </div>
        </div>
         End Top Selling -->

      </div>


  </div>
</section>

</main><!-- End #main -->

<?php
require ('footer.inc.php'); //Aca uso el FOOTER que esta seccionados en otro archivo

?>

</body>

</html>