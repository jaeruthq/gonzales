var hora = null;
var minutos = null;
var am_pm = null;
var dia = null;
var mes = null;
var anio = null;

var datos = null;
var seccion = null;

var contador = 10;
var conteo = null;

var busqueda = null;

$(document).ready(function () {
    obtener_seccion();
    $('#rfid').focus();
    setInterval(reloj,1000);

    $('#rfid').click(function(){
        $(this).select();
    });

    $('#rfid').keypress(function (e) {
        let code = (e.keyCode ? e.keyCode : e.which); 
        if(code == 13){
            cargaAccion();
            return false; 
        }
    });

    // CARGAR MAPEO
    $('#seccion').change(function(){
        obtener_seccion();
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
            $('#txtMapeo').val(elemento_id);

            swal({
                title: 'CONFIRMAR',
                text: `${seccion}: ${$(this).children('input.nom').val()}`,
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: "green",
                confirmButtonText: 'CONFIRMAR',
                cancelButtonText: 'CANCELAR',
                closeOnConfirm: false
              }, function () {
                recolecta();
                guardaIngresoSalida();
                $('#m_ingreso2').modal('hide');
                $('#rfid').val('');
                $('#rfid').focus();
              });

        }
        else{
            $('.elemento').removeClass('seleccionado');
            $('#txtMapeo').val('');
        }
    });
});

function obtener_seccion()
{
    $.ajax({
        type: "get",
        url: $('#url_seccion').val(),
        data: {id:$('#seccion').val()},
        dataType: "json",
        success: function (response) {
            seccion = response;
        }
    });
}

function cargaAccion()
{
    $.ajax({
        type: "get",
        url: $('#url_accion').val(),
        data: {rfid:$('#rfid').val()},
        dataType: "json",
        success: function (response) {
            console.log(response);
            if(response.msg == 'bien')
            {
                $('#accion').text(response.accion);
                $('#txtAccion').val(response.accion);
                $('#txtHora').val(`${hora}:${minutos}`);
                $('#txtFecha').val(`${anio}-${mes}-${dia}`);
                $('#vehiculo_id').val(response.vehiculo_id);
                $('#marcado').text(`MARCÓ A HRS.: ${$('#txtHora').val()} ${am_pm}`);
                if(response.accion == 'INGRESO')
                {

                    // DETERMINAR EL TIPO DE FORMULARIO Y RESPUESTA
                    let modal = response.modal;
                    if(modal == 'modal2')
                    {
                        $('#seccion').prop('required',true);
                        $('#txtMapeo').prop('required',true);
                        cargaMapeo();
                        $('#m_ingreso2').modal('show');
                        $('#accion').addClass('ingreso');
                        $('#accion').removeClass('salida');
                    }
                    else{
                        $('#seccion').prop('required',false);
                        $('#txtMapeo').prop('required',false);
                        // mostrar el modal de ingreso 1
                        // cargar los datos
                        $('#vehiculo_placa').text(response.vehiculo);
                        $('#seccion_asiganada').text(response.disponible.seccion);
                        $('#mapeo_asignado').text(response.disponible.mapeo);
                        $('#m_ingreso1').modal('show');
                        iniciaConteo();
                        $('#accion').addClass('ingreso');
                        $('#accion').removeClass('salida');
                    }
                }
                else if(response.accion == 'SALIDA'){
                    $('#accion').removeClass('ingreso');
                    $('#accion').addClass('salida');
                    cargarTarifa();
                }
                else if(response.accion == 'SALIDA MENSUAL'){
                    $('#rfid').val('');
                    $('#rfid').focus();
                    swal({
                        title: "CORRECTO",
                        text: "REGISTRO EXITOSÓ.",
                        timer: 2500,
                        showConfirmButton: false,
                        type:'success'
                    });
                }else if(response.accion == 'INGRESO HISTORICO')
                {
                    // mostrar el modal de ingreso 1
                    // cargar los datos
                    $('#vehiculo_placa').text(response.vehiculo);
                    $('#seccion_asiganada').text(response.seccion);
                    $('#mapeo_asignado').text(response.mapeo);
                    $('#m_ingreso1').modal('show');
                    iniciaConteo();
                    $('#accion').addClass('ingreso');
                    $('#accion').removeClass('salida');
                }
            }
            else{
                guardaPendiente();
                // swal('ERROR!','NO SE ENCONTRÓ NINGUN VEHICULO CON ESE CÓDIGO RFID. INTENTE NUEVAMENTE.', "error");
            }
        }
    });
}

