var pendiente = null;
var ubicacion = null;
var vehiculo_rfid = null;
$(document).ready(function () {
    iniciaBusqueda();

    /* VALIDACIÓN DE CÓDIGO RFID */
    $('#rfid').keypress(function (e) {
        let code = (e.keyCode ? e.keyCode : e.which); 
        if(code == 13){
            return false; 
        }
    });

    // GUARDAR VEHICULO
    $('#btnRegVehiculo').click(function(){
        if(validaVehiculo())
        {
            $('#error_vehiculo').removeClass('oculto');
        }
        else{
            let url = $('#form_vehiculo').attr('action');
            var str = new FormData($('#form_vehiculo')[0]);
            $.ajax({
                contentType: false,
                cache: false,
                processData:false,
                headers:{'X-CSRF-TOKEN':$('#token').val()},
                type: "POST",
                url: url,
                data: str,
                dataType: "json",
                success: function (response) {
                    if(response.sw)
                    {

                        swal({
                            title: 'EXITÓ',
                            text: `VEHÍCULO REGISTRADO CON ÉXITO`,
                            type: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "green",
                            confirmButtonText: 'ACEPTAR',
                            cancelButtonText: '',
                            closeOnConfirm: true
                          }, function () {
                                vehiculo_rfid = response.rfid;
                                limpiarVehiculo();
                                iniciaBusquedaUbicacion();
                                $('#m_propietario').modal('hide');
                                $('#m_vehiculo').modal('hide');
                          });
                    }
                }
            });
        }
    });

    // MOSTRAR MODAL PROPIETARIO
    $('#btnModalProp').click(function(){
        $('#error_vehiculo').addClass('oculto');
        $('#m_vehiculo').modal('toggle');
        $('#m_propietario').modal('toggle');
    });

    // MOSTRAR MODAL VEHICULO
    $('#btnModalVe').click(function(){
        $('#error_propietario').addClass('oculto');
        $('#m_propietario').modal('toggle');
        $('#m_vehiculo').modal('toggle');
    });
    
    // GUARDAR PROPIETARIO
    $('#btnRegProp').click(function(){
        if(validaPropietario())
        {
            $('#error_propietario').removeClass('oculto');
        }
        else{
            let url = $('#form_propietario').attr('action');
            var str = new FormData($('#form_propietario')[0]);
            $.ajax({
                contentType: false,
                cache: false,
                processData:false,
                headers:{'X-CSRF-TOKEN':$('#token').val()},
                type: "POST",
                url: url,
                data: str,
                dataType: "json",
                success: function (response) {
                    if(response)
                    {
                        swal({
                            title: 'EXITÓ',
                            text: `PROPIETARIO REGISTRADO CON ÉXITO`,
                            type: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "green",
                            confirmButtonText: 'ACEPTAR',
                            cancelButtonText: '',
                            closeOnConfirm: true
                          }, function () {
                                limpiarPropietario();
                                $('#registro_vehiculo').addClass('oculto');
                                $('#m_propietario').modal('hide');
                                cargaPropietarios();
                                $('#m_vehiculo').modal('show');
                          });
                    }
                }
            });
        }
    });
});

function getUbicacion()
{   
    $.ajax({
        type: "GET",
        url: $('#url_mapeo').val(),
        data: {rfid:vehiculo_rfid},
        dataType: "json",
        success: function (response) {
            if(response.sw)
            {
                detieneBusquedaUbicacion();
                $('#seccion_asignada').text(response.seccion);
                $('#mapeo_asiganado').text(response.mapeo);
                $('#m_ubicacion').modal('show');
            }
        }
    });

}

function iniciaBusquedaUbicacion()
{
    ubicacion = setInterval(getUbicacion,1000);
}

function detieneBusquedaUbicacion()
{
    clearInterval(ubicacion);
}

function getPendiente()
{
    $.ajax({
        type: "get",
        url: $('#url_obtienePendiente').val(),
        data: {pendiente:'pendiente'},
        dataType: "json",
        success: function (response) {
            if(response.msg == 'SI')
            {
                cargaTipos();
                cargaPropietarios();
                cargaTarifas();
                $('#rfid').parent().addClass('focused');
                $('#rfid').val(response.rfid);
                $('#m_vehiculo').modal('show');
                clearInterval(pendiente);
                $('#registro_vehiculo').removeClass('oculto');
            }
        }
    });
}

function iniciaBusqueda()
{
    pendiente = setInterval(getPendiente,1000);
}

function cargaTipos()
{
    $.ajax({
        type: "GET",
        url: $('#url_cargaTipos').val(),
        data: {data:'data'},
        dataType: "json",
        success: function (response) {
            $('#tipo').html(response);
        }
    });
}

function cargaPropietarios()
{
    $.ajax({
        type: "GET",
        url: $('#url_cargaPropietarios').val(),
        data: {data:'data'},
        dataType: "json",
        success: function (response) {
            $('#propietario_id').html(response);
        }
    });
}


function cargaTarifas()
{
    $.ajax({
        type: "GET",
        url: $('#url_cargaTarifas').val(),
        data: {data:'data'},
        dataType: "json",
        success: function (response) {
            $('#tarifa').html(response);
        }
    });
}

function validaPropietario(){
    let input_required = $('#form_propietario input[required]');
    var sw = false;
    input_required.each(function(){
        if($(this).val() == '')
        {
            sw = true;
            return sw;
        }
    });

    let select_required = $('#form_propietario select[required]');
    select_required.each(function(){
        if($(this).val() == '')
        {
            sw = true;
            return sw;
        }
    });

    return sw;
}

function validaVehiculo(){
    let input_required = $('#form_vehiculo input[required]');
    var sw = false;
    input_required.each(function(){
        if($(this).val() == '')
        {
            sw = true;
            return sw;
        }
    });

    let select_required = $('#form_vehiculo select[required]');
    select_required.each(function(){
        if($(this).val() == '')
        {
            sw = true;
            return sw;
        }
    });

    return sw;
}

function limpiarPropietario()
{
    let inputs = $('#form_propietario input');
    inputs.each(function(){
        $(this).val('');
    });

    let selects = $('#form_propietario select');
    selects.each(function(){
        $(this).val('');
    });
}

function limpiarVehiculo()
{
    let inputs = $('#form_vehiculo input');
    var sw = false;
    inputs.each(function(){
        $(this).val('');
    });

    let selects = $('#form_vehiculo select');
    selects.each(function(){
        $(this).val('');
    });
}