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
    <h1>Panel de Turnos</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../inicio/index.php">Home</a></li>
        <li class="breadcrumb-item active">Panel Turnos</li>
      </ol>
    </nav>
  </div>

  <section class="section dashboard">
    <div class="row">
      <div class="col-12 mb-3">
        <div class="float-end">
          <button id="sincronizar-filtros" class="btn btn-sm btn-outline-primary" onclick="toggleSincronizacionFiltros()">
            <i id="sincronizar-filtros-icon" class="bi bi-link"></i> Sincronizar filtros
          </button>
        </div>
      </div>
      
      <!-- Tarjeta 1: Turnos Hoy -->
      <div class="col-lg-6 col-md-6">
        <div class="card info-card sales-card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
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
            <h5 class="card-title">Turnos <span id="periodo-turnosHoy">| Hoy</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-calendar-check"></i>
              </div>
              <div class="ps-3">
                <h6 id="valor-turnosHoy">0</h6>
                <span class="text-success small pt-1 fw-bold" id="variacion-turnosHoy">0%</span>
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
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
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
            <h5 class="card-title">Ingresos <span id="periodo-ingresosTurnos">| Hoy</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div class="ps-3">
                <h6 id="valor-ingresosTurnos">$0.00</h6>
                <span class="text-success small pt-1 fw-bold" id="variacion-ingresosTurnos">0%</span>
                <span class="text-muted small pt-2 ps-1">vs período anterior</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráfico 1: Turnos por Estado -->
      <div class="col-lg-6">
        <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
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
            <h5 class="card-title">Turnos por Estado <span id="periodo-estadoChart">| Hoy</span></h5>
            <div id="estadoChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 2: Turnos por Estilista -->
      <div class="col-lg-6">
        <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
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
            <h5 class="card-title">Turnos por Estilista <span id="periodo-estilistaChart">| Hoy</span></h5>
            <div id="estilistaChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

      <!-- Gráfico 3: Ocupación por Horario -->
      <div class="col-lg-12">
        <div class="card">
          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li><a class="dropdown-item" onclick="seleccionarPeriodo('hoy', this)">Hoy</a></li>
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
            <h5 class="card-title">Ocupación por Horario <span id="periodo-horarioChart">| Hoy</span></h5>
            <div id="horarioChart" style="min-height: 350px;"></div>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

<?php require ('../footer.inc.php'); ?>

<!-- CDN de ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Variables globales para los gráficos
let estadoChart, estilistaChart, horarioChart;
let filtrosSincronizados = true;

// Función para formatear números
function formatNumber(value, decimals = 0) {
  const num = typeof value === 'string' ? parseFloat(value) : value;
  return isNaN(num) ? '0' : num.toFixed(decimals);
}

// Función para manejar la selección de período
function seleccionarPeriodo(periodo, elemento) {
  if (periodo === 'personalizado') return; // Se maneja con el botón
  
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
  if (card.classList.contains('sales-card')) return 'turnosHoy';
  if (card.classList.contains('revenue-card')) return 'ingresosTurnos';
  if (card.querySelector('#estadoChart')) return 'estadoChart';
  if (card.querySelector('#estilistaChart')) return 'estilistaChart';
  return 'horarioChart';
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
  
  if (tipo === 'turnosHoy' || tipo === 'ingresosTurnos') {
    cargarDatos(tipo, periodo, fechaInicio, fechaFin);
  } else {
    const funcion = tipo === 'estadoChart' ? cargarGraficoEstado :
                   tipo === 'estilistaChart' ? cargarGraficoEstilista : cargarGraficoHorario;
    funcion(periodo, fechaInicio, fechaFin);
  }
}

