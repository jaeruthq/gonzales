// INICIO GRAFICO
var hoy = new Date();
var fecha_actual = ("0" + hoy.getDate()).slice(-2) + "-" + ("0" + (hoy.getMonth() + 1)).slice(-2) + "-" + hoy.getFullYear();

var options={
    chart:{
        renderTo:'container',
        type:'column',
    },
    title: {
        text: 'COBROS'
    },
    subtitle: {
        text: fecha_actual
    },
    xAxis: {

    },
    yAxis: {
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.0f} Bs.</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{}]
}

// FIN GRAFICO
var data = null;
$(document).ready(function () {
    cargaCobros({filtro:'todos'});
    cobros();
});

function cobros()
{
    $('#m_cobros select#filtro').change(function(){
        let filtro = $(this).val();
        switch(filtro)
        {
            case 'TODOS':
                $('.fecha').addClass('oculto');
                $('.tarifa').addClass('oculto');
                data = {filtro:'todos'};
                cargaCobros(data); 
            break;
            case 'TARIFA':
                $('.fecha').addClass('oculto');
                $('.tarifa').removeClass('oculto');
                let tarifa = $('#__tarifa').val();
                data = {
                    filtro : 'tarifa',
                    tarifa : tarifa,
                };
                cargaCobros(data);
            break;
            case 'FECHA':
                $('.fecha').removeClass('oculto');
                $('.tarifa').addClass('oculto');

                let fecha_ini = $('#fecha_ini').val();
                let fecha_fin = $('#fecha_fin').val();
                if(fecha_ini == '' || fecha_ini == null || fecha_fin == null || fecha_fin == '')
                {
                    filtro = 'todos';
                }

                data = {
                    filtro : 'fecha',
                    fecha_ini : fecha_ini,
                    fecha_fin : fecha_fin,
                };
                cargaCobros(data);
            break;
        }
    });

    $('#__tarifa').change(function(){
        let tarifa = $(this).val();
        data = {
            filtro : 'tarifa',
            tarifa : tarifa,
        };
        console.log(data);
        cargaCobros(data);
    });

    $('.fecha').change(function(){
        let fecha_ini = $('#fecha_ini').val();
        let fecha_fin = $('#fecha_fin').val();
        if(fecha_ini == '' || fecha_ini == null || fecha_fin == null || fecha_fin == '')
        {
            filtro = 'todos';
        }

        data = {
            filtro : 'fecha',
            fecha_ini : fecha_ini,
            fecha_fin : fecha_fin,
        };
        cargaCobros(data);
    });
}

function cargaCobros(datos)
{
    $.ajax({
        type: "get",
        url: $('#url_cobros').val(),
        data: datos,
        dataType: "json",
        success: function (response) {
            options.options3d = { enabled: true,
                                    alpha: 0,
                                    beta: 10,
                                    depth: 80
                                };
            options.series = [{
                                colorByPoint:true,
                                name: 'TOTAL: '+response.total.toFixed(2)+' Bs.',
                                data:response.datos,
                                dataLabels: {
                                    enabled: true,
                                    rotation: 0,
                                    color: '#000000',
                                    align: 'center',
                                    format: '{point.y:.2f} Bs.', // one decimal
                                    y: 0, // 10 pixels down from the top
                                    style: {
                                        fontSize: '13px',
                                        fontFamily: 'Verdana, sans-serif'
                                    }
                                }
                            }];
            // options.series.data = response.datos;
            options.xAxis = {type:'category',
                                labels: {
                                    skew3d: true,
                                    style: {
                                        fontSize: '12px',
                                    }
                                }
                            };
            options.yAxis = {
                                title: {
                                    text: 'COBROS'
                                }
                            }
            options.xAxis.crosshair = true;
            chart = new Highcharts.Chart(options);
        }
    });
}

