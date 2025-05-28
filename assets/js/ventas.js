/**
   * Funciones de Agregar
   */

  $(document).ready(function() { //Se asegura que el DOM este cargado 

    //Activa campos para agregar cliente
    $('.btn_new_cliente').click(function(e){
        e.preventDefault();
        $('#nom_cliente').removeAttr('disabled');
        $('#ape_cliente').removeAttr('disabled');
        $('#tel_cliente').removeAttr('disabled');

        $('#div_registro_cliente').slideDown();

    });

    //Buscar clientes
    $('#dni_cliente').keyup(function(e){ //cada vez que teclean un valor se activa
        e.preventDefault(); //evito que se recargue

        var cl = $(this).val(); //capturo lo que se teclea en cl
        var action = 'searchCliente';

        $.ajax({
            url: '../ajax.php',
            type: "POST",
            async : true,
            data: {action:action,cliente:cl},

            success: function(response)
            {
                if(response == 0){
                    $('#idCliente').val('');
                    $('#nom_cliente').val('');
                    $('#ape_cliente').val('');
                    $('#tel_cliente').val('');
                    //mostrar boton agregar
                    $('.btn_new_cliente').slideDown();
                }else{
                    var data = $.parseJSON(response);
                    $('#idCliente').val(data.idCliente);
                    $('#nom_cliente').val(data.nombre);
                    $('#ape_cliente').val(data.apellido);
                    $('#tel_cliente').val(data.telefono);
                    //Ocultar boton agregar
                    $('.btn_new_cliente').slideUp();

                    //Bloquea campos
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#ape_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');

                    //Oculta boton guardar
                    $('#div_registro_cliente').slideUp();

                }
            },
            error: function(error){
                console.log('Error:', error);
            }

        });

    });

    //Crear clientes
    $('#formularioClienteVenta').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '../ajax.php',
            type: "POST",
            async : true,
            data: $('#formularioClienteVenta').serialize(), //le paso todos los elementos del formulario

            success: function(response)
            {
                if(response != 'error'){
                    //Agregar id al input hiden
                    $('#idCliente').val(response);
                    //Bloquea campos
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#ape_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');

                    //Ocultar boton agregar
                    $('.btn_new_cliente').slideUp();

                    //Ocultar boton guardar
                    $('#div_registro_cliente').slideUp();
                }
            },
            error: function(error){
                console.log('Error:', error);
            }

        });

    });

    //Buscar producto
    $('#txtIdProducto').keyup(function(e) {
    e.preventDefault();

    var producto = $(this).val();
    var action = 'infoProducto';

    // Resetear valores ANTES de la llamada AJAX
    $('#txt_producto').html('-'); 
    $('#txt_categoria').html('-');
    $('#txt_precio').html('0.00');
    $('#txt_cantidad_producto').val(0).attr('disabled', true);
    $('#txt_precio_total').html('0.00');
    $('#add_producto_venta').slideUp();
    $('#add_producto_pedido').slideUp();

    if(producto != '') {
        $.ajax({
            url: '../ajax.php',
            type: "POST",
            dataType: 'text', // se espera texto para poder parsear y validar
            data: { action: action, producto: producto },
            success: function(response) {
                try {
                    var data = JSON.parse(response);

                    if (data === 'error' || data == null || data.nombre === undefined) {
                        // Producto no encontrado → resetear campos
                        $('#txt_producto').html('-'); 
                        $('#txt_categoria').html('-');
                        $('#txt_precio').html('0.00');
                        $('#txt_cantidad_producto').val(0).attr('disabled', true);
                        $('#txt_precio_total').html('0.00');
                        $('#add_producto_venta').slideUp();
                        $('#add_producto_pedido').slideUp();
                    } else {
                        // Producto encontrado → mostrar info
                        $('#txt_producto').html(data.nombre);
                        $('#txt_categoria').html(data.descripcion);
                        $('#txt_precio').html(data.precio);
                        $('#txt_cantidad_producto').val(1).removeAttr('disabled');
                        $('#txt_precio_total').html(data.precio);
                        $('#add_producto_venta').slideDown();
                        $('#add_producto_pedido').slideDown();
                    }
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    // En caso de error al parsear, resetear campos
                    $('#txt_producto').html('-'); 
                    $('#txt_categoria').html('-');
                    $('#txt_precio').html('0.00');
                    $('#txt_cantidad_producto').val(0).attr('disabled', true);
                    $('#txt_precio_total').html('0.00');
                    $('#add_producto_venta').slideUp();
                    $('#add_producto_pedido').slideUp();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                // Reset por seguridad
                $('#txt_producto').html('-'); 
                $('#txt_categoria').html('-');
                $('#txt_precio').html('0.00');
                $('#txt_cantidad_producto').val(0).attr('disabled', true);
                $('#txt_precio_total').html('0.00');
                $('#add_producto_venta').slideUp();
                $('#add_producto_pedido').slideUp();
            }
        });
    }
    });

    //Validar cantidad de producto antes de agregar
    $('#txt_cantidad_producto').keyup(function(e){
        e.preventDefault();
        var cantidad = $(this).val();
        var idProducto = $('#txtIdProducto').val();
        var precio = parseFloat($('#txt_precio').html());
        
        // Validación básica numérica
        if(isNaN(cantidad)) {
            $('#add_producto_venta').slideUp();
            $('#add_producto_pedido').slideUp();
            return;
        }
        
        cantidad = parseInt(cantidad);
        
        // Obtener stock mediante AJAX
        $.ajax({
            url: '../ajax.php',
            type: "POST",
            async: true,
            data: {
                action: 'checkStock',
                idProducto: idProducto
            },
            success: function(response){
                var stockDisponible = parseInt(response);
                
                // Mostrar mensaje de error si no hay suficiente stock
                if(cantidad > stockDisponible) {
                    $('#stock-error').remove();
                    $('#txt_cantidad_producto').after(
                        '<div id="stock-error" class="text-danger small mt-1">' +
                        'Stock insuficiente. Disponible: ' + stockDisponible +
                        '</div>'
                    );
                    $('#add_producto_venta').slideUp();
                    $('#add_producto_pedido').slideUp();
                    return;
                } else {
                    $('#stock-error').remove();
                }
                
                // Calcular precio total
                var precio_total = cantidad * precio;
                var precioConDosDecimales = precio_total.toFixed(2);
                $('#txt_precio_total').html(precioConDosDecimales);

                // Mostrar/ocultar botones según cantidad
                if(cantidad < 1) {
                    $('#add_producto_venta').slideUp();
                    $('#add_producto_pedido').slideUp();
                } else {
                    $('#add_producto_venta').slideDown();
                    $('#add_producto_pedido').slideDown();
                }
            },
            error: function(){
                console.log('Error al verificar el stock');
            }
        });
    });

    // Evento keyup para recalcular el total restando la seña
    $(document).on('keyup', '#seniaPedido', function (e) {
        e.preventDefault();
        
        var descuento = $('#descuentoPedido').val();

        if (descuento !== null && !isNaN(descuento) && parseFloat(descuento) > 0) {

            var descuentoCalculado = $('#total_pedido_original').html() * (descuento / 100);
            var totalConDescuento = $('#total_pedido_original').html() - descuentoCalculado;

            var precio_total = totalConDescuento - $(this).val();//calculo el precio total

            // Actualizar el total restante en el DOM
            $('#total_pedido').text(precio_total.toFixed(2));
    
            // Mostrar u ocultar el botón según el total restante
            if (precio_total < 0) {
                $('#btn_new_pedido').hide();
            } else {
                $('#btn_new_pedido').show();
            }
        }else{
            var precio_total =$('#total_pedido_original').html() - $(this).val();//calculo el precio total
            console.log(descuento);
            // Actualizar el total restante en el DOM
            $('#total_pedido').text(precio_total.toFixed(2));
    
            // Mostrar u ocultar el botón según el total restante
            if (precio_total < 0) {
                $('#btn_new_pedido').hide();
            } else {
                $('#btn_new_pedido').show();
            }
        }
     
    });

    // Evento keyup para recalcular el total restando el descuento
    $(document).on('keyup', '#descuentoPedido', function (e) {
        e.preventDefault();

        // Obtener valores de los campos
        var senia = parseFloat($('#seniaPedido').val()) || 0; // Si no hay valor, se toma como 0
        var totalOriginal = parseFloat($('#total_pedido_original').html()) || 0; // Si no hay valor, se toma como 0
        var descuento = parseFloat($(this).val()) || 0; // Si no hay valor, se toma como 0

        // Calcular el descuento
        var descuentoCalculado = totalOriginal * (descuento / 100);
        var totalConDescuento = totalOriginal - descuentoCalculado;

        // Calcular el total restando la seña
        var precio_total = totalConDescuento - senia;

        // Actualizar el total restante en el DOM
        $('#total_pedido').text(precio_total.toFixed(2));

        // Mostrar u ocultar el botón según el total restante
        if (precio_total < 0) {
            $('#btn_new_pedido').hide();
        } else {
            $('#btn_new_pedido').show();
        }
    });

    //Agregar producto al detalle temporal
    $('#add_producto_venta').click(function(e){
        e.preventDefault();
        if($('#txt_cantidad_producto').val() > 0){

            var idProducto = $('#txtIdProducto').val();
            var cantidad = $('#txt_cantidad_producto').val();
            var action = 'agregarProductoDetalle';

            $.ajax({
                url: '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,producto:idProducto,cantidad:cantidad}, 
    
                success: function(response){
                    console.log('Respuesta del servidor:', response); // Depuración
                    try {
                        var info = JSON.parse(response); // Intentar analizar la respuesta como JSON
                        $('#detalleVenta').html(info.detalle);//pasamos el codigo a #detalle_venta y totales
                        $('#detalleTotal').html(info.totales);

                        //ponemos todos los valores por defecto
                        $('#txtIdProducto').val('');
                        $('#txt_producto').html('-');
                        $('#txt_categoria').html('-'); 
                        $('#txt_precio').html('0.00');
                        $('#txt_cantidad_producto').val(0);
                        $('#txt_precio_total').html('0.00');

                        //bloquear Cantidad
                        $('#txt_cantidad_producto').attr('disabled','disabled');

                        //ocultar boton agregar
                        $('#add_producto_venta').slideUp();
                    } catch (e) {
                        console.error('Error al analizar el JSON:', e);
                        console.error('Respuesta recibida:', response);
                        console.log('no data');
                    }
                    viewProcesar();//llamo la funcion para ver si oculto el boton

                },
                error: function(error){
                    console.log('Error:', error);
                }
    
            });


        }
    });

    //Agregar producto al detalle temporal
    $('#add_producto_pedido').click(function(e){
        e.preventDefault();
        if($('#txt_cantidad_producto').val() > 0){

            var idProducto = $('#txtIdProducto').val();
            var cantidad = $('#txt_cantidad_producto').val();
            var action = 'agregarProductoDetallePedido';

            $.ajax({
                url: '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,producto:idProducto,cantidad:cantidad}, 
    
                success: function(response){
                    console.log('Respuesta del servidor:', response); // Depuración
                    try {
                        var info = JSON.parse(response); // Intentar analizar la respuesta como JSON
                        $('#detalleVenta').html(info.detalle);//pasamos el codigo a #detalle_venta y totales
                        $('#detalleTotal').html(info.totales);

                        //ponemos todos los valores por defecto
                        $('#txtIdProducto').val('');
                        $('#txt_producto').html('-');
                        $('#txt_categoria').html('-'); 
                        $('#txt_precio').html('0.00');
                        $('#txt_cantidad_producto').val(0);
                        $('#txt_precio_total').html('0.00');

                        //bloquear Cantidad
                        $('#txt_cantidad_producto').attr('disabled','disabled');

                        //ocultar boton agregar
                        $('#add_producto_pedido').slideUp();
                    } catch (e) {
                        console.error('Error al analizar el JSON:', e);
                        console.error('Respuesta recibida:', response);
                        console.log('no data');
                    }
                    viewProcesar();//llamo la funcion para ver si oculto el boton

                },
                error: function(error){
                    console.log('Error:', error);
                }
    
            });


        }
    });

    //Anular venta
    $('#btn_anular_venta').click(function(e){
        e.preventDefault();
        console.log('entre a anular venta');
        var rows =$('#detalleVenta tr').length;//cuantas filas tiene detalle venta

        if(rows > 0){// si hay productos en el detalle                                                                                                                                  
            var action = 'anularVenta';

            $.ajax({
                url: '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action}, 
    
                success: function(response){
                    if(response!='error'){// si elimino todo el detalle
                        location.reload();//refresca toda la pagina
                    }
                },
                error: function(error){

                }
            });    

        }

    });

    //Anular pedido
    $('#btn_anular_pedido').click(function(e){
        e.preventDefault();
        console.log('entre a anular venta');
        var rows =$('#detalleVenta tr').length;//cuantas filas tiene detalle venta

        if(rows > 0){// si hay productos en el detalle                                                                                                                                  
            var action = 'anularVenta';

            $.ajax({
                url: '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action}, 
    
                success: function(response){
                    if(response!='error'){// si elimino todo el detalle
                        location.reload();//refresca toda la pagina
                    }
                },
                error: function(error){

                }
            });    

        }

    });

    //Confirmar venta
    $('#btn_new_venta').click(function(e) {
        e.preventDefault();

        var rows = $('#detalleVenta tr').length; // Contar las filas en el detalle de la venta
        var codCliente = $('#idCliente').val();
        var senia = parseFloat($('#seniaPedido').val()) || 0; // Obtener la seña, si no hay valor, se toma como 0
        var descuento = parseFloat($('#descuentoPedido').val()) || 0; // Obtener el descuento, si no hay valor, se toma como 0

        if (rows > 0) { // Si hay productos en el detalle
            if (!codCliente) {
                alert('Falta agregar cliente');
                return;
            }

            $.ajax({
                url: '../ajax.php',
                type: 'POST',
                async: true,
                data: {
                    action: 'procesarVenta',
                    codCliente: codCliente,
                    senia: senia,
                    descuento: descuento
                },
                success: function(response) {
                    try {
                        var info = JSON.parse(response); // Analizar la respuesta como JSON
                        if (info.error) {
                            alert('Error al procesar la venta: ' + info.error);
                        } else {
                            alert('Venta procesada correctamente');
                            console.log('Datos de la venta:', info);
                            location.reload(); // Refrescar la página
                        }
                    } catch (e) {
                        console.error('Error al analizar la respuesta:', e);
                        console.error('Respuesta recibida:', response);
                        alert('Error al procesar la venta');
                    }
                },
                error: function(error) {
                    console.error('Error en la solicitud AJAX:', error);
                    alert('Error al procesar la venta');
                }
            });
        } else {
            alert('No hay productos en el detalle de la venta');
        }
    });

    //Confirmar pedido
    $('#btn_new_pedido').click(function(e) {
        e.preventDefault();

        var rows = $('#detalleVenta tr').length; // Contar las filas en el detalle de la venta
        var codCliente = $('#idCliente').val();
        var senia = parseFloat($('#seniaPedido').val()) || 0; // Obtener la seña, si no hay valor, se toma como 0
        var descuento = parseFloat($('#descuentoPedido').val()) || 0; // Obtener el descuento, si no hay valor, se toma como 0

        if (rows > 0) { // Si hay productos en el detalle
            if (!codCliente) {
                alert('Falta agregar cliente');
                return;
            }

            $.ajax({
                url: '../ajax.php',
                type: 'POST',
                async: true,
                data: {
                    action: 'procesarPedido',
                    codCliente: codCliente,
                    senia: senia,
                    descuento: descuento
                },
                success: function(response) {
                    try {
                        var info = JSON.parse(response); // Analizar la respuesta como JSON
                        if (info.error) {
                            alert('Error al procesar el pedido: ' + info.error);
                        } else {
                            alert('Pedido procesada correctamente');
                            console.log('Datos del pedido:', info);
                            location.reload(); // Refrescar la página
                        }
                    } catch (e) {
                        console.error('Error al analizar la respuesta:', e);
                        console.error('Respuesta recibida:', response);
                        alert('Error al procesar el pedido');
                    }
                },
                error: function(error) {
                    console.error('Error en la solicitud AJAX:', error);
                    alert('Error al procesar el pedido');
                }
            });
        } else {
            alert('No hay productos en el detalle del pedido');
        }
    }); 

});

