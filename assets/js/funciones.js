$(document).ready(function() {
    // Verifica que el contenedor del gráfico exista en el DOM
    const chartElement = document.querySelector("#reportsChart");
    if (!chartElement) {
        console.error('No se encontró el elemento con ID "reportsChart".');
        return;
    }

    // Inicializa el gráfico
    window.chart = new ApexCharts(chartElement, {
        series: [], // Inicialmente vacío
        chart: {
            id: "reportsChart", // Asigna un ID al gráfico
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            },
        },
        markers: {
            size: 4
        },
        colors: ['#4154f1', '#2eca6a', '#ff771d'],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.4,
                stops: [0, 90, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            type: 'category', // Cambia a 'category' para usar horarios
            categories: [] // Inicialmente vacío
        },
        tooltip: {
            x: {
                format: 'HH:mm'
            },
        }
    });

    // Renderiza el gráfico
    window.chart.render();
    console.log('Gráfico inicializado:', window.chart);

    // Cargar datos iniciales (por defecto, hoy)
    cambiarFiltro('hoy', 'reportes');
});

function cambiarFiltro(filtro, tipo) {
    // Actualizar el texto del período seleccionado
    const periodoElement = document.getElementById(`periodo-${tipo}`);
    if (periodoElement) {
        periodoElement.textContent = `| ${filtro.charAt(0).toUpperCase() + filtro.slice(1)}`;
    }

    // Llamar a la función PHP para obtener los datos
    obtenerDatos(filtro, tipo);
}

function obtenerDatos(filtro, tipo) {
    fetch(`/Proyecto-Practica-Profesionalizante/ajax.php?accion=obtener_${tipo}&filtro=${filtro}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            // Actualizar los datos en la interfaz según el tipo
            switch (tipo) {
                case 'ganancia':
                    const totalGananciaElement = document.getElementById('totalGanancia');
                    if (totalGananciaElement) {
                        totalGananciaElement.textContent = `$${data.total}`;
                        console.log('Datos de ganancias:', data);
                    }
                    break;
                case 'turnos':
                    const totalTurnosElement = document.getElementById('totalTurnos');
                    if (totalTurnosElement) {
                        totalTurnosElement.textContent = data.total;
                        console.log('Datos de turnos:', data.total);
                    }
                    break;
                case 'reportes':
                    // Actualizar el gráfico de reportes
                    actualizarGrafico(data.series, data.categorias);
                    console.log('Datos de reportes:', data);
                    break;
                default:
                    console.error('Tipo no válido:', tipo);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            alert('Hubo un error al obtener los datos. Por favor, inténtalo de nuevo.');
        });
}

function cambiarFiltroReporte(filtro, tipo) {
    // Actualizar el texto del período seleccionado
    const periodoElement = document.getElementById('periodo-reportes');
    if (periodoElement) {
        periodoElement.textContent = `/${filtro.charAt(0).toUpperCase() + filtro.slice(1)}`;
    }

    // Llamar a la función PHP para obtener los datos
    obtenerDatos(filtro, tipo);
}

function actualizarGrafico(series, categorias) {
    // Verifica si el gráfico existe
    const chart = ApexCharts.getChartByID("reportsChart");
    if (chart) {
        chart.updateOptions({
            series: series,
            xaxis: {
                categories: categorias
            }
        });
    } else {
        console.error('No se encontró el gráfico con ID "reportsChart".');
    }
}



