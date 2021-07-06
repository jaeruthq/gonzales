$(document).ready(function () {
    $('#is_rfid').focus();

    $('#is_rfid').click(function(){
        $(this).select();
    });

    $('#is_rfid').keypress(function (e) {
        let code = (e.keyCode ? e.keyCode : e.which); 
        if(code == 13){
            cargaAccion();
            return false; 
        }
    });

    // ACTUALIZAR COBROS AL CAMBIO DE LOS INPUTS
    $('[type="time" ]').change(function(){
        cargarTarifa();
    });

    $('[type="date" ]').change(function(){
        cargarTarifa();
    });

    // CARGAR MAPEO
    $('#seccion').change(function(){
        cargaMapeo();
    });

    // SELECCIONAR LUGAR
    $(document).on('click','.contenedor .elemento',function(e){
        e.preventDefault();
        if(!$(this).hasClass('si'))
        {
            $('.elemento').removeClass('seleccionado');
            $(this).addClass('seleccionado');
            $(this).attr('title','Seleccionado');
            $('[data-toggle="tooltip"]').tooltip();
            let elemento_id = $(this).children('input.id').val();
            $('#mapeo_id').val(elemento_id);
        }
        else{
            $('.elemento').removeClass('seleccionado');
            $('#mapeo_id').val('');
        }
    });
});

function cargaAccion()
{
    $.ajax({
        type: "get",
        url: $('#url_accion').val(),
        data: {rfid:$('#is_rfid').val()},
        dataType: "json",
        success: function (response) {
            if(response.msg == 'bien')
            {
                $('#accion').val(response.accion);
                $('#nom_vehiculo').val(response.vehiculo);
                $('#vehiculo_id').val(response.vehiculo_id);
                $('#accion').parent().addClass('focused');
                $('#nom_vehiculo').parent().addClass('focused');
                if(response.accion == 'INGRESO')
                {
                    $('.cobrar').addClass('oculto');
                    $('.seccion').removeClass('oculto');
                    $('#seccion').prop('required',true);
                    $('#mapeo_id').prop('required',true);
                    cargaMapeo();
                }
                else{
                    $('.cobrar').removeClass('oculto');
                    $('.seccion').addClass('oculto');
                    $('#seccion').prop('required',false);
                    $('#mapeo_id').prop('required',false);
                    cargarTarifa();
                }
            }
            else{
                swal('ERROR!','NO SE ENCONTRó NINGUN VEHICULO CON ESE CÓDIGO RFID. INTENTE NUEVAMENTE.', "error");
            }
        }
    });
}

function cargaMapeo()
{
    let seccion = $('#seccion').val();
    $.ajax({
        type: "get",
        url: $('#url_mapeo_ig').val(),
        data: {seccion:seccion,sw:$('#sw').val()},
        dataType: "json",
        success: function (response) {
            $('#contenedor').html(response)
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

function cargarTarifa()
{
    datos = {
        'rfid' : $('#is_rfid').val(),
        'fecha' : $('#fecha_reg').val(),
        'hora' : $('#hora').val(),
    };
    if($('#accion').val() == 'SALIDA')
    {
        $.ajax({
            type: "get",
            url: $('#url_tarifa').val(),
            data: datos,
            dataType: "json",
            success: function (response) {
                
                    if(response.msg == 'SI')
                    {
                        $('#btnGuardar').prop('disabled',false);
                    }
                    else if(response.msg == 'NO COBRAR')
                    {
                        // swal(titulo, mensaje, "success");
                        $('#btnGuardar').prop('disabled',true);
                        swal('ATENCIÓN!','EL TIEMPO TRANSCURIDO ES MINIMO', "info");
                    }
                    else{
                        $('#btnGuardar').prop('disabled',true);
                        swal('ERROR!','NO SE ENCONTRO NINGÚN REGISTRO DE INGRESO DEL VEHICULO SELECCIONADO', "error");
                    }
                    
                    $('#h_ingreso').html(`${response.f_ingreso} ${response.h_ingreso}`);
                    $('#h_salida').html(`${response.f_salida} ${response.h_salida}`);
                    $('#is_tarifa').html(`${response.tarifa} | ${response.precio} Bs. por ${response.tiempo_tarifa} hora(s). `);
                    $('#tiempo').html(`${response.tiempo} hora(s)`);
                    $('#span_total').html(`${response.total} Bs.`);
                    $('#txtTotal').val(response.total);
                    $('#txtTiempo').val(response.tiempo);
                    $('#txt_a_nombre').val(response.a_nombre);
                    $('#txt_nit').val(response.nit);
                    $('#nom_vehiculo').val(`${response.vehiculo} | ${response.propietario}`);
                    $('#txtHoraIngreso').val(response.horaIngreso);
                    $('#txtFechaIngreso').val(response.fechaIngreso);
                }
        });
    }
    else{
        $('.cobrar').addClass('oculto');
    }
}