let contenedor_permisos = $('#contenedor_permisos');
$(document).ready(function () {
    contenedor_permisos.on('click', '.permiso.sin_asignar', function () {
        let permiso = $(this);
        let permiso_id = $(this).attr('data-permiso');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('#token').val()
            },
            type: "POST",
            url: $('#urlStoreUserPermiso').val(),
            data: {
                permiso_id: permiso_id,
            },
            dataType: "json",
            success: function (response) {
                permiso.removeClass('sin_asignar');
                permiso.addClass('correcto');
                showNotification('alert-success', 'ASIGNACIÓN ÉXITOSA', 'top', 'right', 'animated bounceInRight', 'animated bounceOutRight');
                permiso.attr('data-up', response.id);
                setTimeout(function () {
                    permiso.removeClass('correcto');
                    permiso.addClass('asignado');
                    permiso.removeAttr('data-permiso');
                }, 700);
            }
        });
    });

    contenedor_permisos.on('click', '.permiso.asignado', function () {
        let permiso = $(this);
        let url = $(this).attr('data-url');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('#token').val()
            },
            type: "DELETE",
            url: url,
            dataType: "json",
            success: function (response) {
                permiso.removeClass('asignado');
                permiso.addClass('removido');
                showNotification('alert-success', 'ASIGNACIÓN REMOVIDA ÉXITOSAMENTE', 'top', 'right', 'animated bounceInRight', 'animated bounceOutRight');
                permiso.attr('data-permiso', response.id);
                setTimeout(function () {
                    permiso.removeClass('removido');
                    permiso.addClass('sin_asignar');
                    permiso.removeAttr('data-up');
                }, 700);
            }
        });
    });
});
