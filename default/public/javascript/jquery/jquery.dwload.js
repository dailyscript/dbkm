/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Plugin de jquery para obtener elementos elementos y cargarlos en un container
 *
 * @category
 * @package     Javascript
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */
(function($) {
    /**
     *
     * Opciones por defecto
     */
    var defaults = {
        change_url       : true, //Indica si cambia la url por la url de la petición
        async            : false, //Indica si la petición es asíncrona
        timeout          : 45000, //Tiempo de espera
        spinner          : true, //Indica si muestra el sppiner al cargar
        append_data      : false, //Indica si carga con html o append la data
        msj              : true, //Indica si muestra alertas
        response         : null, //Método de respuesta que se espera
        capa            : 'dw-shell-content', //Capa a actualizar
        method          : 'GET', //Método a utilizar
        data            : null  //Data o parámetros a enviar
    };

    /**
     * Objeto para el load
     */
    $.dwload = function(options) {
        //Variable de éxito (solo para peticiones no asíncronas)
        var request = false;
        //Extiendo las opciones
        var opt = $.extend(true, defaults, options);

        //Verifico si muestra el spiner
        if(opt.spinner==true){
            DwSpinner('show');
        }

        //Realizo la petición
        $.ajax({
            type: opt.method, url: opt.url, timeout: opt.timeout, async: opt.async, data: opt.data,
            beforeSend: function(data) {
                $("[rel=tooltip]").tooltip('hide');
            },
            error: function (xhr, text, err) {
                var response = xhr.statusCode().status+" "+xhr.statusCode().statusText;
                if(opt.msg==true) {
                    try {
                        $('#dw-error-ajax').html(response);
                        if($('#dw-info-error-ajax').size() > 0) {
                             $('#dw-info-error-ajax').html(xhr.responseText);
                        }
                        errorAjax();
                    } catch(e) {
                        alert('Oops! Se ha producido un error en la carga\nDetalle del error: '+response);
                    }
                }
                request = false;
            }
        }).success(function() {
            if(opt.change_url == true) {
                DwUpdateUrl(opt.url);
            }
        }).done(function(data) {
            //Verifico si carga la data o la adhiere
            (opt.append_data==true) ? $("#"+opt.capa).append(data) : $("#"+opt.capa).html(data);
            $("[rel=tooltip]").tooltip();
            DwDatePicker();//Actualizo los datepicker
            request = true;
        });

        //Oculto el spinner si está habilitado
        if(opt.spinner==true) {
            DwSpinner('hide');
        }
        //Retorno la variable de éxito
        //Si la petición no es asíncrona retornará un boolean
        return request;
    };
})(jQuery);