function cargaMapeo()
{
    let seccion = $('#seccion').val();
    $.ajax({
        type: "get",
        url: $('#url_mapeo').val(),
        data: {seccion:seccion,sw:$('#sw').val()},
        dataType: "json",
        success: function (response) {
            $('#contenedor').html(response)
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

function guardaPendiente()
{
    $.ajax({
        headers: {'X-CSRF-TOKEN':$('#token').val()},
        type: "POST",
        url: $('#url_GuardaPendiente').val(),
        data: {rfid:$('#rfid').val()},
        dataType: "json",
        success: function (response) {
            if(response)
            {
                swal({
                    title: "ATENCIÓN",
                    text: "SU VEHÍCULO AUN NO ESTA REGISTRADO. POR FAVOR ACERQUESE AL ENCARGADO Y REGISTRE LOS DATOS DEL MISMO.",
                    timer: 7000,
                    showConfirmButton: false,
                    type:'info'
                });
                iniciaBusqueda();
            }
            else{
                swal({
                    title: "ERROR",
                    text: "ALGO SALIÓ MAL. INTENTE NUEVAMENTE",
                    timer: 2500,
                    showConfirmButton: false,
                    type:'error'
                });
            }
        }
    });
}

function guardaIngresoSalida()
{
    $.ajax({
        headers: {'X-CSRF-TOKEN':$('#token').val()},
        type: "POST",
        url: $('#url_guarda').val(),
        data: datos,
        dataType: "json",
        success: function (response) {
            if(response.msg == 'ingreso')
            {
                swal({
                    title: "CORRECTO",
                    text: "REGISTRO EXITOSÓ.",
                    timer: 2000,
                    showConfirmButton: false,
                    type:'success'
                });
                // swal('CORRECTO','REGISTRO EXITOSÓ.', "success");
                $('#rfid').val('');
                $('#rfid').focus();
                limpiar();
                $('#marcado').text('');
                datos = null;
            }
            else if(response.msg == 'salida'){
                swal({
                    title: "CORRECTO",
                    text: "REGISTRO EXITOSÓ. PIDA SU FACTURA",
                    timer: 2500,
                    showConfirmButton: false,
                    type:'success'
                });
                $('#rfid').val('');
                $('#rfid').focus();
                limpiar();
                $('#marcado').text('');
                datos = null;
            }
            else{
                swal({
                    title: "ERROR",
                    text: "ALGO SALIÓ MAL. INTENTE NUEVAMENTE",
                    timer: 2500,
                    showConfirmButton: false,
                    type:'error'
                });
            }
        }
    }).fail(function(){
        swal({
            title: "ERROR DE SISTEMA",
            text: "ALGO SALIÓ MAL. INTENTE MAS TARDÉ.",
            timer: 2500,
            showConfirmButton: false,
            type:'error'
        });
    });
}

function cargarTarifa()
{
    data = {
        'rfid' : $('#rfid').val(),
        'fecha' : $('#txtFecha').val(),
        'hora' : $('#txtHora').val(),
    };

    $.ajax({
        type: "get",
        url: $('#url_tarifa').val(),
        data: data,
        dataType: "json",
        success: function (response) {
            
                if(response.msg == 'SI')
                {
                    setTimeout(function(){
                        recolecta();
                        // console.log(datos);
                        guardaIngresoSalida();
                    },700);
                }
                else if(response.msg == 'NO COBRAR')
                {
                    // swal(titulo, mensaje, "success");
                    $('#btnGuardar').prop('disabled',true);
                    swal({
                        title: "ATENCIÓN",
                        text: "EL TIEMPO TRANSCURIDO ES MINIMO",
                        timer: 4000,
                        showConfirmButton: false,
                        type:'info'
                    });
                }
                else{
                    swal({
                        title: "ERROR",
                        text: "NO SE ENCONTRO NINGÚN REGISTRO DE INGRESO DEL VEHICULO SELECCIONADO. INTENTE NUEVAMENTE",
                        timer: 4000,
                        showConfirmButton: false,
                        type:'error'
                    });
                }
                
                $('#txtTotal').val(response.total);
                $('#txtTiempo').val(response.tiempo);
                $('#txtHoraIngreso').val(response.horaIngreso);
                $('#txtFechaIngreso').val(response.fechaIngreso);
                $('#a_nombre').val(response.a_nombre);
                $('#nit').val(response.nit);
            }
    });
}

function recolecta()
{
    datos = {
        vehiculo_id     : $('#vehiculo_id').val(),
        hora            : $('#txtHora').val(),
        fecha_reg       : $('#txtFecha').val(),
        accion          : $('#txtAccion').val(),
        mapeo_id        : $('#txtMapeo').val(),
        txtTotal        : $('#txtTotal').val(),
        txtTiempo       : $('#txtTiempo').val(),
        txtHoraIngreso  : $('#txtHoraIngreso').val(),
        txtFechaIngreso : $('#txtFechaIngreso').val(),
        a_nombre        : $('#a_nombre').val(),
        nit             : $('#nit').val(),
    };
}

function limpiar()
{
    $('#vehiculo_id').val('');
    $('#txtHora').val('');
    $('#txtFecha').val('');
    $('#txtAccion').val('');
    $('#txtMapeo').val('');
}

function reloj()
{
    let meses = new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");;
    let fecha_hora = new Date();
    dia = (fecha_hora.getDate() < 10)? '0'+fecha_hora.getDate():fecha_hora.getDate();
    mes = fecha_hora.getMonth();
    anio = fecha_hora.getFullYear();
    $('#fecha').text((dia + " de " + meses[mes] + " de " + anio));
    mes++;
    mes = (mes < 10)? '0'+mes:mes;
    hora = (fecha_hora.getHours() < 10)? '0'+fecha_hora.getHours():fecha_hora.getHours();
    minutos = (fecha_hora.getMinutes() < 10)? '0'+fecha_hora.getMinutes():fecha_hora.getMinutes();
    let segundos = (fecha_hora.getSeconds() < 10)? '0'+fecha_hora.getSeconds():fecha_hora.getSeconds();
    am_pm = (fecha_hora.getHours() < 12) ? 'a.m.':'p.m.';
    $('#reloj').html(`${hora} : ${minutos} : ${segundos} ${am_pm}`);
}

function iniciaConteo()
{
    contador = 10;
    conteo = setInterval(function(){
    $('#conteo').text(contador);
        contador--;
        if(contador == -1)
        {
            clearInterval(conteo);
            $('#m_ingreso1').modal('hide');
            $('#rfid').val('');
            $('#rfid').focus();
        }
    },1000);
}

// FUNCIONES PARA NUEVOS VEHICULOS QUE INGRESAN
function compruebaRegistro()
{
    $.ajax({
        type: "get",
        url: $('#url_compruebaRegistro').val(),
        data: {rfid:$('#rfid').val()},
        dataType: "json",
        success: function (response) {
            if(response)
            {
                detieneBusqueda();
                cargaAccion();
            }
        }
    });
}

function iniciaBusqueda()
{
    busqueda = setInterval(compruebaRegistro,1000);
}

function detieneBusqueda()
{
    clearInterval(busqueda);
}