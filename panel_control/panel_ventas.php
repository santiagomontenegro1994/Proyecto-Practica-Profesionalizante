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
    <h1>Panel de Ventas</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../inicio/index.php">Home</a></li>
        <li class="breadcrumb-item active">Panel Ventas</li>
      </ol>
    </nav>
  </div>

  <style>
    .markdown-content {
      line-height: 1.6;
    }
    .markdown-content h3 {
      color: #3b82f6;
      margin-top: 1.5rem;
      margin-bottom: 1rem;
      font-size: 1.25rem;
    }
    .markdown-content h4 {
      color: #4b5563;
      margin-top: 1.25rem;
      margin-bottom: 0.75rem;
      font-size: 1.1rem;
    }
    .markdown-content ul {
      padding-left: 1.5rem;
      margin-bottom: 1rem;
    }
    .markdown-content li {
      margin-bottom: 0.5rem;
    }
    .markdown-content strong {
      font-weight: 600;
      color: #1f2937;
    }
  </style>

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
      
      <!-- Tarjeta 1: Ventas Hoy -->
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
            <h5 class="card-title">Ventas <span id="periodo-ventasHoy">| Hoy</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-cash-coin"></i>
              </div>
              <div class="ps-3">
                <h6 id="valor-ventasHoy">0</h6>
                <span class="text-success small pt-1 fw-bold" id="variacion-ventasHoy">0%</span>
                <span class="text-muted small pt-2 ps-1">vs período anterior</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tarjeta 2: Ingresos -->
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
            <h5 class="card-title">Ingresos <span id="periodo-ingresosVentas">| Hoy</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div class="ps-3">
                <h6 id="valor-ingresosVentas">$0.00</h6>
                <span class="text-success small pt-1 fw-bold" id="variacion-ingresosVentas">0%</span>
                <span class="text-muted small pt-2 ps-1">vs período anterior</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráfico 1: Productos Más Vendidos -->
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
            <h5 class="card-title">Productos Más Vendidos <span id="periodo-productosChart">| Hoy</span></h5>
            <div id="productosChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 2: Clientes Destacados -->
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
            <h5 class="card-title">Clientes Destacados <span id="periodo-clientesChart">| Hoy</span></h5>
            <div id="clientesChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 3: Rendimiento por Empleado -->
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
            <h5 class="card-title">Rendimiento por Empleado <span id="periodo-empleadosChart">| Hoy</span></h5>
            <div id="empleadosChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 4: Ventas por Día -->
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
            <h5 class="card-title">Ventas por Día <span id="periodo-diasChart">| Hoy</span></h5>
            <div id="diasChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Sección de Recomendaciones Inteligentes -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Recomendaciones Inteligentes <span id="periodo-recomendaciones">| Hoy</span></h5>
            <div id="recomendacionesContainer">
              <div class="text-center py-4">
                <i class="bi bi-robot" style="font-size: 2rem; color: #6c757d;"></i>
                <p class="text-muted mt-2">Haz clic en el botón para obtener recomendaciones basadas en tus datos de ventas</p>
                <button id="btnObtenerRecomendaciones" class="btn btn-primary" onclick="obtenerRecomendaciones()">
                  <i class="bi bi-magic"></i> Obtener Recomendaciones
                </button>
              </div>
              <div id="recomendacionesContent" class="d-none">
                <div class="d-flex justify-content-end mb-3">
                  <button class="btn btn-sm btn-outline-primary" onclick="obtenerRecomendaciones()">
                    <i class="bi bi-arrow-repeat"></i> Actualizar
                  </button>
                </div>
                <div id="recomendacionesMarkdown" class="markdown-content"></div>
              </div>
              <div id="recomendacionesLoading" class="text-center py-5 d-none">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Analizando datos y generando recomendaciones...</p>
              </div>
            </div>
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
let productosChart, clientesChart, empleadosChart, diasChart;
let filtrosSincronizados = true;
let datosReporte = {
  ventas: [],
  ingresos: [],
  productos: [],
  clientes: [],
  empleados: [],
  dias: []
};