//Agrega producto a venta desde la lista de productos(fuera del ready)
function agregarAVenta(idProducto) {
    // Solicitar la cantidad
    var cantidad = prompt("Ingrese la cantidad:");

    // Verificar que se haya ingresado un valor
    if (cantidad > 0 && cantidad !== null && cantidad !== "" && !isNaN(cantidad)) {
        // Confirmar la acción
        var confirmar = confirm("¿Está seguro que desea agregar " + cantidad + " unidades a la venta?");
        if (confirmar) {
            // Redirigir a la página con los parámetros necesarios
            //window.location.href = "modificar_productos.php?ID_PRODUCTO=" + idProducto + "&CANTIDAD=" + cantidad;

            var action = 'agregarProductoDetalle';

            $.ajax({
                url: '../ajax.php',
                type: "POST",
                async : true,
                data: {action:action,producto:idProducto,cantidad:cantidad}, 
    
                success: function(response){
                    try {
                        var info = JSON.parse(response); // Intentar analizar la respuesta como JSON
                        $('#detalleVenta').html(info.detalle);//pasamos el codigo a #detalle_venta y totales
                        $('#detalleTotal').html(info.totales);

                        //ponemos todos los valores por defecto
                        $('#txtIdProducto').val('');
                        $('#txt_producto').html('-'); 
                        $('#txt_categoria').html('-');
                        $('#txt_precio').html('0.00');
                        $('#txt_cantidad_producto').val(0);
                        $('#txt_precio_total').html('0.00');

                        //bloquear Cantidad
                        $('#txt_cantidad_producto').attr('disabled','disabled');

                        //ocultar boton agregar
                        $('#add_producto_venta').slideUp();
                        alert('Producto agregado a la venta!');
                    } catch (e) {
                        console.error('Error al analizar el JSON:', e);
                        console.error('Respuesta recibida:', response);
                        console.log('no data');
                    }
                    viewProcesar();//llamo la funcion para ver si oculto el boton

                },
                error: function(error){
                    console.log('Error:', error);
                }
    
            });
        }
    } else {
        alert("Por favor, ingrese una cantidad válida.");
    }
}

