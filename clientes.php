<?php
require ('encabezado.inc.php'); //Aca uso el encabezado que esta seccionados en otro archivo

require ('barraLateral.inc.php'); //Aca uso el encabezaso que esta seccionados en otro archivo
?>


  

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>
        Listado de Paquetes de Viajes </h1>

      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Inicio</a></li>
          <li class="breadcrumb-item"><a href="#">Paquetes</a></li>
          <li class="breadcrumb-item active">Disponibles</li>


        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Top Selling -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">

                <div class="card-body pb-0">
                  <h5 class="card-title">Los mas vendidos </h5>

                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Destino</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Precio B/D</th>
                        <th scope="col">Precio B/T</th>
                        <th scope="col">Info Paquetes</th>
                        <th scope="col">Info Pasajes</th>
                        <th scope="col">Total de ventas</th>
                      </tr>
                    </thead>


                    <tbody>

                    <!-- DENTRO DEL FOR -->
                    <?php
                        for ($i = 0; $i < $CantidadPaquetes; $i++) {

                            ?>
                      <!-- IF  con contador de Nacionales y acumulador de paquetes N e I vendidos y variable de impuestos-->
                    <?php
                      $TotalVendidos=$Paquetes[$i]['TotalVendidosBD']+$Paquetes[$i]['TotalVendidosBT']; 
                      if ($Paquetes[$i]['Dato']=="Nacional") { 
                        $Nacional++;
                        $PaquetesNVendidos+=$Paquetes[$i]['TotalVendidosBD'];
                        $PaquetesNVendidos+=$Paquetes[$i]['TotalVendidosBT'];
                        $Impuestos="";
                        $Marcador="info";
                        $PrecioFinalD=$Paquetes[$i]['PrecioBD']; //Title del precio final DB
                        $PrecioFinalT=$Paquetes[$i]['PrecioBD']*0.9; //Title del precio final DT
                        $EsNAcional=true;//Si es Nacional se pone True
                      }else{
                        $Internacional++;
                        $PaquetesIVendidos+=$Paquetes[$i]['TotalVendidosBD'];
                        $PaquetesIVendidos+=$Paquetes[$i]['TotalVendidosBT'];
                        $Impuestos=" + Imp. U$  169,00";
                        $Marcador="danger";
                        $PrecioFinalD=$Paquetes[$i]['PrecioBD']+169; //Title del precio final DB
                        $PrecioFinalT=($Paquetes[$i]['PrecioBD']*0.9)+169; //Title del precio final DT
                        $EsNAcional=false;//Si no es Nacional se pone false
                      }
                      
                      ?>

                      <tr>

                        <th scope="row"> <?php echo ($i+1);?></th>
                        <th scope="row">
                          <a href="#"><img src="assets/img/<?php echo $Paquetes[$i]['Codigo']; ?>.jpg" data-bs-placement="left" data-bs-toggle="tooltip"
                              data-bs-original-title="<?php echo $Paquetes[$i]['Codigo']; ?>" /></a>
                          <br />
                          <i class="bi bi-bookmark-star-fill text-<?php echo $Marcador; ?>"></i>
                          <?php echo $Destino = strtoupper($Paquetes[$i]['NombreDestino']); ?>
                        </th>

                        <td>
                          <a href="#" class="text-primary fw-bold">
                          <?php echo substr($Paquetes[$i]['Descripcion'],0,50);echo $Puntos; ?>
                          </a>
                        </td>

                        <td>
                          <h6>

                            <span title="Precio Final: <?php echo $PrecioFinalD; ?>">
                              U$S <?php echo number_format($Paquetes[$i]['PrecioBD'],2);echo $Impuestos; ?> </span>
                          </h6>
                        </td>

                        <td>
                          <h6>      
                              
                            <?php 
                              //calcula el precio de BT con la funcion                             
                              $PrecioBT = PrecioBaseTriple($Paquetes[$i]['PrecioBD']);
                            ?>  
                            <span title="Precio Final: <?php echo $PrecioFinalT; ?>">
                              U$S <?php echo number_format($PrecioBT,2); echo $Impuestos;?>
                            </span>


                          </h6>
                        </td>

                        <td>
                          <h5>
                            <!-- info paquetes -->        

                            <span class="badge border-success border-1 text-<?php echo ColorTexto($TotalVendidos,$Paquetes[$i]['TotalDisponibles']); ?>">
                              B/D vendidos : <?php echo $Paquetes[$i]['TotalVendidosBD']; ?> </span>


                            <span class="badge border-success border-1 text-<?php echo ColorTexto($TotalVendidos,$Paquetes[$i]['TotalDisponibles']); ?>">
                              B/T vendidos: <?php echo $Paquetes[$i]['TotalVendidosBT']; ?> </span>

                            <span class="badge border-info border-1 text-info" title="Total paquetes vendidos: <?php echo Sumar($Paquetes[$i]['TotalVendidosBD'],$Paquetes[$i]['TotalVendidosBT']); ?>">
                              Paquetes disponibles: <?php echo $Paquetes[$i]['TotalDisponibles']; ?> </span>
                          </h5>

                        </td>

                        <td>
                          <!-- info pasajes -->
                          <h5>
                            <?php 
                                //calcula el total del BD Vendidos
                                $BDVendidos = PasajesVendidos($Paquetes[$i]['TotalVendidosBD'],"D"); ?>
                            <span class="badge border-info border-1 text-info">
                              B/D Vendidos <?php echo $BDVendidos; ?> </span>
                            
                            <?php 
                                //calcula el total del BT Vendidos
                                $BTVendidos = PasajesVendidos($Paquetes[$i]['TotalVendidosBT'],"T"); ?>  
                            <span class="badge border-info border-1 text-info">
                              B/T Vendidos <?php echo $BTVendidos; ?> </span>

                            <?php 
                                //calcula el total de Vendidos
                                $TotalVendidos = $BDVendidos+$BTVendidos; ?> 
                            <span class="badge border-info border-1 text-info">
                              Total pasajes vendidos: <?php echo $TotalVendidos ?> </span>


                          </h5>




                        </td>

                        <td>
                          <h4>
                            <?php 
                              //calcula el dinero DB si es nacional no se le suma el impuesto
                              if($EsNAcional==True){
                              $DineroBD = ($Paquetes[$i]['PrecioBD'])*$BDVendidos; 
                              }else{
                              $DineroBD = ($Paquetes[$i]['PrecioBD']+169)*$BDVendidos;
                              }
                              ?>
                            <span class="badge border-info border-1 text-info"
                              title="Precio Final U$S<?php echo $PrecioFinalD; ?> * Cant. Pasajes vendidos (<?php echo $BDVendidos; ?> )">
                              B/D: U$S <?php echo $DineroBD; ?> </span>

                            <?php 
                              //calcula el dinero DT
                              if($EsNAcional==True){
                                $DineroBT = ($PrecioBT)*$BTVendidos; 
                                }else{
                                $DineroBT = ($PrecioBT+169)*$BTVendidos;
                                }
                               ?>
                            <span class="badge border-info border-1 text-info"
                              title="Precio Final U$S<?php echo $PrecioFinalT; ?> * Cant. Pasajes vendidos (<?php echo $BTVendidos; ?> )">
                              B/T: U$S <?php echo $DineroBT; ?> </span>

                            <?php 
                              //calcula el dinero Total
                              $DineroTotal = $DineroBD + $DineroBT; 
                              $FinalVentas+=$DineroTotal // voy acumulando los totales en la variable FinalVentas
                              ?> 
                            <span class="badge border-info border-1 text-info" title="(<?php echo $DineroBD; ?> + <?php echo $DineroBT; ?> ) ">
                              TOTAL U$S <?php echo $DineroTotal; ?> </span> 

                          </h4>
                        </td>
                      </tr>
                      <?php } //fin del FOR ?>
                    <!-- DENTRO DEL FOR -->


                    </tbody>
                  </table>

                </div>



              </div>
            </div><!-- End Top Selling -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">
                    DESTINOS <span>| Cantidad Internacionales</span> </h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $Internacional; ?></h6>
                      <h5>Paquetes vendidos: <?php echo $PaquetesIVendidos; ?></h5>

                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">
                    DESTINOS <span>| Cantidad Nacionales</span> </h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $Nacional; ?></h6>
                      <h5>Paquetes vendidos: <?php echo $PaquetesNVendidos; ?></h5>

                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                <div class="card-body"><!--  Stock Actual * Precio -->
                  <h5 class="card-title">
                    Total <span>| Final de Ventas</span> </h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="ps-3">
                      <h6>U$S <?php echo $FinalVentas; ?></h6>
                    </div>
                  </div>
                </div>

              </div>
            </div>


          </div><!-- End Left side columns -->
        </div>

    </section>

  </main><!-- End #main -->

  <?php
require ('footer.inc.php'); //Aca uso el footer que esta seccionados en otro archivo

?>


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files 2023-->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Template Main JS File 2023-->
  <script src="assets/js/main.js"></script>

</body>

</html>