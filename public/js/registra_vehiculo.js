$(document).ready(function () {
    
    $('#rfid').click(function (e) {
        $(this).select();
    });

    /* VALIDACIÓN DE CÓDIGO RFID */
    $('#rfid').keypress(function (e) {
        let code = (e.keyCode ? e.keyCode : e.which); 
        if(code == 13){
            return false; 
        }
    });

    $('#rfid').keyup(function (e) {
        if($(this).val().length % 10 == 0)
        {
            $(this).val($(this).val().substr(($(this).val().length - 10),$(this).val().length));
        }
        else if($(this).val().length > 10){
            $(this).val($(this).val().substr(($(this).val().length - 10),$(this).val().length));
        }
    });
    /* FIN VALIDACIÓN */
});