//funcion para eliminar el detalle de la venta(fuera del ready)
function del_producto_detalle(correlativo){
    var action ='delProductoDetalle';
    var id_detalle =correlativo;

    $.ajax({
        url: '../ajax.php',
        type: "POST",
        async : true,
        data: {action:action,id_detalle:id_detalle}, 

        success: function(response){
            console.log('Respuesta del servidor:', response); // Depuración
            try {
                var info = JSON.parse(response); // Intentar analizar la respuesta como JSON
                $('#detalleVenta').html(info.detalle);//pasamos el codigo a #detalle_venta y totales
                $('#detalleTotal').html(info.totales);

                //ponemos todos los valores por defecto
                $('#txtIdProducto').val('');
                $('#txt_producto').html('-'); 
                $('#txt_categoria').html('-');
                $('#txt_precio').html('0.00');
                $('#txt_cantidad_producto').val(0);
                $('#txt_precio_total').html('0.00');

                //bloquear Cantidad
                $('#txt_cantidad_producto').attr('disabled','disabled');

                //ocultar boton agregar
                $('#add_producto_pedido').slideUp();
            } catch (e) {
                console.error('Error al analizar el JSON:', e);
                console.error('Respuesta recibida:', response);
                $('#detalleVenta').html('');
                $('#detalleTotal').html('');
            }
            viewProcesar();//llamo la funcion para ver si oculto el boton
        },
        error: function(error){
            console.log('Error:', error);
        }

    });

}

