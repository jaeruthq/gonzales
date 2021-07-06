var interval = null;
$(document).ready(function () {
    $('#cobros_pendientes').addClass('oculto');
    interval = setInterval(comprueba,1000);
    $('#btnCobrar').click(function(){
        $('#form_salida').submit();
        reiniciar();
        $('#cobros_pendientes').addClass('oculto');
        $('#m_cobro').modal('hide');
    });
});

function comprueba()
{
    $.ajax({
        type: "GET",
        url: $('#url_salidas').val(),
        data: {data:'data'},
        dataType: "json",
        success: function (response) {
            if(response.msg = 'nuevo')
            {
                $('#cobros_pendientes').removeClass('oculto');
                $('#m_cobro').modal('show');
                recolecta(response);
                detener();
            }
        }
    });
}

function detener()
{
    clearInterval(interval)
}

function reiniciar()
{
    interval = setInterval(comprueba,1000);
}

function recolecta(resp)
{
    $('#vehiculo').val(resp.vehiculo);
    $('#propietario').val(resp.propietario);
    $('#fecha_ingreso').val(resp.fecha_ingreso);
    $('#hora_ingreso').val(resp.hora_ingreso);
    $('#fecha_salida').val(resp.fecha_salida);
    $('#hora_salida').val(resp.hora_salida);
    $('#tiempo_cobrado').val(resp.tiempo_cobrado);
    $('#total').val(resp.total);
    $('#a_nombre').val(resp.a_nombre);
    $('#nit').val(resp.nit);
    $('#cobro_id').val(resp.cobro_id);
}

function limpiar()
{
    $('#vehiculo').val('');
    $('#propietario').val('');
    $('#fecha_ingreso').val('');
    $('#hora_ingreso').val('');
    $('#fecha_salida').val('');
    $('#hora_salida').val('');
    $('#tiempo_cobrado').val('');
    $('#total').val('');
    $('#a_nombre').val('');
    $('#nit').val('');
    $('#cobro_id').val('');
}