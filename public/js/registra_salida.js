var id = 0;
$(document).ready(function () {
    var url = $('#url_stock').val();
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
            let _rfid = $(this).val();
            obtieneStock(url,_rfid);
        }
    });


    /* REGISTRAR SALIDA DEL PRODUCTO CON SU RFID */
    $('#registra').click(function(e){
        e.preventDefault();
        let error1 = `<label id="rfid-error" class="error" for="rfid">Este campo es obligatorio.</label>`;
        let error2 = `<label id="precio_uni-error" class="error" for="rfid">Este campo es obligatorio.</label>`;
        let error3 = `<label id="tipo-error" class="error" for="rfid">Este campo es obligatorio.</label>`;
        let error4 = `<label id="descripcion-error" class="error" for="rfid">Este campo es obligatorio.</label>`;
        let rfid = $('#rfid');
        let precio_uni = $('#precio_uni');
        let tipo = $('#tipo');
        let descripcion = $('#descripcion');
        if(rfid.val() == '' || rfid.val() == null)
        {
            if(!$(rfid).parents('.form-line').hasClass('error'))
            {
                $(rfid).parents('.form-line').addClass('error');
                $(rfid).parents('.form-group').append(error1);
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

        if(tipo.val() == '' || tipo.val() == null)
        {
            if(!$(tipo).parents('.form-line').hasClass('error'))
            {
                $(tipo).parents('.form-line').addClass('error');
                $(tipo).parents('.form-group').append(error3);
            }
        }

        if(descripcion.val() == '' || descripcion.val() == null)
        {
            if(!$(descripcion).parents('.form-line').hasClass('error'))
            {
                $(descripcion).parents('.form-line').addClass('error');
                $(descripcion).parents('.form-group').append(error4);
            }
        }

        if(rfid.val() != '' && precio_uni.val() != '' && tipo.val() != '' && descripcion.val() != '')
        {
            console.log('Registrara');
            let _rfid = rfid.val();
            let _precio_uni = precio_uni.val();
            let _tipo = tipo.val();
            let _descripcion = descripcion.val();
            let url = $('#url_store').val();
            registraSalida(id, _rfid, _precio_uni, _tipo, _descripcion, url);
        }
        else{
            console.log('NO Registrara');
        }
    });
    /* FIN REGISTRAR SALIDA DEL PRODUCTO */
});

function registraSalida(id, rfid, precio_uni, tipo, descripcion, url)
{
    $.ajax({
        type: "POST",
        headers : {'X-CSRF-TOKEN':$('#token').val()},
        url: url,
        data: {id:id,rfid:rfid,precio_uni:precio_uni,tipo:tipo,descripcion:descripcion},
        dataType: "json",
        success: function (response) {
            if(response.msg == 'BIEN')
            {
                showNotification('alert-success', 'REGISTRO ÉXITOSO!!! ','bottom', 'left', 'animated bounceInLeft', 'animated bounceOutLeft');
                $('#stock').text(parseInt($('#stock').text()) - 1);
                $('#rfid').val("");
                $('#precio_uni').val("");
                id = 0;
            }
            else{
                if(response.msg == 'NO EXISTE')
                {
                    swal('El producto con ese código no existe.', 'Utilice otra tarjeta.', "info");
                }
                else{
                    console.log(response.msg);
                    swal('Algo salió mal.', 'Intente más tarde.', "warning");
                }
            }
        }
    });
}

function obtieneStock(url,rfid)
{
    console.log(url);
    let url_imgs = $('#url_imgs').val();
    $.ajax({
        type: "GET",
        url: url,
        data: {rfid:rfid},
        dataType: "json",
        success: function (response) {
            $('#titulo_grupo').text(response.nombre);
            $('#stock').text(response.stock);
            $('#p_venta').text('Bs. '+response.precio);
            $('#imagen_prod').prop('src',url_imgs+'/productos/'+response.imagen);
            id = response.id;
        }
    });
}