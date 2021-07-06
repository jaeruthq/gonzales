$(document).ready(function () {
    
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
    });
    /* FIN VALIDACIÓN */

    /* OBTENER STOCK DEL GRUPO */
    $('#producto_id').change(function(){
        let url_stock = $('#url_stock').val();
        let id_pro = $(this).val();
        if(id_pro == null || id_pro == '')
        {
            id_pro = 0;
        }
        obtieneStock(url_stock,id_pro);
        $('#precio_uni').val("");
    });
    /* FIN OBTENER STOCK */

    /* REGISTRAR EL PRODUCTO CON SU RFID */
    $('#registra').click(function(e){
        e.preventDefault();
        let error1 = `<label id="producto_id-error" class="error" for="producto_id">Este campo es obligatorio.</label>`;
        let error2 = `<label id="precio_uni-error" class="error" for="precio_uni">Este campo es obligatorio.</label>`;
        let error3 = `<label id="rfid-error" class="error" for="rfid">Este campo es obligatorio.</label>`;
        let error4 = `<label id="tipo-error" class="error" for="tipo">Este campo es obligatorio.</label>`;
        let error5 = `<label id="nro_fac-error" class="error" for="nro_fac">Este campo es obligatorio.</label>`;
        let error6 = `<label id="codigo-error" class="error" for="codigo">Este campo es obligatorio.</label>`;
        let error7 = `<label id="nro_rec-error" class="error" for="nro_rec">Este campo es obligatorio.</label>`;
        let id_producto = $('#producto_id');
        let precio_uni = $('#precio_uni');
        let rfid = $('#rfid');
        let nro_fac = $('#nro_fac');
        let codigo = $('#codigo');
        let nro_rec = $('#nro_rec');
        let tipo = $('#tipo');
        if(id_producto.val() == '' || id_producto.val() == null)
        {
            if(!$(id_producto).parents('.form-line').hasClass('error'))
            {
                $(id_producto).parents('.form-line').addClass('error');
                $(id_producto).parents('.form-group').append(error1);
            }
        }

        if(precio_uni.val() == '' || precio_uni.val() == null)
        {
            if(!$(precio_uni).parents('.form-line').hasClass('error'))
            {
                $(precio_uni).parents('.form-line').addClass('error');
                $(precio_uni).parents('.form-group').append(error2);
            }
        }

        if(rfid.val() == '' || rfid.val() == null)
        {
            if(!$(rfid).parents('.form-line').hasClass('error'))
            {
                $(rfid).parents('.form-line').addClass('error');
                $(rfid).parents('.form-group').append(error3);
            }
        }

        if(nro_fac.val() == '' || nro_fac.val() == null)
        {
            if(!$(nro_fac).parents('.form-line').hasClass('error'))
            {
                $(nro_fac).parents('.form-line').addClass('error');
                $(nro_fac).parents('.form-group').append(error5);
            }
        }

        if(codigo.val() == '' || codigo.val() == null)
        {
            if(!$(codigo).parents('.form-line').hasClass('error'))
            {
                $(codigo).parents('.form-line').addClass('error');
                $(codigo).parents('.form-group').append(error6);
            }
        }

        if(nro_rec.val() == '' || nro_rec.val() == null)
        {
            if(!$(nro_rec).parents('.form-line').hasClass('error'))
            {
                $(nro_rec).parents('.form-line').addClass('error');
                $(nro_rec).parents('.form-group').append(error7);
            }
        }

        if(tipo.val() == '' || tipo.val() == null)
        {
            if(!$(tipo).parents('.form-line').hasClass('error'))
            {
                $(tipo).parents('.form-line').addClass('error');
                $(tipo).parents('.form-group').append(error4);
            }
        }

        if(id_producto.val() != '' && precio_uni.val() != '' && rfid.val() != '' && tipo.val() != '' && nro_fac.val() != '' && codigo.val() != '' && nro_rec.val() != '')
        {
            console.log('Registrara');
            let _id = id_producto.val();
            let _precio_uni = precio_uni.val();
            let _rfid = rfid.val();
            let _nro_fac = nro_fac.val();
            let _codigo = codigo.val();
            let _nro_rec = nro_rec.val();
            let _tipo = tipo.val();
            let url = $('#url_store').val();
            let data = {id:_id,
                        rfid:_rfid,
                        nro_fac:_nro_fac,
                        codigo:_codigo,
                        nro_rec:_nro_rec,
                        tipo:_tipo,
                        precio_uni:_precio_uni,
                        };
            registraProducto(data,url);
        }
        else{
            console.log('NO Registrara');
        }
    });
    /* FIN REGISTRAR EL PRODUCTO */
});

function registraProducto(data, url)
{
    $.ajax({
        type: "POST",
        headers : {'X-CSRF-TOKEN':$('#token').val()},
        url: url,
        data: data,
        dataType: "json",
        success: function (response) {
            if(response.msg == 'BIEN')
            {
                showNotification('alert-success', 'REGISTRO ÉXITOSO!!! ','bottom', 'left', 'animated bounceInLeft', 'animated bounceOutLeft');
                $('#stock').text(parseInt($('#stock').text()) + 1);
                $('#rfid').val("");
                $('#rfid').focus();
            }
            else{
                if(response.msg == 'EXISTE')
                {
                    swal('El código RFID esta siendo utilizado.', 'Utilice otra tarjeta.', "info");
                }
                else{
                    console.log(response.msg);
                    swal('Algo salió mal.', 'Intente más tarde.', "warning");
                }
            }
        }
    });
}

function obtieneStock(url,id)
{
    let url_imgs = $('#url_imgs').val();
    $.ajax({
        type: "GET",
        url: url,
        data: {id:id},
        dataType: "json",
        success: function (response) {
            $('#titulo_grupo').text(response.nombre);
            $('#stock').text(response.stock);
            $('#p_venta').text('Bs. '+response.precio);
            $('#imagen_prod').prop('src',url_imgs+'/productos/'+response.imagen);
        }
    });
}