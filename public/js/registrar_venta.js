$(document).ready(function () {
    var url_obtiene = $('#url_obtiene').val();

    /* VALIDACIÓN DE CÓDIGO RFID */
    $('#rfid').keypress(function (e) {
        let code = (e.keyCode ? e.keyCode : e.which); 
        if(code == 13){
            return false; 
        }
    });

    $('#rfid').keyup(function (e) {
        if($(this).val().length % 10 == 0)
        {
            $(this).val($(this).val().substr(($(this).val().length - 10),$(this).val().length));
        }
        else if($(this).val().length > 10){
            $(this).val($(this).val().substr(($(this).val().length - 10),$(this).val().length));
        }

        if($(this).val().length == 10)
        {
            // SI EL TAMAÑO DEL TEXTO ES 10 BUSCAR Y AGREGAR PRODUCTO
            agregaProducto(url_obtiene, $(this).val());
            $(this).val("");
        }
    });
    /* FIN VALIDACIÓN */

    // ELIMINAR UN PRODUCTO DEL CARRITO
    $('#productos').on('dblclick','tr.producto',eliminarProducto);
        // evitar la propagacion del select para que no se elimne cuando se haga doble click sobre el
        $('#productos').on('dblclick','tr.producto td select',function(e){
            e.stopPropagation();
        });

    // REGISTRAR LA VENTA
    $('#registrar_venta').click(function(e){
        e.preventDefault();
        let tbody = $('#productos');
        let num_filas = tbody.children('tr.producto').length;
        if(num_filas > 0)
        {
            if($('#nit_ci').val() != '' && $('#nit_ci').val() != null)
            {
                $(this).text("Cargando...");
                $(this).prop('disabled','disabled');
                realizarVenta();
            }
            else{
                let error = `<label id="nit_ci-error" class="error" for="nit_ci">Este campo es obligatorio.</label>`;
                let label_error = $('#nit_ci').parents('.form-group').children('label.error');
                if(label_error.length == 0)
                {
                    $('#nit_ci').parents('.form-line').addClass('error');
                    $('#nit_ci').parents('.form-group').append(error);
                    $('#nit_ci').focus();
                }
            }
        }
        else{
            swal('No hay productos en el carrito.', 'Por favor registre por lo menos un producto.', "info");
        }
    });

    $('#nit_ci').keyup(function(){
        if($(this).val() != '')
        {
            $(this).parents('.form-line').removeClass('error');
            let label_error = $(this).parents('.form-group').children('label.error');
            $(this).parents('.form-group').remove(label_error);
        }
    });

    // RECALCULAR EL TOTAL CUANDO SE CAMBIE EL DESCUENTO
    $('#productos').on('change','tr.producto td select.simbolo_desc',function(){
        calculaSubTotal();
    });
});

// FUNCION PARA AGREGAR UN PRODUCTO
function agregaProducto(url,rfid)
{
    let tbody = $('#productos');
    let no_hay = tbody.children('tr.no_hay');
    let fila_total = tbody.children('tr.total');
    let num_filas = tbody.children('tr.producto').length;
    let select_descuentos = $('#select_descuentos');
    $.ajax({
        type: "get",
        url: url,
        data: {rfid:rfid},
        dataType: "json",
        success: function (response) {
            if(response.msg == 'Bien')
            {
                let fila = `<tr class="producto ${response.rfid}">
                                <td>1</td>
                                <td>
                                    ${response.producto}
                                    <input type="text" value="${response.producto_id}" class="producto_id" hidden/>
                                    <input type="text" value="${response.rfid}" class="rfid" hidden/>
                                </td>
                                <td>${response.precio}</td>
                                <td>
                                    <select class="simbolo_desc">
                                    ${select_descuentos.html()}
                                    </select>
                                    <input type="text" value="" class="descuento_fila" hidden/>
                                </td>
                                <td>1</td>
                                <td>${response.precio}</td>
                            </tr>`;
                let existe = tbody.children('tr.producto.'+response.rfid);
                if(existe.length == 0)
                {
                    if(num_filas == 0)
                    {
                        no_hay.remove();
                        fila_total.before(fila);
                    }
                    else{
                        fila_total.before(fila);
                    }
                    showNotification('alert-success', 'PRODUCTO AGREGADO. ','bottom', 'left', 'animated bounceInLeft', 'animated bounceOutLeft');
                    contarProductos();
                    calculaSubTotal();
                }
            }   
            else{
                if(response.msg == 'Vendido')
                {
                    swal('El producto con ese código ya se vendió.', 'Intente nuevamente.', "info");
                }
                else{
                    swal('No existe ningún producto con ese código.', 'Intente nuevamente.', "warning");
                }
            }
        }
    });    
}

// FUNCION PARA ELIMINAR UN PRODUCTO
function eliminarProducto()
{
    $(this).remove();
    let tbody = $('#productos');
    let num_filas = tbody.children('tr.producto').length;
    let no_hay = `<tr class="no_hay">
                    <td colspan="6">No hay productos en el carrito</td>
                </tr>`;
    if(num_filas == 0)
    {
        let fila_total = tbody.children('tr.total');
        fila_total.before(no_hay)
        fila_total.children('td').eq(2).text("0");
    }
    else{
        contarProductos();
        calculaTotal();
    }
}

