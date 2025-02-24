  /**
   * Funciones
   */

$(document).ready(function() { //Se asegura que el DOM este cargado 


    function cambiarFiltro(filtro) {
        // Actualizar el texto del período seleccionado
        console.log("entre");
        const periodoElement = document.getElementById('periodo');
        periodoElement.textContent = `| ${filtro.charAt(0).toUpperCase() + filtro.slice(1)}`;

        // Llamar a la función PHP para obtener los datos
        obtenerGanancias(filtro);
    }

    function obtenerGanancias(filtro) {
        // Hacer una solicitud AJAX al servidor
        fetch(`ajax.php?accion=obtener_ganancia&filtro=${filtro}`)
        .then(response => response.json())
        .then(data => {
            // Actualizar el total de ganancias en la interfaz
            const totalGananciaElement = document.getElementById('totalGanancia');
            totalGananciaElement.textContent = `$${data.total}`;
        })
        .catch(error => console.error('Error:', error));
    }


});





    