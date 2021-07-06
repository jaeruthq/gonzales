// INICIO GRAFICO
var hoy = new Date();
var fecha_actual = ("0" + hoy.getDate()).slice(-2) + "-" + ("0" + (hoy.getMonth() + 1)).slice(-2) + "-" + hoy.getFullYear();

var options={
    chart:{
        renderTo:'container',
        type:'column',
    },
    title: {
        text: 'UBICACIONES'
    },
    subtitle: {
        text: fecha_actual
    },
    xAxis: {

    },
    yAxis: {
        min: 0,
        title: {
            text: 'UBICACIONES DISPONIBLES'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.0f}.</b></td></tr>',
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
    cargaUbicaciones({seccion:$('#seccion').val()});
    $('#seccion').change(function(){
        let seccion = $(this).val();
        data = {
            seccion : seccion,
        };
        cargaUbicaciones(data);
    });
});

function cargaUbicaciones(datos)
{
    $.ajax({
        type: "get",
        url: $('#url_ubicaciones').val(),
        data: datos,
        dataType: "json",
        success: function (response) {
            options.series = response.datos;
            // options.series.dataLabels = {};
            options.series.colorByPoint = true;
            options.xAxis.categories = response.categorias;
            options.xAxis.crosshair = true;
            chart = new Highcharts.Chart(options);
        }
    });
}