// FUNCION PARA ENUMERAR LOS PRODUCTOS
function contarProductos()
{
    let num = 1;
    let tbody = $('#productos');
    let filas_productos = tbody.children('tr.producto');
    filas_productos.each(function(){
        $(this).children('td').eq(0).text(num);
        num++;
    });
}

// FUNCION PARA CALCULAR EL TOTAL DE LA VENTA
function calculaSubTotal()
{
    let tbody = $('#productos');
    let filas_productos = tbody.children('tr.producto');
    filas_productos.each(function(){
        //obtener el descuento de la fila
        let url_descuento = $('#url_descuento').val();
        var simbolo = $(this).children('td').eq(3).children('select.simbolo_desc').val();
        var fila = $(this);
        obtieneDescuentos(simbolo,fila,url_descuento);
    });
}

function obtieneDescuentos(simbolo,fila,url)
{
    $.ajax({
        type: "get",
        url: url,
        data: {simbolo:simbolo},
        dataType: "json",
        success: function (response) {
            let input  = fila.children('td').eq(3).children('input.descuento_fila');
            input.addClass('asdasd');
            input.val(response.descuento);
            let descuento = parseFloat(fila.children('td').eq(3).children('input.descuento_fila').val());
            descuento = parseFloat(fila.children('td').eq(2).text()) - parseFloat(fila.children('td').eq(2).text()) * descuento;
            fila.children('td').eq(5).text(descuento.toFixed(2));
            calculaTotal();
        }
    });
}

function calculaTotal()
{
    let total = 0;
    let cant = 0;
    let tbody = $('#productos');
    let filas_productos = tbody.children('tr.producto');
    let fila_total = tbody.children('tr.total');
    filas_productos.each(function(){
        // sumando los totales
        total = total + parseFloat($(this).children('td').eq(5).text());
        cant = cant + parseInt($(this).children('td').eq(4).text());
    });
    fila_total.children('td').eq(2).text(total.toFixed(2));
    fila_total.children('td').eq(1).text(cant);
}

// FUNCION PARA REGISTRAR LA VENTA
function realizarVenta()
{
    let array_cantidad = new Array();
    let array_precio = new Array();
    let array_descuento = new Array();
    let array_subtotal = new Array();
    let array_producto_id = new Array();
    let array_rfid = new Array();
    let tbody = $('#productos');
    let filas_productos = tbody.children('tr.producto');
    let fila_total = tbody.children('tr.total');
    let fila_total_final = tbody.children('tr.total_final');
    // OBTENER TODOS LOS PRODUCTOS
    filas_productos.each(function(){
        let producto_id = $(this).children('td').eq(1).children('input.producto_id').val();
        let precio = $(this).children('td').eq(2).text();
        let descuento = $(this).children('td').eq(3).children('select.simbolo_desc').val();
        let cantidad = $(this).children('td').eq(4).text();
        let sub_total = $(this).children('td').eq(5).text();
        let rfid = $(this).children('td').eq(1).children('input.rfid').val();
        array_cantidad.push(cantidad);
        array_precio.push(precio);
        array_descuento.push(descuento);
        array_subtotal.push(sub_total);
        array_producto_id.push(producto_id);
        array_rfid.push(rfid);
    });
    // OBTENER EL TOTAL
    let total = fila_total.children('td').eq(2).text();
    // DATOS DEL CLIENTE
    let nom_cli = $('#nom_cli').val();
    let nit_ci = $('#nit_ci').val();
    if(nom_cli == '' || nom_cli == null)
    {
        nom_cli = "S/N";
    }
    if(nit_ci == '' || nit_ci == null)
    {
        nit_ci = "S/N";
    }
    // OBTENER DESCUENTO
    let descuento_sim = $('#descuento_sim').val();

    // ENVIAR DATOS POR AJAX
    let url_store = $('#url_store').val();
    // console.log(array_cantidad);
    // console.log(array_precio);
    console.log(array_subtotal);
    // console.log(array_producto_id);
    let data = {'array_cantidad':array_cantidad,
                'array_precio':array_precio,
                'array_descuento':array_descuento,
                'array_subtotal':array_subtotal,
                'array_producto_id':array_producto_id,
                'array_rfid':array_rfid,
                'total':total,
                'nom_cli':nom_cli,
                'nit_ci':nit_ci,
                'descuento_sim':descuento_sim};
    $.ajax({
        headers:{'X-CSRF-TOKEN':$('#token').val()},
        type: "post",
        url: url_store,
        data: data,
        dataType: "json",
        success: function (response) {
            // console.log(response.msg);
            if(response.msg == 'Bien')
            {
                swal('Venta realizada con éxito.', '', "success");
                window.location.href = response.url;
                console.log(response.msg);
            }
        }
    });
}