//funcion para eliminar el detalle de la venta(fuera del ready)
function del_producto_detalle_pedido(correlativo){
    var action ='delProductoDetallePedido';
    var id_detalle =correlativo;

    $.ajax({
        url: '../ajax.php',
        type: "POST",
        async : true,
        data: {action:action,id_detalle:id_detalle}, 

        success: function(response){
            console.log('Respuesta del servidor:', response); // Depuración
            try {
                var info = JSON.parse(response); // Intentar analizar la respuesta como JSON
                $('#detalleVenta').html(info.detalle);//pasamos el codigo a #detalle_venta y totales
                $('#detalleTotal').html(info.totales);

                //ponemos todos los valores por defecto
                $('#txtIdProducto').val('');
                $('#txt_producto').html('-'); 
                $('#txt_categoria').html('-');
                $('#txt_precio').html('0.00');
                $('#txt_cantidad_producto').val(0);
                $('#txt_precio_total').html('0.00');

                //bloquear Cantidad
                $('#txt_cantidad_producto').attr('disabled','disabled');

                //ocultar boton agregar
                $('#add_producto_pedido').slideUp();
            } catch (e) {
                console.error('Error al analizar el JSON:', e);
                console.error('Respuesta recibida:', response);
                $('#detalleVenta').html('');
                $('#detalleTotal').html('');
            }
            viewProcesar();//llamo la funcion para ver si oculto el boton
        },
        error: function(error){
            console.log('Error:', error);
        }

    });

}