// Función para formatear números
function formatNumber(value, decimals = 2) {
  const num = typeof value === 'string' ? parseFloat(value) : value;
  if (isNaN(num)) return '0,00';
  
  return num.toLocaleString('es-AR', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  });
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
  if (card.classList.contains('sales-card')) return 'ventasHoy';
  if (card.classList.contains('revenue-card')) return 'ingresosVentas';
  if (card.querySelector('#productosChart')) return 'productosChart';
  if (card.querySelector('#clientesChart')) return 'clientesChart';
  if (card.querySelector('#empleadosChart')) return 'empleadosChart';
  return 'diasChart';
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
  
  if (tipo === 'ventasHoy' || tipo === 'ingresosVentas') {
    cargarDatos(tipo, periodo, fechaInicio, fechaFin);
  } else {
    const funcion = tipo === 'productosChart' ? cargarGraficoProductos :
                   tipo === 'clientesChart' ? cargarGraficoClientes :
                   tipo === 'empleadosChart' ? cargarGraficoEmpleados : cargarGraficoDias;
    funcion(periodo, fechaInicio, fechaFin);
  }
}

// Función para cargar datos de las tarjetas
async function cargarDatos(tipo, periodo, fechaInicio = null, fechaFin = null) {
  let url = `../panel_control/get_ventas_data.php?tipo=${tipo}&periodo=${periodo}`;
  
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

    if (tipo === 'ventasHoy') {
      document.getElementById('valor-ventasHoy').textContent = formatNumber(data.total);
      document.getElementById('variacion-ventasHoy').textContent = formatNumber(data.variacion, 2) + '%';
      document.getElementById('periodo-ventasHoy').textContent = '| ' + (data.periodo || periodo);
      
      // Actualizar clase según variación
      const variacionElement = document.getElementById('variacion-ventasHoy');
      variacionElement.className = data.variacion >= 0 ? 
        'text-success small pt-1 fw-bold' : 'text-danger small pt-1 fw-bold';
    } 
    else if (tipo === 'ingresosVentas') {
      document.getElementById('valor-ingresosVentas').textContent = '$' + formatNumber(data.total, 2);
      document.getElementById('variacion-ingresosVentas').textContent = formatNumber(data.variacion, 2) + '%';
      document.getElementById('periodo-ingresosVentas').textContent = '| ' + (data.periodo || periodo);
      
      // Actualizar clase según variación
      const variacionElement = document.getElementById('variacion-ingresosVentas');
      variacionElement.className = data.variacion >= 0 ? 
        'text-success small pt-1 fw-bold' : 'text-danger small pt-1 fw-bold';
    }
  } catch (error) {
    console.error(`Error cargando ${tipo}:`, error);
    const elemento = tipo === 'ventasHoy' ? 'valor-ventasHoy' : 'valor-ingresosVentas';
    document.getElementById(elemento).textContent = 'Error';
    document.getElementById(`variacion-${tipo}`).textContent = '';
    
    mostrarAlerta(`Error al cargar datos: ${error.message}`, 'danger');
  }
}

