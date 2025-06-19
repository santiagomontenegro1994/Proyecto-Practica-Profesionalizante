<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
  header('Location: ../inicio/cerrarsesion.php');
  exit;
}

require ('../encabezado.inc.php');
require ('../barraLateral.inc.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Panel de Compras</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../inicio/index.php">Home</a></li>
        <li class="breadcrumb-item active">Panel Compras</li>
      </ol>
    </nav>
  </div>

  <section class="section dashboard">
    <div class="row">
      <div class="col-12 mb-3 d-flex justify-content-between">
        <div>
          <button id="sincronizar-filtros" class="btn btn-sm btn-primary" onclick="toggleSincronizacionFiltros()">
            <i id="sincronizar-filtros-icon" class="bi bi-link"></i> Sincronizar filtros
          </button>
        </div>
        <div>
          <button onclick="generarPDF()" class="btn btn-sm btn-danger me-2">
            <i class="bi bi-file-pdf"></i> PDF
          </button>
          <button onclick="exportarAExcel()" class="btn btn-sm btn-success">
            <i class="bi bi-file-excel"></i> Excel
          </button>
        </div>
      </div>
      
      <!-- Tarjeta 1: Compras Hoy -->
      <div class="col-lg-6 col-md-6">
        <div class="card info-card sales-card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item active" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('semana', this)">Esta semana</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('mes', this)">Este mes</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('anio', this)">Este año</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('personalizado', this)">Personalizado...</a></li>
              <li>
                <div class="px-3 py-2">
                  <div class="mb-2">
                    <label class="form-label small">Fecha inicio</label>
                    <input type="date" class="form-control form-control-sm fecha-inicio">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fecha fin</label>
                    <input type="date" class="form-control form-control-sm fecha-fin">
                  </div>
                  <button class="btn btn-primary btn-sm w-100" onclick="aplicarRangoPersonalizado(this)">Aplicar</button>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <h5 class="card-title">Compras <span id="periodo-comprasHoy">| Hoy</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-cart-check"></i>
              </div>
              <div class="ps-3">
                <h6 id="valor-comprasHoy">0</h6>
                <span class="text-success small pt-1 fw-bold" id="variacion-comprasHoy">0%</span>
                <span class="text-muted small pt-2 ps-1">vs período anterior</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tarjeta 2: Gastos -->
      <div class="col-lg-6 col-md-6">
        <div class="card info-card revenue-card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item active" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('semana', this)">Esta semana</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('mes', this)">Este mes</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('anio', this)">Este año</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('personalizado', this)">Personalizado...</a></li>
              <li>
                <div class="px-3 py-2">
                  <div class="mb-2">
                    <label class="form-label small">Fecha inicio</label>
                    <input type="date" class="form-control form-control-sm fecha-inicio">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fecha fin</label>
                    <input type="date" class="form-control form-control-sm fecha-fin">
                  </div>
                  <button class="btn btn-primary btn-sm w-100" onclick="aplicarRangoPersonalizado(this)">Aplicar</button>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <h5 class="card-title">Gastos <span id="periodo-gastosCompras">| Hoy</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div class="ps-3">
                <h6 id="valor-gastosCompras">$0.00</h6>
                <span class="text-success small pt-1 fw-bold" id="variacion-gastosCompras">0%</span>
                <span class="text-muted small pt-2 ps-1">vs período anterior</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráfico 1: Artículos Más Comprados -->
      <div class="col-lg-6">
        <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item active" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('semana', this)">Esta semana</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('mes', this)">Este mes</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('anio', this)">Este año</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('personalizado', this)">Personalizado...</a></li>
              <li>
                <div class="px-3 py-2">
                  <div class="mb-2">
                    <label class="form-label small">Fecha inicio</label>
                    <input type="date" class="form-control form-control-sm fecha-inicio">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fecha fin</label>
                    <input type="date" class="form-control form-control-sm fecha-fin">
                  </div>
                  <button class="btn btn-primary btn-sm w-100" onclick="aplicarRangoPersonalizado(this)">Aplicar</button>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <h5 class="card-title">Artículos Más Comprados <span id="periodo-articulosChart">| Hoy</span></h5>
            <div id="articulosChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 2: Compras por Proveedor -->
      <div class="col-lg-6">
        <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item active" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('semana', this)">Esta semana</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('mes', this)">Este mes</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('anio', this)">Este año</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('personalizado', this)">Personalizado...</a></li>
              <li>
                <div class="px-3 py-2">
                  <div class="mb-2">
                    <label class="form-label small">Fecha inicio</label>
                    <input type="date" class="form-control form-control-sm fecha-inicio">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fecha fin</label>
                    <input type="date" class="form-control form-control-sm fecha-fin">
                  </div>
                  <button class="btn btn-primary btn-sm w-100" onclick="aplicarRangoPersonalizado(this)">Aplicar</button>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <h5 class="card-title">Compras por Proveedor <span id="periodo-proveedoresChart">| Hoy</span></h5>
            <div id="proveedoresChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 3: Evolución de Compras -->
      <div class="col-lg-12">
        <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item active" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('semana', this)">Esta semana</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('mes', this)">Este mes</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('anio', this)">Este año</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('personalizado', this)">Personalizado...</a></li>
              <li>
                <div class="px-3 py-2">
                  <div class="mb-2">
                    <label class="form-label small">Fecha inicio</label>
                    <input type="date" class="form-control form-control-sm fecha-inicio">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fecha fin</label>
                    <input type="date" class="form-control form-control-sm fecha-fin">
                  </div>
                  <button class="btn btn-primary btn-sm w-100" onclick="aplicarRangoPersonalizado(this)">Aplicar</button>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <h5 class="card-title">Evolución de Compras <span id="periodo-evolucionChart">| Hoy</span></h5>
            <div id="evolucionChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 4: Frecuencia de Compras -->
      <div class="col-lg-12">
        <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item active" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('semana', this)">Esta semana</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('mes', this)">Este mes</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('anio', this)">Este año</a></li>
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('personalizado', this)">Personalizado...</a></li>
              <li>
                <div class="px-3 py-2">
                  <div class="mb-2">
                    <label class="form-label small">Fecha inicio</label>
                    <input type="date" class="form-control form-control-sm fecha-inicio">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fecha fin</label>
                    <input type="date" class="form-control form-control-sm fecha-fin">
                  </div>
                  <button class="btn btn-primary btn-sm w-100" onclick="aplicarRangoPersonalizado(this)">Aplicar</button>
                </div>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <h5 class="card-title">Frecuencia de Compras por Proveedor <span id="periodo-frecuenciaChart">| Hoy</span></h5>
            <div id="frecuenciaChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require ('../footer.inc.php'); ?>

<!-- CDN de ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- SweetAlert para notificaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- SheetJS para exportar Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
// Variables globales para los gráficos y datos
let articulosChart, proveedoresChart, evolucionChart, frecuenciaChart;
let filtrosSincronizados = true;
let datosReporte = {
  compras: [],
  gastos: [],
  articulos: [],
  proveedores: [],
  evolucion: [],
  frecuencia: []
};

// Función para formatear números
function formatNumber(value, decimals = 0) {
  const num = typeof value === 'string' ? parseFloat(value) : value;
  return isNaN(num) ? '0' : num.toFixed(decimals);
}

// Función para manejar la selección de período
function seleccionarPeriodo(periodo, elemento) {
  if (periodo === 'personalizado') return; // Se maneja con el botón
  
  // Marcar como activo en el dropdown
  document.querySelectorAll('.dropdown-item').forEach(item => {
    item.classList.remove('active');
  });
  elemento.classList.add('active');
  
  const card = elemento.closest('.card');
  const tipo = obtenerTipoCard(card);
  
  cargarDatosPorTipo(tipo, periodo);
  
  // Si los filtros están sincronizados, aplicar a todos
  if (filtrosSincronizados) {
    aplicarFiltroGlobal(periodo);
  }
}

// Función para obtener el tipo de card
function obtenerTipoCard(card) {
  if (card.classList.contains('sales-card')) return 'comprasHoy';
  if (card.classList.contains('revenue-card')) return 'gastosCompras';
  if (card.querySelector('#articulosChart')) return 'articulosChart';
  if (card.querySelector('#proveedoresChart')) return 'proveedoresChart';
  if (card.querySelector('#evolucionChart')) return 'evolucionChart';
  return 'frecuenciaChart';
}

// Función para aplicar rango personalizado
function aplicarRangoPersonalizado(boton) {
  const dropdown = boton.closest('.dropdown-menu');
  const fechaInicio = dropdown.querySelector('.fecha-inicio').value;
  const fechaFin = dropdown.querySelector('.fecha-fin').value;
  
  if (!fechaInicio || !fechaFin) {
    mostrarAlerta('Por favor seleccione ambas fechas', 'warning');
    return;
  }
  
  if (new Date(fechaInicio) > new Date(fechaFin)) {
    mostrarAlerta('La fecha de inicio no puede ser mayor a la fecha fin', 'warning');
    return;
  }
  
  const card = boton.closest('.card');
  const tipo = obtenerTipoCard(card);
  
  cargarDatosPorTipo(tipo, 'personalizado', fechaInicio, fechaFin);
  
  // Si los filtros están sincronizados, aplicar a todos
  if (filtrosSincronizados) {
    aplicarFiltroGlobal('personalizado', fechaInicio, fechaFin);
  }
}

// Función unificada para cargar datos
function cargarDatosPorTipo(tipo, periodo, fechaInicio = null, fechaFin = null) {
  // Guardar preferencia de filtro
  guardarPreferenciaFiltro(tipo, periodo, fechaInicio, fechaFin);
  
  if (tipo === 'comprasHoy' || tipo === 'gastosCompras') {
    cargarDatos(tipo, periodo, fechaInicio, fechaFin);
  } else {
    const funcion = tipo === 'articulosChart' ? cargarGraficoArticulos :
                   tipo === 'proveedoresChart' ? cargarGraficoProveedores :
                   tipo === 'evolucionChart' ? cargarGraficoEvolucion : cargarGraficoFrecuencia;
    funcion(periodo, fechaInicio, fechaFin);
  }
}

// Función para cargar datos de las tarjetas
async function cargarDatos(tipo, periodo, fechaInicio = null, fechaFin = null) {
  let url = `../panel_control/get_compras_data.php?tipo=${tipo}&periodo=${periodo}`;
  
  if (periodo === 'personalizado' && fechaInicio && fechaFin) {
    url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
  }
  
  try {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
    
    const data = await response.json();
    
    if (data.error) {
      throw new Error(data.message || 'Error en los datos recibidos');
    }

    if (tipo === 'comprasHoy') {
      document.getElementById('valor-comprasHoy').textContent = formatNumber(data.total);
      document.getElementById('variacion-comprasHoy').textContent = formatNumber(data.variacion, 2) + '%';
      document.getElementById('periodo-comprasHoy').textContent = '| ' + (data.periodo || periodo);
      
      // Actualizar clase según variación
      const variacionElement = document.getElementById('variacion-comprasHoy');
      variacionElement.className = data.variacion >= 0 ? 
        'text-success small pt-1 fw-bold' : 'text-danger small pt-1 fw-bold';
    } 
    else if (tipo === 'gastosCompras') {
      document.getElementById('valor-gastosCompras').textContent = '$' + formatNumber(data.total, 2);
      document.getElementById('variacion-gastosCompras').textContent = formatNumber(data.variacion, 2) + '%';
      document.getElementById('periodo-gastosCompras').textContent = '| ' + (data.periodo || periodo);
      
      // Actualizar clase según variación
      const variacionElement = document.getElementById('variacion-gastosCompras');
      variacionElement.className = data.variacion >= 0 ? 
        'text-success small pt-1 fw-bold' : 'text-danger small pt-1 fw-bold';
    }
  } catch (error) {
    console.error(`Error cargando ${tipo}:`, error);
    const elemento = tipo === 'comprasHoy' ? 'valor-comprasHoy' : 'valor-gastosCompras';
    document.getElementById(elemento).textContent = 'Error';
    document.getElementById(`variacion-${tipo}`).textContent = '';
    
    mostrarAlerta(`Error al cargar datos: ${error.message}`, 'danger');
  }
}

// Función para cargar el gráfico de Artículos Más Comprados
async function cargarGraficoArticulos(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('articulosChart');
  const periodoElement = document.getElementById('periodo-articulosChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_compras_data.php?tipo=articulosChart&periodo=${periodo}`;
    if (periodo === 'personalizado' && fechaInicio && fechaFin) {
      url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    }
    
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    
    const data = await response.json();
    
    container.innerHTML = '';
    periodoElement.textContent = '| ' + (data.periodo || periodo);
    
    if (!data.series || data.series.length === 0 || data.series.every(val => val === 0)) {
      container.innerHTML = `
        <div class="text-center py-4">
          <i class="bi bi-box-seam" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay artículos comprados en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.articulos = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Compras',
        data: data.series
      }],
      chart: {
        type: 'bar',
        height: 350,
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: data.labels,
        title: {
          text: 'Cantidad Comprada'
        }
      },
      yaxis: {
        title: {
          text: 'Artículo'
        }
      },
      colors: ['#3b82f6'],
      tooltip: {
        y: {
          formatter: function(value) {
            return value + ' unidades';
          }
        }
      }
    };
    
    if (articulosChart) {
      articulosChart.destroy();
    }
    
    articulosChart = new ApexCharts(container, options);
    articulosChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Compras por Proveedor
async function cargarGraficoProveedores(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('proveedoresChart');
  const periodoElement = document.getElementById('periodo-proveedoresChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_compras_data.php?tipo=proveedoresChart&periodo=${periodo}`;
    if (periodo === 'personalizado' && fechaInicio && fechaFin) {
      url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    }
    
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    
    const data = await response.json();
    
    container.innerHTML = '';
    periodoElement.textContent = '| ' + (data.periodo || periodo);
    
    if (!data.series || data.series.length === 0 || data.series.every(val => val === 0)) {
      container.innerHTML = `
        <div class="text-center py-4">
          <i class="bi bi-truck" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay datos de proveedores en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.proveedores = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Monto Comprado',
        data: data.series
      }],
      chart: {
        type: 'bar',
        height: 350,
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: data.labels,
        title: {
          text: 'Monto ($)'
        }
      },
      yaxis: {
        title: {
          text: 'Proveedor'
        }
      },
      colors: ['#10b981'],
      tooltip: {
        y: {
          formatter: function(value) {
            return '$' + value.toFixed(2);
          }
        }
      }
    };
    
    if (proveedoresChart) {
      proveedoresChart.destroy();
    }
    
    proveedoresChart = new ApexCharts(container, options);
    proveedoresChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Evolución de Compras
async function cargarGraficoEvolucion(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('evolucionChart');
  const periodoElement = document.getElementById('periodo-evolucionChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_compras_data.php?tipo=evolucionChart&periodo=${periodo}`;
    if (periodo === 'personalizado' && fechaInicio && fechaFin) {
      url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    }
    
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    
    const data = await response.json();
    
    container.innerHTML = '';
    periodoElement.textContent = '| ' + (data.periodo || periodo);
    
    if (!data.series || data.series.length === 0 || data.series.every(val => val === 0)) {
      container.innerHTML = `
        <div class="text-center py-4">
          <i class="bi bi-graph-up" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay datos de evolución en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.evolucion = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Cantidad Comprada',
        data: data.series
      }],
      chart: {
        type: 'line',
        height: 350,
        toolbar: {
          show: false
        }
      },
      stroke: {
        width: 3,
        curve: 'smooth'
      },
      markers: {
        size: 5
      },
      xaxis: {
        categories: data.labels,
        title: {
          text: 'Fecha'
        }
      },
      yaxis: {
        title: {
          text: 'Cantidad'
        }
      },
      colors: ['#6366f1'],
      tooltip: {
        y: {
          formatter: function(value) {
            return value + ' unidades';
          }
        }
      }
    };
    
    if (evolucionChart) {
      evolucionChart.destroy();
    }
    
    evolucionChart = new ApexCharts(container, options);
    evolucionChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Frecuencia de Compras
async function cargarGraficoFrecuencia(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('frecuenciaChart');
  const periodoElement = document.getElementById('periodo-frecuenciaChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_compras_data.php?tipo=frecuenciaChart&periodo=${periodo}`;
    if (periodo === 'personalizado' && fechaInicio && fechaFin) {
      url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    }
    
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    
    const data = await response.json();
    
    container.innerHTML = '';
    periodoElement.textContent = '| ' + (data.periodo || periodo);
    
    if (!data.series || data.series.length === 0 || data.series.every(val => val === 0)) {
      container.innerHTML = `
        <div class="text-center py-4">
          <i class="bi bi-calendar-check" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay datos de frecuencia en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.frecuencia = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Frecuencia',
        data: data.series
      }],
      chart: {
        type: 'bar',
        height: 350,
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: data.labels,
        title: {
          text: 'Número de Compras'
        }
      },
      yaxis: {
        title: {
          text: 'Proveedor'
        }
      },
      colors: ['#ec4899'],
      tooltip: {
        y: {
          formatter: function(value) {
            return value + ' compras';
          }
        }
      }
    };
    
    if (frecuenciaChart) {
      frecuenciaChart.destroy();
    }
    
    frecuenciaChart = new ApexCharts(container, options);
    frecuenciaChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para alternar sincronización de filtros
function toggleSincronizacionFiltros() {
  filtrosSincronizados = !filtrosSincronizados;
  const icono = document.getElementById('sincronizar-filtros-icon');
  const boton = document.getElementById('sincronizar-filtros');
  
  if (filtrosSincronizados) {
    icono.classList.remove('bi-link-45deg');
    icono.classList.add('bi-link');
    boton.classList.remove('btn-outline-primary');
    boton.classList.add('btn-primary');
    
    // Aplicar último filtro usado a todos
    aplicarFiltroGlobal();
  } else {
    icono.classList.remove('bi-link');
    icono.classList.add('bi-link-45deg');
    boton.classList.remove('btn-primary');
    boton.classList.add('btn-outline-primary');
  }
}

// Función para aplicar filtro global
function aplicarFiltroGlobal(periodo = null, fechaInicio = null, fechaFin = null) {
  // Si no se proporcionan parámetros, usar el último filtro guardado
  if (!periodo) {
    const ultimoFiltro = JSON.parse(localStorage.getItem('ultimo_filtro_global'));
    if (!ultimoFiltro) return;
    
    periodo = ultimoFiltro.periodo;
    fechaInicio = ultimoFiltro.fechaInicio;
    fechaFin = ultimoFiltro.fechaFin;
  }
  
  // Guardar como último filtro global
  localStorage.setItem('ultimo_filtro_global', JSON.stringify({
    periodo,
    fechaInicio,
    fechaFin,
    timestamp: new Date().getTime()
  }));
  
  // Aplicar a todos los componentes
  cargarDatosPorTipo('comprasHoy', periodo, fechaInicio, fechaFin);
  cargarDatosPorTipo('gastosCompras', periodo, fechaInicio, fechaFin);
  cargarGraficoArticulos(periodo, fechaInicio, fechaFin);
  cargarGraficoProveedores(periodo, fechaInicio, fechaFin);
  cargarGraficoEvolucion(periodo, fechaInicio, fechaFin);
  cargarGraficoFrecuencia(periodo, fechaInicio, fechaFin);
}

// Función para guardar preferencia de filtro
function guardarPreferenciaFiltro(tipo, periodo, fechaInicio = null, fechaFin = null) {
  const preferencia = {
    periodo,
    fechaInicio,
    fechaFin,
    timestamp: new Date().getTime()
  };
  localStorage.setItem(`filtro_${tipo}`, JSON.stringify(preferencia));
  
  // Guardar también como último filtro global si están sincronizados
  if (filtrosSincronizados) {
    localStorage.setItem('ultimo_filtro_global', JSON.stringify(preferencia));
  }
}

// Función para cargar preferencia de filtro
function cargarPreferenciaFiltro(tipo) {
  const preferencia = JSON.parse(localStorage.getItem(`filtro_${tipo}`));
  if (preferencia) {
    // Verificar si la preferencia es reciente (menos de 1 día)
    const unDia = 24 * 60 * 60 * 1000;
    if (new Date().getTime() - preferencia.timestamp < unDia) {
      return preferencia;
    }
  }
  return null;
}

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo = 'info') {
  Swal.fire({
    icon: tipo,
    title: mensaje,
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });
}

// Función para generar PDF
function generarPDF() {
    // Obtener el período y fechas del último filtro aplicado
    const ultimoFiltro = JSON.parse(localStorage.getItem('ultimo_filtro_global')) || {};
    const periodo = ultimoFiltro.periodo || 'hoy';
    const fechaInicio = ultimoFiltro.fechaInicio || '';
    const fechaFin = ultimoFiltro.fechaFin || '';
    
    // Mostrar spinner mientras se genera
    Swal.fire({
        title: 'Generando PDF',
        html: '<div class="spinner-border text-primary" role="status"></div>',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    
    // Crear formulario dinámico
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'imprimir_panel_compras.php';
    form.target = '_blank';
    
    // Agregar parámetros como inputs ocultos
    const params = {
        'periodo': periodo
    };
    
    if (fechaInicio && fechaFin) {
        params.fecha_inicio = fechaInicio;
        params.fecha_fin = fechaFin;
    }
    
    Object.keys(params).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Cerrar spinner después de un tiempo
    setTimeout(() => Swal.close(), 2000);
}

// Función para exportar a Excel
function exportarAExcel() {
  // Recolectar todos los datos necesarios
  const reporteData = {
    fecha: new Date().toLocaleDateString(),
    periodo: document.getElementById('periodo-comprasHoy').textContent.replace('| ', ''),
    comprasHoy: document.getElementById('valor-comprasHoy').textContent,
    variacionCompras: document.getElementById('variacion-comprasHoy').textContent,
    gastos: document.getElementById('valor-gastosCompras').textContent,
    variacionGastos: document.getElementById('variacion-gastosCompras').textContent,
    articulos: datosReporte.articulos,
    proveedores: datosReporte.proveedores,
    evolucion: datosReporte.evolucion,
    frecuencia: datosReporte.frecuencia
  };

  // Crear libro de Excel
  const wb = XLSX.utils.book_new();
  
  // Hoja de resumen
  const resumen = [
    ["REPORTE DE COMPRAS"],
    ["Fecha de generación", reporteData.fecha],
    ["Período", reporteData.periodo],
    [],
    ["Métrica", "Valor"],
    ["Compras hoy", reporteData.comprasHoy],
    ["Variación compras", reporteData.variacionCompras],
    ["Gastos", reporteData.gastos],
    ["Variación gastos", reporteData.variacionGastos]
  ];
  
  // Hoja de artículos
  const articulos = [
    ["Artículos Más Comprados"],
    ["Artículo", "Cantidad", "Porcentaje"]
  ];
  const totalArticulos = reporteData.articulos.reduce((sum, item) => sum + item.value, 0);
  reporteData.articulos.forEach(item => {
    articulos.push([
      item.label,
      item.value,
      totalArticulos > 0 ? (item.value / totalArticulos * 100).toFixed(2) + '%' : '0%'
    ]);
  });
  
  // Hoja de proveedores
  const proveedores = [
    ["Compras por Proveedor"],
    ["Proveedor", "Monto ($)", "Porcentaje"]
  ];
  const totalProveedores = reporteData.proveedores.reduce((sum, item) => sum + item.value, 0);
  reporteData.proveedores.forEach(item => {
    proveedores.push([
      item.label,
      item.value,
      totalProveedores > 0 ? (item.value / totalProveedores * 100).toFixed(2) + '%' : '0%'
    ]);
  });
  
  // Hoja de evolución
  const evolucion = [
    ["Evolución de Compras"],
    ["Fecha", "Cantidad", "Porcentaje"]
  ];
  const totalEvolucion = reporteData.evolucion.reduce((sum, item) => sum + item.value, 0);
  reporteData.evolucion.forEach(item => {
    evolucion.push([
      item.label,
      item.value,
      totalEvolucion > 0 ? (item.value / totalEvolucion * 100).toFixed(2) + '%' : '0%'
    ]);
  });
  
  // Hoja de frecuencia
  const frecuencia = [
    ["Frecuencia de Compras"],
    ["Proveedor", "Número de Compras", "Porcentaje"]
  ];
  const totalFrecuencia = reporteData.frecuencia.reduce((sum, item) => sum + item.value, 0);
  reporteData.frecuencia.forEach(item => {
    frecuencia.push([
      item.label,
      item.value,
      totalFrecuencia > 0 ? (item.value / totalFrecuencia * 100).toFixed(2) + '%' : '0%'
    ]);
  });

  // Añadir hojas al libro
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(resumen), "Resumen");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(articulos), "Artículos");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(proveedores), "Proveedores");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(evolucion), "Evolución");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(frecuencia), "Frecuencia");

  // Generar archivo
  XLSX.writeFile(wb, `reporte_compras_${new Date().toISOString().split('T')[0]}.xlsx`);
  
  // Mostrar notificación
  Swal.fire(
    'Exportado a Excel',
    'El reporte se ha descargado correctamente con todos los datos.',
    'success'
  );
}

// Cargar datos iniciales al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  // Verificar si hay preferencias guardadas
  const preferenciaCompras = cargarPreferenciaFiltro('comprasHoy');
  const preferenciaGlobal = JSON.parse(localStorage.getItem('ultimo_filtro_global'));
  
  if (preferenciaGlobal && filtrosSincronizados) {
    // Cargar con el último filtro global
    aplicarFiltroGlobal(
      preferenciaGlobal.periodo,
      preferenciaGlobal.fechaInicio,
      preferenciaGlobal.fechaFin
    );
  } else if (preferenciaCompras && !filtrosSincronizados) {
    // Cargar cada componente con su propia preferencia
    cargarDatosPorTipo(
      'comprasHoy',
      preferenciaCompras.periodo,
      preferenciaCompras.fechaInicio,
      preferenciaCompras.fechaFin
    );
    
    const preferenciaGastos = cargarPreferenciaFiltro('gastosCompras') || preferenciaCompras;
    cargarDatosPorTipo(
      'gastosCompras',
      preferenciaGastos.periodo,
      preferenciaGastos.fechaInicio,
      preferenciaGastos.fechaFin
    );
    
    const preferenciaArticulos = cargarPreferenciaFiltro('articulosChart') || preferenciaCompras;
    cargarGraficoArticulos(
      preferenciaArticulos.periodo,
      preferenciaArticulos.fechaInicio,
      preferenciaArticulos.fechaFin
    );
    
    const preferenciaProveedores = cargarPreferenciaFiltro('proveedoresChart') || preferenciaCompras;
    cargarGraficoProveedores(
      preferenciaProveedores.periodo,
      preferenciaProveedores.fechaInicio,
      preferenciaProveedores.fechaFin
    );
    
    const preferenciaEvolucion = cargarPreferenciaFiltro('evolucionChart') || preferenciaCompras;
    cargarGraficoEvolucion(
      preferenciaEvolucion.periodo,
      preferenciaEvolucion.fechaInicio,
      preferenciaEvolucion.fechaFin
    );
    
    const preferenciaFrecuencia = cargarPreferenciaFiltro('frecuenciaChart') || preferenciaCompras;
    cargarGraficoFrecuencia(
      preferenciaFrecuencia.periodo,
      preferenciaFrecuencia.fechaInicio,
      preferenciaFrecuencia.fechaFin
    );
  } else {
    // Cargar con valores por defecto (hoy)
    cargarDatos('comprasHoy', 'hoy');
    cargarDatos('gastosCompras', 'hoy');
    cargarGraficoArticulos('hoy');
    cargarGraficoProveedores('hoy');
    cargarGraficoEvolucion('hoy');
    cargarGraficoFrecuencia('hoy');
  }
  
  // Configurar fecha mínima/máxima en los inputs de fecha
  const hoy = new Date().toISOString().split('T')[0];
  document.querySelectorAll('.fecha-inicio, .fecha-fin').forEach(input => {
    input.setAttribute('max', hoy);
    if (input.classList.contains('fecha-inicio')) {
      // Establecer fecha inicio por defecto (hace 1 mes)
      const haceUnMes = new Date();
      haceUnMes.setMonth(haceUnMes.getMonth() - 1);
      input.value = haceUnMes.toISOString().split('T')[0];
    } else {
      // Establecer fecha fin por defecto (hoy)
      input.value = hoy;
    }
  });
});
</script>