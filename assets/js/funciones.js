  /**
   * Funciones
   */

$(document).ready(function() { //Se asegura que el DOM este cargado 


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
    // Hacer una solicitud AJAX al servidor
    fetch(`ajax.php?accion=obtener_${tipo}&filtro=${filtro}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
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
                    // Aquí puedes actualizar el gráfico de reportes si es necesario
                    console.log('Datos de reportes:', data.total);
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




    