// Función para cargar datos de las tarjetas
async function cargarDatos(tipo, periodo, fechaInicio = null, fechaFin = null) {
  let url = `../panel_control/get_turnos_data.php?tipo=${tipo}&periodo=${periodo}`;
  
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

    if (tipo === 'turnosHoy') {
      document.getElementById('valor-turnosHoy').textContent = formatNumber(data.total);
      document.getElementById('variacion-turnosHoy').textContent = formatNumber(data.variacion, 2) + '%';
      document.getElementById('periodo-turnosHoy').textContent = '| ' + (data.periodo || periodo);
      
      // Actualizar clase según variación
      const variacionElement = document.getElementById('variacion-turnosHoy');
      variacionElement.className = data.variacion >= 0 ? 
        'text-success small pt-1 fw-bold' : 'text-danger small pt-1 fw-bold';
    } 
    else if (tipo === 'ingresosTurnos') {
      document.getElementById('valor-ingresosTurnos').textContent = '$' + formatNumber(data.total, 2);
      document.getElementById('variacion-ingresosTurnos').textContent = formatNumber(data.variacion, 2) + '%';
      document.getElementById('periodo-ingresosTurnos').textContent = '| ' + (data.periodo || periodo);
      
      // Actualizar clase según variación
      const variacionElement = document.getElementById('variacion-ingresosTurnos');
      variacionElement.className = data.variacion >= 0 ? 
        'text-success small pt-1 fw-bold' : 'text-danger small pt-1 fw-bold';
    }
  } catch (error) {
    console.error(`Error cargando ${tipo}:`, error);
    const elemento = tipo === 'turnosHoy' ? 'valor-turnosHoy' : 'valor-ingresosTurnos';
    document.getElementById(elemento).textContent = 'Error';
    document.getElementById(`variacion-${tipo}`).textContent = '';
    
    mostrarAlerta(`Error al cargar datos: ${error.message}`, 'danger');
  }
}

// Función para cargar el gráfico de Turnos por Estado
async function cargarGraficoEstado(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('estadoChart');
  const periodoElement = document.getElementById('periodo-estadoChart');
  
  // Mostrar indicador de carga
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_turnos_data.php?tipo=estadoChart&periodo=${periodo}`;
    if (periodo === 'personalizado' && fechaInicio && fechaFin) {
      url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    }
    
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    
    const data = await response.json();
    
    // Limpiar contenedor
    container.innerHTML = '';
    periodoElement.textContent = '| ' + (data.periodo || periodo);
    
    if (!data.series || data.series.length === 0 || data.series.every(val => val === 0)) {
      container.innerHTML = `
        <div class="text-center py-4">
          <i class="bi bi-calendar-x" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay turnos en este período</p>
        </div>
      `;
      return;
    }
    
    // Configuración del gráfico
    const options = {
      series: data.series,
      chart: {
        type: 'donut',
        height: 350,
        animations: {
          enabled: true
        }
      },
      labels: data.labels,
      colors: data.colors || ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'],
      legend: {
        position: 'bottom',
        horizontalAlign: 'center'
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              total: {
                show: true,
                label: 'Total turnos',
                formatter: function(w) {
                  return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                }
              }
            }
          }
        }
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 300
          },
          legend: {
            position: 'bottom'
          }
        }
      }],
      noData: {
        text: "No hay datos disponibles",
        align: 'center',
        verticalAlign: 'middle'
      }
    };
    
    // Destruir gráfico anterior si existe
    if (estadoChart) {
      estadoChart.destroy();
    }
    
    // Crear nuevo gráfico
    estadoChart = new ApexCharts(container, options);
    estadoChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Turnos por Estilista
async function cargarGraficoEstilista(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('estilistaChart');
  const periodoElement = document.getElementById('periodo-estilistaChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_turnos_data.php?tipo=estilistaChart&periodo=${periodo}`;
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
          <p class="text-muted mt-2">No hay turnos asignados a estilistas en este período</p>
        </div>
      `;
      return;
    }
    
    const options = {
      series: [{
        name: 'Turnos',
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
          text: 'Cantidad de Turnos'
        }
      },
      yaxis: {
        title: {
          text: 'Estilista'
        }
      },
      colors: ['#3b82f6'],
      tooltip: {
        y: {
          formatter: function(value) {
            return value + ' turnos';
          }
        }
      }
    };
    
    if (estilistaChart) {
      estilistaChart.destroy();
    }
    
    estilistaChart = new ApexCharts(container, options);
    estilistaChart.render();
    
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> Error al cargar datos: ${error.message}
      </div>
    `;
  }
}

