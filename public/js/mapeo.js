var datos = null;

$(document).ready(function () {

    $('#btnAbreModal').click(function(e){
        e.preventDefault();
        $('#msj_error').addClass('oculto');
        botones(true);
        limpiar();
        $('#titulo_modal').text('AGREGAR');
        $('#modal_mapeo').modal('show');
    });

    $(document).on('click','.contenedor .elemento',function(e){
        e.preventDefault();
        $('#msj_error').addClass('oculto');
        botones(false);
        let id = $(this).children('input.id').val();
        let nom = $(this).children('input.nom').val();
        $('#id_elemento').val(id);
        $('#nom_elemento').val(nom);
        $('#nom_elemento').focus();
        // $('#nom_elemento').select();

        $('#titulo_modal').text('MODIFICAR/ELIMINAR');
        $('#modal_mapeo').modal('show');
    });

    // ACCIONES
    $('#btnAgregar').click(function(e){        
        e.preventDefault();
        accion('guardar');
    });
    $('#btnModificar').click(function(e){        
        e.preventDefault();
        accion('modificar');
    });
    $('#btnEliminar').click(function(e){        
        e.preventDefault();
        accion('eliminar');
    });
});

function cargarElementos()
{
    $.ajax({
        type: "get",
        url: $('#url_carga').val(),
        data: "data",
        dataType: "json",
        success: function (response) {
            $('#contenedor').html(response);
        }
    });
}

function botones(sw)
{
    $('#btnAgregar').prop('disabled',!sw);
    $('#btnModificar').prop('disabled',sw);
    $('#btnEliminar').prop('disabled',sw);
}

function valida()
{
    let sw = false;
    if($('#nom_elemento').val() != '' && $('#nom_elemento').val() != null)
    {
        sw = true;
    }
    return sw;
}

function limpiar()
{
    $('#id_elemento').val('');
    $('#nom_elemento').val('');
}

function recolecta(accion)
{
    datos = {
        seccion : $('#id_seccion').val(),
        id      : $('#id_elemento').val(),
        nom     : $('#nom_elemento').val(),
        accion  : accion
    }
}

var msj = ''
function accion(ac)
{
    recolecta(ac);

    console.log(datos);

    switch(ac)
    {
        case 'guardar':
            msj = 'REGISTRO EXITOSO!';
        break;
        case 'modificar':
            msj = 'MODIFICACIÓN EXITOSA!';
            break;
        case 'eliminar':
            msj = 'ELIMINACIÓN EXITOSA!';
        break;
    }

    let sw = true;
    if(ac != 'eliminar')
    {
        sw = valida();
    }

    if(sw)
    {
        $.ajax({
            headers : {'X-CSRF-TOKEN':$('#token').val()},
            type: "POST",
            url: $('#url_acciones').val(),
            data: datos,
            dataType: "json",
            success: function (response) {
                if(response)
                {
                    cargarElementos();
                    $('#modal_mapeo').modal('hide');
                    showNotification('alert-success', msj,'top', 'right', 'animated bounceInRight', 'animated bounceOutRight');
                }
            }
        });
    }
    else{
        $('#msj_error').removeClass('oculto');
        setTimeout(function(){
            $('#msj_error').addClass('oculto');
        },3500);
    }
}