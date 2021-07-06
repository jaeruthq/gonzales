$(document).ready(function () {
    vehiculos();
    ingresos();
    salidas();
    tarifas();
    cobros();
});

function vehiculos()
{
    var fecha_ini = $('#m_vehiculos #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_vehiculos #fecha_fin').parents('.form-group');
    var select1 = $('#m_vehiculos #tarifa').parents('.form-group');
    var select2 = $('#m_vehiculos #tipo').parents('.form-group');

    fecha_ini.hide();
    fecha_fin.hide();
    select1.hide();
    select2.hide();

    $('#m_vehiculos select#filtro').change(function(){
        let filtro = $(this).val();
        switch(filtro)
        {
            case 'TODOS':
                fecha_ini.hide();
                fecha_fin.hide();
                select1.hide();
                select2.hide();
            break;
            case 'TIPO':
                fecha_ini.hide();
                fecha_fin.hide();
                select1.hide();
                select2.show();
            break;
            case 'TARIFA':
                fecha_ini.hide();
                fecha_fin.hide();
                select1.show();
                select2.hide();
            break;
            case 'FECHA':
                fecha_ini.show();
                fecha_fin.show();
                select1.hide();
                select2.hide();
            break;
        }
    });
}


function ingresos()
{
    var fecha_ini = $('#m_ingresos #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_ingresos #fecha_fin').parents('.form-group');
    var hora_ini = $('#m_ingresos #hora_ini').parents('.form-group');
    var hora_fin = $('#m_ingresos #hora_fin').parents('.form-group');
    var select1 = $('#m_ingresos #tarifa').parents('.form-group');
    var select2 = $('#m_ingresos #tipo').parents('.form-group');

    fecha_ini.hide();
    fecha_fin.hide();
    hora_ini.hide();
    hora_fin.hide();
    select1.hide();
    select2.hide();

    $('#m_ingresos select#filtro').change(function(){
        let filtro = $(this).val();
        switch(filtro)
        {
            case 'TODOS':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.hide();
                hora_fin.hide();
                select1.hide();
                select2.hide();
            break;
            case 'TIPO':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.hide();
                hora_fin.hide();
                select1.hide();
                select2.show();
            break;
            case 'TARIFA':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.hide();
                hora_fin.hide();
                select1.show();
                select2.hide();
            break;
            case 'FECHA':
                fecha_ini.show();
                fecha_fin.show();
                hora_ini.hide();
                hora_fin.hide();
                select1.hide();
                select2.hide();
            break;
            case 'HORA':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.show();
                hora_fin.show();
                select1.hide();
                select2.hide();
            break;
        }
    });
}

function salidas()
{
    var fecha_ini = $('#m_salidas #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_salidas #fecha_fin').parents('.form-group');
    var hora_ini = $('#m_salidas #hora_ini').parents('.form-group');
    var hora_fin = $('#m_salidas #hora_fin').parents('.form-group');
    var select1 = $('#m_salidas #tarifa').parents('.form-group');
    var select2 = $('#m_salidas #tipo').parents('.form-group');

    fecha_ini.hide();
    fecha_fin.hide();
    hora_ini.hide();
    hora_fin.hide();
    select1.hide();
    select2.hide();

    $('#m_salidas select#filtro').change(function(){
        let filtro = $(this).val();
        switch(filtro)
        {
            case 'TODOS':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.hide();
                hora_fin.hide();
                select1.hide();
                select2.hide();
            break;
            case 'TIPO':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.hide();
                hora_fin.hide();
                select1.hide();
                select2.show();
            break;
            case 'TARIFA':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.hide();
                hora_fin.hide();
                select1.show();
                select2.hide();
            break;
            case 'FECHA':
                fecha_ini.show();
                fecha_fin.show();
                hora_ini.hide();
                hora_fin.hide();
                select1.hide();
                select2.hide();
            break;
            case 'HORA':
                fecha_ini.hide();
                fecha_fin.hide();
                hora_ini.show();
                hora_fin.show();
                select1.hide();
                select2.hide();
            break;
        }
    });
}

function tarifas()
{
    var fecha_ini = $('#m_tarifas #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_tarifas #fecha_fin').parents('.form-group');
    var select1 = $('#m_tarifas #tarifa').parents('.form-group');

    fecha_ini.hide();
    fecha_fin.hide();
    select1.hide();

    $('#m_tarifas select#filtro').change(function(){
        let filtro = $(this).val();
        switch(filtro)
        {
            case 'TODOS':
                fecha_ini.hide();
                fecha_fin.hide();
                select1.hide();
            break;
            case 'TARIFA':
                fecha_ini.hide();
                fecha_fin.hide();
                select1.show();
            break;
            case 'FECHA':
                fecha_ini.show();
                fecha_fin.show();
                select1.hide();
            break;
        }
    });
}

function cobros()
{
    var fecha_ini = $('#m_cobros #fecha_ini').parents('.form-group');
    var fecha_fin = $('#m_cobros #fecha_fin').parents('.form-group');
    var select1 = $('#m_cobros #tarifa').parents('.form-group');

    fecha_ini.hide();
    fecha_fin.hide();
    select1.hide();

    $('#m_cobros select#filtro').change(function(){
        let filtro = $(this).val();
        switch(filtro)
        {
            case 'TODOS':
                fecha_ini.hide();
                fecha_fin.hide();
                select1.hide();
            break;
            case 'TARIFA':
                fecha_ini.hide();
                fecha_fin.hide();
                select1.show();
            break;
            case 'FECHA':
                fecha_ini.show();
                fecha_fin.show();
                select1.hide();
            break;
        }
    });
}