//funcion para mostrar u ocultar boton de registrar venta(fuera del ready)
function viewProcesar(){
    if($('#detalleVenta tr').length > 0){
        $('#btn_new_venta').show();
    }else{
        $('#btn_new_venta').hide();
    }

}

//funcion para mostrar siempre el detalle de la venta(fuera del ready)
function searchforDetalle() {
    var action = 'searchforDetalle';

    $.ajax({
        url: '../ajax.php',
        type: "POST",
        dataType: 'json', // Esperamos JSON
        data: {action: action},
        success: function(response) {
            if (response.success) {
                $('#detalleVenta').html(response.detalle);
                $('#detalleTotal').html(response.totales);
            } else {
                $('#detalleVenta').html(response.detalle);
                $('#detalleTotal').html(response.totales);
                console.log(response.message);
            }
            viewProcesar();
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud:', status, error);
            $('#detalleVenta').html('<tr><td colspan="7" class="text-center">Error al cargar los productos</td></tr>');
            $('#detalleTotal').html('');
            viewProcesar();
        }
    });
}

//funcion para mostrar siempre el detalle del pedido(fuera del ready)
function searchforDetallePedido() {
    var action = 'searchforDetallePedido';

    $.ajax({
        url: '../ajax.php',
        type: "POST",
        dataType: 'json', // Esperamos JSON
        data: {action: action},
        success: function(response) {
            if (response.success) {
                $('#detalleVenta').html(response.detalle);
                $('#detalleTotal').html(response.totales);
            } else {
                $('#detalleVenta').html(response.detalle);
                $('#detalleTotal').html(response.totales);
                console.log(response.message);
            }
            viewProcesar();
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud:', status, error);
            $('#detalleVenta').html('<tr><td colspan="7" class="text-center">Error al cargar los productos</td></tr>');
            $('#detalleTotal').html('');
            viewProcesar();
        }
    });
}