// Función para cargar el gráfico de Ocupación por Horario
async function cargarGraficoHorario(periodo, fechaInicio = null, fechaFin = null) {
  const container = document.getElementById('horarioChart');
  const periodoElement = document.getElementById('periodo-horarioChart');
  
  container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
  
  try {
    let url = `../panel_control/get_turnos_data.php?tipo=horarioChart&periodo=${periodo}`;
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
          <i class="bi bi-clock-history" style="font-size: 2rem; color: #6c757d;"></i>
          <p class="text-muted mt-2">No hay datos de ocupación en este período</p>
        </div>
      `;
      return;
    }
    
    const options = {
      series: [{
        name: 'Turnos',
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
          columnWidth: '80%',
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: data.labels,
        title: {
          text: 'Franja Horaria'
        }
      },
      yaxis: {
        title: {
          text: 'Cantidad de Turnos'
        }
      },
      colors: ['#10b981'],
      tooltip: {
        y: {
          formatter: function(value) {
            return value + ' turnos';
          }
        }
      }
    };
    
    if (horarioChart) {
      horarioChart.destroy();
    }
    
    horarioChart = new ApexCharts(container, options);
    horarioChart.render();
    
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
  cargarDatosPorTipo('turnosHoy', periodo, fechaInicio, fechaFin);
  cargarDatosPorTipo('ingresosTurnos', periodo, fechaInicio, fechaFin);
  cargarGraficoEstado(periodo, fechaInicio, fechaFin);
  cargarGraficoEstilista(periodo, fechaInicio, fechaFin);
  cargarGraficoHorario(periodo, fechaInicio, fechaFin);
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
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
  alerta.style.top = '20px';
  alerta.style.right = '20px';
  alerta.style.zIndex = '9999';
  alerta.style.minWidth = '300px';
  alerta.innerHTML = `
    ${mensaje}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  
  document.body.appendChild(alerta);
  
  // Auto cerrar después de 5 segundos
  setTimeout(() => {
    const bsAlert = new bootstrap.Alert(alerta);
    bsAlert.close();
  }, 5000);
}

// Cargar datos iniciales al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  // Verificar si hay preferencias guardadas
  const preferenciaTurnos = cargarPreferenciaFiltro('turnosHoy');
  const preferenciaGlobal = JSON.parse(localStorage.getItem('ultimo_filtro_global'));
  
  if (preferenciaGlobal && filtrosSincronizados) {
    // Cargar con el último filtro global
    aplicarFiltroGlobal(
      preferenciaGlobal.periodo,
      preferenciaGlobal.fechaInicio,
      preferenciaGlobal.fechaFin
    );
  } else if (preferenciaTurnos && !filtrosSincronizados) {
    // Cargar cada componente con su propia preferencia
    cargarDatosPorTipo(
      'turnosHoy',
      preferenciaTurnos.periodo,
      preferenciaTurnos.fechaInicio,
      preferenciaTurnos.fechaFin
    );
    
    const preferenciaIngresos = cargarPreferenciaFiltro('ingresosTurnos') || preferenciaTurnos;
    cargarDatosPorTipo(
      'ingresosTurnos',
      preferenciaIngresos.periodo,
      preferenciaIngresos.fechaInicio,
      preferenciaIngresos.fechaFin
    );
    
    const preferenciaEstado = cargarPreferenciaFiltro('estadoChart') || preferenciaTurnos;
    cargarGraficoEstado(
      preferenciaEstado.periodo,
      preferenciaEstado.fechaInicio,
      preferenciaEstado.fechaFin
    );
    
    const preferenciaEstilista = cargarPreferenciaFiltro('estilistaChart') || preferenciaTurnos;
    cargarGraficoEstilista(
      preferenciaEstilista.periodo,
      preferenciaEstilista.fechaInicio,
      preferenciaEstilista.fechaFin
    );
    
    const preferenciaHorario = cargarPreferenciaFiltro('horarioChart') || preferenciaTurnos;
    cargarGraficoHorario(
      preferenciaHorario.periodo,
      preferenciaHorario.fechaInicio,
      preferenciaHorario.fechaFin
    );
  } else {
    // Cargar con valores por defecto (hoy)
    cargarDatos('turnosHoy', 'hoy');
    cargarDatos('ingresosTurnos', 'hoy');
    cargarGraficoEstado('hoy');
    cargarGraficoEstilista('hoy');
    cargarGraficoHorario('hoy');
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