// Función para cargar el gráfico de Productos Más Vendidos
async function cargarGraficoProductos(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('productosChart');
  const periodoElement = document.getElementById('periodo-productosChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_ventas_data.php?tipo=productosChart&periodo=${periodo}`;
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
          <p class="text-muted mt-2">No hay productos vendidos en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.productos = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Ventas',
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
          text: 'Cantidad Vendida'
        }
      },
      yaxis: {
        title: {
          text: 'Producto'
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
    
    if (productosChart) {
      productosChart.destroy();
    }
    
    productosChart = new ApexCharts(container, options);
    productosChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Clientes Destacados
async function cargarGraficoClientes(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('clientesChart');
  const periodoElement = document.getElementById('periodo-clientesChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_ventas_data.php?tipo=clientesChart&periodo=${periodo}`;
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
          <i class="bi bi-people" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay datos de clientes en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.clientes = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Gasto Total',
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
          text: 'Monto Gastado ($)'
        }
      },
      yaxis: {
        title: {
          text: 'Cliente'
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
    
    if (clientesChart) {
      clientesChart.destroy();
    }
    
    clientesChart = new ApexCharts(container, options);
    clientesChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Rendimiento por Empleado
async function cargarGraficoEmpleados(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('empleadosChart');
  const periodoElement = document.getElementById('periodo-empleadosChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_ventas_data.php?tipo=empleadosChart&periodo=${periodo}`;
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
          <i class="bi bi-person-badge" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay datos de empleados en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.empleados = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Ventas Generadas',
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
          text: 'Monto Generado ($)'
        }
      },
      yaxis: {
        title: {
          text: 'Empleado'
        }
      },
      colors: ['#6366f1'],
      tooltip: {
        y: {
          formatter: function(value) {
            return '$' + value.toFixed(2);
          }
        }
      }
    };
    
    if (empleadosChart) {
      empleadosChart.destroy();
    }
    
    empleadosChart = new ApexCharts(container, options);
    empleadosChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Ventas por Día
async function cargarGraficoDias(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('diasChart');
  const periodoElement = document.getElementById('periodo-diasChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_ventas_data.php?tipo=diasChart&periodo=${periodo}`;
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
          <i class="bi bi-calendar-week" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay datos de ventas por día en este período</p>
        </div>
      `;
      return;
    }
    
    // Guardar datos para reportes
    datosReporte.dias = data.labels.map((label, index) => ({
      label: label,
      value: data.series[index]
    }));
    
    const options = {
      series: [{
        name: 'Ventas',
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
          text: 'Monto ($)'
        }
      },
      colors: ['#ec4899'],
      tooltip: {
        y: {
          formatter: function(value) {
            return '$' + value.toFixed(2);
          }
        }
      }
    };
    
    if (diasChart) {
      diasChart.destroy();
    }
    
    diasChart = new ApexCharts(container, options);
    diasChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para convertir markdown a HTML
function convertirMarkdownAHTML(markdown) {
  if (!markdown) return '';
  
  return markdown
    .replace(/^# (.*$)/gm, '<h3>$1</h3>')
    .replace(/^## (.*$)/gm, '<h4>$1</h4>')
    .replace(/^- (.*$)/gm, '<li>$1</li>')
    .replace(/(<li>.*<\/li>)+/g, '<ul>$&</ul>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\n\n/g, '<br><br>');
}

// Función para obtener recomendaciones
async function obtenerRecomendaciones() {
  const container = document.getElementById('recomendacionesContainer');
  const content = document.getElementById('recomendacionesContent');
  const loading = document.getElementById('recomendacionesLoading');
  const markdown = document.getElementById('recomendacionesMarkdown');
  const btnObtener = document.getElementById('btnObtenerRecomendaciones');

  // Obtener período actual
  const ultimoFiltro = JSON.parse(localStorage.getItem('ultimo_filtro_global')) || {};
  const periodo = ultimoFiltro.periodo || 'hoy';
  const fechaInicio = ultimoFiltro.fechaInicio || null;
  const fechaFin = ultimoFiltro.fechaFin || null;

  // Deshabilitar botón y mostrar loading
  btnObtener.disabled = true;
  container.querySelector('.text-center').classList.add('d-none');
  content.classList.add('d-none');
  loading.classList.remove('d-none');

  // Definir requestUrl aquí para que esté disponible en el catch
  let requestUrl = `../panel_control/get_recomendaciones_ventas.php?periodo=${periodo}`;
  if (periodo === 'personalizado' && fechaInicio && fechaFin) {
    requestUrl += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
  }
  requestUrl += `&_=${Date.now()}`; // Evitar caché

  try {
    console.log("Solicitando recomendaciones a:", requestUrl);
    
    // Hacer la solicitud con timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 45000); // 45 segundos timeout
    
    const response = await fetch(requestUrl, {
      signal: controller.signal
    });
    
    clearTimeout(timeoutId);

    // Verificar si la respuesta es JSON
    const contentType = response.headers.get('content-type') || '';
    const isJson = contentType.includes('application/json');
    const rawResponse = await response.text();

    console.log("Respuesta del servidor:", {
      status: response.status,
      contentType: contentType,
      body: rawResponse.substring(0, 300) + (rawResponse.length > 300 ? '...' : '')
    });

    // Si no es JSON, manejar error
    if (!isJson) {
      throw new Error('El servidor respondió con un formato inesperado');
    }

    // Parsear JSON
    const data = JSON.parse(rawResponse);
    
    if (!response.ok || data.error) {
      throw new Error(data.message || 'Error al obtener recomendaciones');
    }

    // Mostrar resultados
    document.getElementById('periodo-recomendaciones').textContent = '| ' + (data.periodo || periodo);
    markdown.innerHTML = convertirMarkdownAHTML(data.recomendaciones);
    
    loading.classList.add('d-none');
    content.classList.remove('d-none');

  } catch (error) {
    console.error('Error completo:', error);
    loading.classList.add('d-none');
    container.querySelector('.text-center').classList.remove('d-none');
    
    let errorMessage = error.message;
    if (error.name === 'AbortError') {
      errorMessage = 'La solicitud tardó demasiado. Por favor intenta nuevamente.';
    }

    // Mostrar error al usuario
    Swal.fire({
      icon: 'error',
      title: 'Error al obtener recomendaciones',
      html: `
        <div class="text-start">
          <p><strong>${errorMessage}</strong></p>
          <details class="mt-2">
            <summary class="text-primary cursor-pointer small">Detalles técnicos</summary>
            <div class="alert alert-light small mt-2">
              <strong>Error:</strong> ${error.message}<br>
              <strong>Tipo:</strong> ${error.name}<br>
              <strong>URL:</strong> ${requestUrl || 'No disponible'}<br>
            </div>
          </details>
        </div>
      `,
      confirmButtonText: 'Entendido'
    });
  } finally {
    btnObtener.disabled = false;
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
  cargarDatosPorTipo('ventasHoy', periodo, fechaInicio, fechaFin);
  cargarDatosPorTipo('ingresosVentas', periodo, fechaInicio, fechaFin);
  cargarGraficoProductos(periodo, fechaInicio, fechaFin);
  cargarGraficoClientes(periodo, fechaInicio, fechaFin);
  cargarGraficoEmpleados(periodo, fechaInicio, fechaFin);
  cargarGraficoDias(periodo, fechaInicio, fechaFin);
  
  // Si hay recomendaciones visibles, actualizarlas
  if (!document.getElementById('recomendacionesContent').classList.contains('d-none')) {
    obtenerRecomendaciones();
  }
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
    form.action = 'imprimir_panel_ventas.php';
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
    periodo: document.getElementById('periodo-ventasHoy').textContent.replace('| ', ''),
    ventasHoy: document.getElementById('valor-ventasHoy').textContent,
    variacionVentas: document.getElementById('variacion-ventasHoy').textContent,
    ingresos: document.getElementById('valor-ingresosVentas').textContent,
    variacionIngresos: document.getElementById('variacion-ingresosVentas').textContent,
    productos: datosReporte.productos,
    clientes: datosReporte.clientes,
    empleados: datosReporte.empleados,
    dias: datosReporte.dias
  };

  // Crear libro de Excel
  const wb = XLSX.utils.book_new();
  
  // Hoja de resumen
  const resumen = [
    ["REPORTE DE VENTAS"],
    ["Fecha de generación", reporteData.fecha],
    ["Período", reporteData.periodo],
    [],
    ["Métrica", "Valor"],
    ["Ventas hoy", reporteData.ventasHoy],
    ["Variación ventas", reporteData.variacionVentas],
    ["Ingresos", reporteData.ingresos],
    ["Variación ingresos", reporteData.variacionIngresos]
  ];
  
  // Hoja de productos
  const productos = [
    ["Productos Más Vendidos"],
    ["Producto", "Cantidad", "Porcentaje"]
  ];
  const totalProductos = reporteData.productos.reduce((sum, item) => sum + item.value, 0);
  reporteData.productos.forEach(item => {
    productos.push([
      item.label,
      item.value,
      totalProductos > 0 ? (item.value / totalProductos * 100).toFixed(2) + '%' : '0%'
    ]);
  });
  
  // Hoja de clientes
  const clientes = [
    ["Clientes Destacados"],
    ["Cliente", "Monto Gastado ($)", "Porcentaje"]
  ];
  const totalClientes = reporteData.clientes.reduce((sum, item) => sum + item.value, 0);
  reporteData.clientes.forEach(item => {
    clientes.push([
      item.label,
      item.value,
      totalClientes > 0 ? (item.value / totalClientes * 100).toFixed(2) + '%' : '0%'
    ]);
  });
  
  // Hoja de empleados
  const empleados = [
    ["Rendimiento por Empleado"],
    ["Empleado", "Monto Generado ($)", "Porcentaje"]
  ];
  const totalEmpleados = reporteData.empleados.reduce((sum, item) => sum + item.value, 0);
  reporteData.empleados.forEach(item => {
    empleados.push([
      item.label,
      item.value,
      totalEmpleados > 0 ? (item.value / totalEmpleados * 100).toFixed(2) + '%' : '0%'
    ]);
  });
  
  // Hoja de días
  const dias = [
    ["Ventas por Día"],
    ["Fecha", "Monto ($)", "Porcentaje"]
  ];
  const totalDias = reporteData.dias.reduce((sum, item) => sum + item.value, 0);
  reporteData.dias.forEach(item => {
    dias.push([
      item.label,
      item.value,
      totalDias > 0 ? (item.value / totalDias * 100).toFixed(2) + '%' : '0%'
    ]);
  });

  // Añadir hojas al libro
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(resumen), "Resumen");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(productos), "Productos");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(clientes), "Clientes");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(empleados), "Empleados");
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dias), "Días");

  // Generar archivo
  XLSX.writeFile(wb, `reporte_ventas_${new Date().toISOString().split('T')[0]}.xlsx`);
  
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
  const preferenciaVentas = cargarPreferenciaFiltro('ventasHoy');
  const preferenciaGlobal = JSON.parse(localStorage.getItem('ultimo_filtro_global'));
  
  if (preferenciaGlobal && filtrosSincronizados) {
    // Cargar con el último filtro global
    aplicarFiltroGlobal(
      preferenciaGlobal.periodo,
      preferenciaGlobal.fechaInicio,
      preferenciaGlobal.fechaFin
    );
  } else if (preferenciaVentas && !filtrosSincronizados) {
    // Cargar cada componente con su propia preferencia
    cargarDatosPorTipo(
      'ventasHoy',
      preferenciaVentas.periodo,
      preferenciaVentas.fechaInicio,
      preferenciaVentas.fechaFin
    );
    
    const preferenciaIngresos = cargarPreferenciaFiltro('ingresosVentas') || preferenciaVentas;
    cargarDatosPorTipo(
      'ingresosVentas',
      preferenciaIngresos.periodo,
      preferenciaIngresos.fechaInicio,
      preferenciaIngresos.fechaFin
    );
    
    const preferenciaProductos = cargarPreferenciaFiltro('productosChart') || preferenciaVentas;
    cargarGraficoProductos(
      preferenciaProductos.periodo,
      preferenciaProductos.fechaInicio,
      preferenciaProductos.fechaFin
    );
    
    const preferenciaClientes = cargarPreferenciaFiltro('clientesChart') || preferenciaVentas;
    cargarGraficoClientes(
      preferenciaClientes.periodo,
      preferenciaClientes.fechaInicio,
      preferenciaClientes.fechaFin
    );
    
    const preferenciaEmpleados = cargarPreferenciaFiltro('empleadosChart') || preferenciaVentas;
    cargarGraficoEmpleados(
      preferenciaEmpleados.periodo,
      preferenciaEmpleados.fechaInicio,
      preferenciaEmpleados.fechaFin
    );
    
    const preferenciaDias = cargarPreferenciaFiltro('diasChart') || preferenciaVentas;
    cargarGraficoDias(
      preferenciaDias.periodo,
      preferenciaDias.fechaInicio,
      preferenciaDias.fechaFin
    );
  } else {
    // Cargar con valores por defecto (hoy)
    cargarDatos('ventasHoy', 'hoy');
    cargarDatos('ingresosVentas', 'hoy');
    cargarGraficoProductos('hoy');
    cargarGraficoClientes('hoy');
    cargarGraficoEmpleados('hoy');
    cargarGraficoDias('hoy');
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