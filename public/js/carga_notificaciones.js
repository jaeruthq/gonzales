$(document).ready(function() {
    cargaNotificaciones();
    setInterval(cargaNotificaciones,30000)

    $('#marca_visto').click(function(){
        marcaVisto();
        let span_notis = $('#span_notis');
        span_notis.text('0');
        span_notis.removeClass('red');
        // $('#contenedor_notis li').removeClass('visto0');
    });
});

function cargaNotificaciones(){
    let url_notificaciones = $('#url_notificaciones').val();
    let span_notis = $('#span_notis');
    let span_notis_menu = $('#span_notis_menu');
    let contenedor_notis = $('#contenedor_notis');

    var lista_notis = ``;
    var url_notis_foco = $('#url_notis_foco').val()
    // console.log(url_notis_foco);
    $.ajax({
        url: url_notificaciones,
        type: 'GET',
        data: {data:"data"},
    })
    .done(function(resp) {
        // console.log(resp);
        try{
            if(typeof resp.num_noti == 'undefined')
            {
                throw 'No se pueden cargar notificaciones';
            }
            if(resp.num_noti.num_notis > 0)
            {
                span_notis.addClass('red');
                span_notis_menu.removeClass('bg-black');
                span_notis_menu.addClass('bg-red');
            }
            else{
                span_notis.removeClass('red');
                span_notis_menu.removeClass('bg-red');
                span_notis_menu.addClass('bg-black');
            }
            span_notis.text(resp.num_noti.num_notis);
            span_notis_menu.text(`${resp.num_noti.num_notis}`);
            resp.listaNotificacionesAdmin.forEach(function(elemento){
            let icono = '';
            let color = '';

            icono = 'access_alarm';
            color = 'light-green';
            lista_notis += `<li class="visto${elemento.visto}">
                            <a href="${url_notis_foco}/show/${elemento.id}">
                                <div class="icon-circle bg-${color}">
                                    <i class="material-icons">${icono}</i>
                                </div>
                                <div class="menu-info">
                                    <h4>${elemento.accion}: ${elemento.vehiculo}</h4>
                                    <p>
                                        <i class="material-icons">access_time</i> ${tiempoHace(elemento)}
                                    </p>
                                </div>
                            </a>
                        </li>
                        `;

            });
            contenedor_notis.html(lista_notis);
        }catch(excep){
            console.log(excep);
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        // console.log("complete");
    });
}

function tiempoHace(obj_notificacion){
    let tiempo_inicial = obj_notificacion.created_at;
    let hoy = new Date();

    let date_notificacion = tiempo_inicial.substring(0,10).trim();
    let hora_notificacion = tiempo_inicial.substring(11,tiempo_inicial.length).trim();

    let array_date_notifi = date_notificacion.split('-');
    for (let i = 0; i < array_date_notifi.length; i++) {
        array_date_notifi[i] = parseInt(array_date_notifi[i]);
    }
    // console.log(array_date_notifi);

    let array_hora_notifi = hora_notificacion.split(':');
    for (let i = 0; i < array_date_notifi.length; i++) {
        array_hora_notifi[i] = parseInt(array_hora_notifi[i]);
    }
    // console.log(array_hora_notifi);

    let anio = hoy.getFullYear('yyyy');
    let mes = hoy.getMonth() + 1;
    let dia = hoy.getDate();
    let hora = hoy.getHours();
    let minutos = hoy.getMinutes();
    let segundos = hoy.getSeconds();
    let array_hoy_date = [anio,mes,dia];
    let array_hoy_horas = [hora,minutos,segundos];
    // console.log(array_hoy_date);
    // console.log(array_hoy_horas);

    let c_anio = 0;
    let c_mes = 0;
    let c_dia = 0;
    let c_hora = 0;
    let c_mins = 0;
    let c_segs = 0;

    if(array_hoy_date[0] - array_date_notifi[0] > 0)// anios
    {
        c_anio = array_hoy_date[0] - array_date_notifi[0];
        if(c_anio > 1)
        {
            return `Hace ${c_anio} años.`;
        }
        return `Hace ${c_anio} año.`;
    }
    else if(array_hoy_date[1] - array_date_notifi[1] > 0)//meses
    {
        c_mes = array_hoy_date[1] - array_date_notifi[1];
        if(c_mes > 1)
        {   
            return `Hace ${c_mes} meses`;
        }
        return `Hace ${c_mes} mes`;
    }
    else if(array_hoy_date[2] - array_date_notifi[2] > 0)// dias
    {
        c_dia = array_hoy_date[2] - array_date_notifi[2];
        if(c_dia > 1)
        {   
            return `Hace ${c_dia} días`;
        }
        return `Hace ${c_dia} día`;
    }
    else if(array_hoy_horas[0] - array_hora_notifi[0] > 0)// horas
    {
        c_hora = array_hoy_horas[0] - array_hora_notifi[0];
        if(c_hora > 1)
        {   
            return `Hace ${c_hora} horas`;
        }
        return `Hace ${c_hora} hora`;
    }   
    else if(array_hoy_horas[1] - array_hora_notifi[1] > 0)// minutos
    {
        c_mins = array_hoy_horas[1] - array_hora_notifi[1];
        if(c_mins > 1)
        {   
            return `Hace ${c_mins} minutos`;
        }
        return `Hace ${c_mins} minuto`;
    }   
    else if(array_hoy_horas[2] - array_hora_notifi[2] > 0)// segundos
    {
        c_segs = array_hoy_horas[2] - array_hora_notifi[2];
        return `Hace un momento`;
    }      
}

function fechaNormal(fecha)
{
    let res = fecha.split('-').reverse().join('/');
    return res;
}

function marcaVisto()
{
    $.ajax({
        type: "GET",
        url: $('#url_visto').val(),
        data: {data:'data'},
        dataType: "json",
        success: function (response) {
            console.log(response);
        }
    });
}