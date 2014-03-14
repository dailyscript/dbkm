/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Plugin para jQuery que incluye los callbacks basicos para los Helpers
 *
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license	http://wiki.kumbiaphp.com/Licencia	 New BSD License
 */

(function($) {
    /**
     * Objeto KumbiaPHP
     *
     */
    $.KumbiaPHP = {
        /**
         * Ruta al directorio public en el servidor
         *
         * @var String
         */
        publicPath : null,

        /**
         * Plugins cargados
         *
         * @var Array
         */
        plugin: [],

        /**
         * Muestra mensaje de confirmacion
         *
         * @param Object event
         */
        cConfirm: function(event) {
            event.preventDefault();
            var este=$(this);
            var este_tmp = this;
            var dialogo = $("#modal_confirmar");
            var data_body = este.attr('confirm-body');
            var data_title = este.attr('confirm-title');
            if(data_title==undefined) {
                data_title = 'Confirma';
            }
            if ($("#modal_confirmar").size() > 0 ){
                dialogo.empty();
            }
            dialogo = $('<div id="modal_confirmar"></div>').addClass('modal fade');
            var header = $('<div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3><i class="icon-warning-sign" style="padding-right:5px; margin-top:5px;"></i>'+data_title+'</h3></div>').addClass('modal-header');
            var cuerpo = (data_body!=undefined) ? $('<div><p>'+data_body+'</p></div>').addClass('modal-body') : $('<div><p>Está seguro de continuar con esta operación?</p></div>').addClass('modal-body');
            var footer = $('<div></div>').addClass('modal-footer');
            dialogo.append(header);
            dialogo.append(cuerpo);
            dialogo.append(footer);
            footer.append('<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>');
            if(este.hasClass('dw-ajax')) {
                footer.append('<a class="btn btn-success dw-ajax dw-spinner" href="'+este.attr("href")+'">Aceptar</a>');
            } else {
                footer.append('<button class="btn btn-success">Aceptar</a>');
            }
            $('.btn-success', dialogo).on('click',function(){
                dialogo.modal('hide')
                if(este.attr('on-confirm')!=undefined) {
                    fn = este.attr('on-confirm')+'(este)';
                    eval(fn);
                    return false;
                }
                if(!($(this).hasClass('dw-ajax'))) {
                    document.location.href = este.attr('href');
                }
            });
            dialogo.modal();
            $(dialogo).on('shown', function () {
                $('.btn-success', dialogo).focus();
            })
        },

        /**
         * Aplica un efecto a un elemento
         *
         * @param String fx
         */
        cFx: function(fx) {
            var este=$(this), rel = $('#'+este.data('to'));
            return function(event) {
                event.preventDefault();
                (rel[fx])();
            }
        },

        /**
         * Carga con AJAX
         *
         * @param Object event
         */
        cRemote: function(event) {
            var este=$(this), rel = $('#'+este.data('to'));
            event.preventDefault();
            rel.load(this.href);
        },

        /**
         * Carga con AJAX y Confirmacion
         *
         * @param Object event
         */
        cRemoteConfirm: function(event) {
            var este=$(this), rel = $('#'+este.data('to'));
            event.preventDefault();
            if(confirm(este.data('msg'))) {
                rel.load(this.href);
            }
        },

        /**
         * Enviar formularios de manera asincronica, via POST
         * Y los carga en un contenedor
         */
        cFRemote: function(event){
            DwSpinner('hide');
            event.preventDefault();
            este = $(this);
            var val = true;
            var button = $('[type=submit]', este);
            button.attr('disabled', 'disabled');
            var url = este.attr('action');
            var div = este.attr('data-to');
            var before_send = este.attr('before-send');
            var after_send = este.attr('after-send');
            if(before_send!=undefined) {
                try { val = eval(before_send); } catch(e) { }
            }
            if(!val) {
                button.removeAttr('disabled');
                return false;
            }
            if(este.hasClass('dw-validate')) { //Para validar el formulario antes de enviarlo
                confirmation = este.hasClass('dw-confirm') ? true : false;
                if(!validForm(este.attr('name'), confirmation)) {
                    button.removeAttr('disabled');
                    return false;
                }
            }
            DwSpinner('show');
            $.post(url, este.serialize(), function(data, status){
                var capa = $('#'+div);
                if(after_send!=null) {
                    try { eval(after_send); } catch(e) { }
                }
                capa.html(data).hide().fadeIn(500);
                DwSpinner('hide');
                button.attr('disabled', null);
            });
        },

        /**
         * Carga con AJAX al cambiar select
         *
         * @param Object event
         */
        cUpdaterSelect: function(event) {
            var $t = $(this),$u= $('#' + $t.data('update'))
            url = $t.data('url');
            $u.empty();
            $.get(url, {'id':$t.val()}, function(d){
                for(i in d){
                    var a = $('<option />').text(d[i]).val(i);
                    $u.append(a);
                }
            }, 'json');
        },

        /**
         * Muestra mensaje para seleccionar el tipo de reporte
         *
         * @param Object event
         */
        cReport: function(event) {
            event.preventDefault();
            var este = $(this);
            var reporte = $("#modal_reporte");
            var data_title = este.attr('data-report-title');
            var data_format = este.attr('data-report-format').split('|');
            if(data_title==undefined) {
                data_title = 'Imprmir reporte';
            }
            if ($("#modal_reporte").size() > 0 ){
                reporte.empty();
            }

            var tmp_check = '';
            for(i=0 ; i < data_format.length ; i++) {
                tmp_checked = (i==0) ? 'checked="checked"' : '';
                tmp_check = tmp_check + '<label class="checkbox inline" style="font-size: 12px;"><input name="report-format-type" type="radio" '+tmp_checked+' value="'+data_format[i].toLowerCase()+'" style="margin: 0px;">&nbsp;'+data_format[i].toUpperCase()+'</label>';
            }
            var tmp_form = '<div class="row-fluid"><form>'+tmp_check+'</form></div>';

            //Armo el modal
            reporte = $('<div id="modal_reporte"></div>').addClass('modal fade');
            var header = $('<div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3><i class="icon-warning-sign" style="padding-right:5px; margin-top:5px;"></i>'+data_title+'</h3></div>').addClass('modal-header');
            var cuerpo = $('<div><p>En qué formato deseas ver este reporte?</p><p>Recuerda reciclar el papel</p>'+tmp_form+'</div>').addClass('modal-body');
            var footer = $('<div></div>').addClass('modal-footer');
            reporte.append(header);
            reporte.append(cuerpo);
            reporte.append(footer);
            footer.append('<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>');
            footer.append('<button class="btn btn-success">Aceptar</a>');
            $('.btn-success', reporte).on('click',function(){
                reporte.modal('hide')
                checked = $("input:checked", reporte).val();
                popup_url = rtrim(este.attr('href'), '/')+'/'+checked+'/';
                (checked=='ticket') ? DwPopupTicket(popup_url) : DwPopupReport(popup_url);
            });
            reporte.modal();
        },

        /**
         * Carga y Enlaza Unobstrusive DatePicker en caso de ser necesario
         *
         */
        bindDatePicker: function() {
            // Selecciona los campos input
            var inputs = $('input.js-datepicker');
            // Verifica si hay al menos un campo
            if(!inputs.is('input')) {
                return true;
            }

            /**
            * Funcion encargada de enlazar el DatePicker a los Input
            *
            */
            var bindInputs = function() {
                //Defino el formato YYYY-MM-DD
                inputs.datepicker({format: 'yyyy-mm-dd'});
                //Al seleccionar una fecha se oculte el calendario
                inputs.datepicker().on('changeDate', function(ev){ $(this).datepicker('hide'); });
            }

            // Carga la hoja de estilos
            //$('head').append('<link href="' + this.publicPath + 'css/bootstrap/datepicker.css" type="text/css" rel="stylesheet"/>');

            // Carga DatePicker
            //$.getScript(this.publicPath + 'javascript/bootstrap/bootstrap-datepicker.js', function(){
                bindInputs();
            //});
        },

        /**
         * Enlaza a las clases por defecto
         *
         */
        bind : function() {
            // Enlace y boton con confirmacion
            $("body").on('click', 'a.js-confirm, input.js-confirm', this.cConfirm);

            // Enlace ajax
            $("a.js-remote").on('click', this.cRemote);

            // Enlace ajax con confirmacion
            $("a.js-remote-confirm").on('click', this.cRemoteConfirm);

            // Efecto show
            $("a.js-show").on('click', this.cFx('show'));

            // Efecto hide
            $("a.js-hide").on('click', this.cFx('hide'));

            // Efecto toggle
            $("a.js-toggle").on('click', this.cFx('toggle'));

            // Efecto fadeIn
            $("a.js-fade-in").on('click', this.cFx('fadeIn'));

            // Efecto fadeOut
            $("a.js-fade-out").on('click', this.cFx('fadeOut'));

            // Formulario ajax
            $("body").on('submit', 'form.js-remote', this.cFRemote);

            //Link para reportes
            $("body").on('click', '.js-report', this.cReport);

            // Lista desplegable que actualiza con ajax
            $("select.js-remote").on('change', this.cUpdaterSelect);

            //Se carga el datepicker por compatibilidad con ajax
            $("body").on('focus', 'input.js-datepicker', this.bindDatePicker);

            // Enlazar DatePicker
            this.bindDatePicker();
        },

        /**
         * Implementa la autocarga de plugins, estos deben seguir
         * una convención para que pueda funcionar correctamente
         */
        autoload: function(){
            var elem = $("[class*='jp-']");
            $.each(elem, function(i, val){
                var este = $(this); //apunta al elemento con clase jp-*
                var classes = este.attr('class').split(' ');
                for (i in classes){
                    if(classes[i].substr(0, 3) == 'jp-'){
                        if($.inArray(classes[i].substr(3),$.KumbiaPHP.plugin) != -1)
                            continue;
                        $.KumbiaPHP.plugin.push(classes[i].substr(3))
                    }
                }
            });
            var head = $('head');
            for(i in $.KumbiaPHP.plugin){
                $.ajaxSetup({ cache: true});
                head.append('<link href="' + $.KumbiaPHP.publicPath + 'css/' + $.KumbiaPHP.plugin[i] + '.css" type="text/css" rel="stylesheet"/>');
                $.getScript($.KumbiaPHP.publicPath + 'javascript/jquery/jquery.' + $.KumbiaPHP.plugin[i] + '.js', function(data, text){});
            }
        },

        /**
         * Inicializa el plugin
         *
         */
        initialize: function() {
            // Obtiene el publicPath, restando los caracteres que sobran
            // de la ruta, respecto a la ruta de ubicacion del plugin de KumbiaPHP
            // "javascript/jquery/jquery.kumbiaphp.js"
            var src = $('script:last').attr('src');
            this.publicPath = src.substr(0, src.length - 37);

            // Enlaza a las clases por defecto
            $(function(){
                $.KumbiaPHP.bind();
                $.KumbiaPHP.autoload();
            });
        }
    }

    // Inicializa el plugin
    $.KumbiaPHP.initialize();
})(jQuery);
