var ah = $("#dw-shell-load").innerHeight();
var aw = $("#dw-shell-load").innerWidth();  
var DwDiv = 'dw-shell-content';
var prevHash = false;
/** Función para mostrar los mensajes del sistema manualmente */
function DwMessage(message, type, appendTo) {  type = (type==null) ? 'warning' : type; container = (appendTo==null) ? 'dw-message' : appendTo; $('#'+container).append('<div class="alert alert-block alert-'+type+'"><button class="close" data-dismiss="alert" type="button">×</button>'+message+'</div>'); }
/** Ajax Cursor **/
$("body").ajaxStart(function() { $("body").css("cursor", "wait"); }).ajaxStop(function() { $("body").css("cursor", "default"); });
/** Message **/
$(function() { $("div.dw-message").live({ mouseenter: function(){ $(this).addClass("dw-blur dw-opacity"); }, mouseleave: function(){ $(this).removeClass("dw-blur dw-opacity"); } }); });
/** Buttons forward y back **/
$(function() { $("body").on('click', '.btn-back', function(event) { history.back();}); $("body").on('click', '.btn-forward', function(event) { history.forward();});   });
/** Enlazo la url **/
$(document).ready(function() { 
    if (typeof window.history.pushState == 'function') { 
        DwPushState(); 
    } else {
        DwCheckHash(); DwHashChange(); 
    } 
});
$(function(){ 
    $('body').on('click', '.btn-list-phone', function(){ 
        if($('.nav-list-phone').height() == 0) {
            setTimeout(function(){ $('.nav-list-phone').css('height', 'auto') }, 100);
        }        
    });
})
$(function() {
    $('body').on('click', '.dw-ajax', function(e) {        
        e.preventDefault();
        var este = $(this);
        if(este.hasClass('no-ajax')) {
            if(este.attr('href') != '#' && este.attr('href') != '#/' && este.attr('href') != '#!/') {
                location.href = ""+este.attr('href')+"";                
            }            
        }
        if(este.hasClass('no-load') || este.hasClass('dw-confirm') || este.hasClass('dw-dialog') || este.hasClass('js-confirm')) {
            return false;
        }        
        var val = true;
        var capa = (este.attr('data-div')!=undefined) ? este.attr('data-div') : 'dw-shell-content';
        var spinner = este.hasClass('dw-spinner') ? true : false;        
        var change_url = este.hasClass('dw-no-change') ? false : true;
        var message = este.hasClass('dw-no-message') ? false : true;
        var url = este.attr('href');
        var before_load = este.attr('before-load');//Callback antes de enviar
        var after_load = este.attr('after-load');//Callback después de enviar        
        if(before_load!=null) {
            try { val = eval(before_load); } catch(e) { }
        }               
        if(val) {
            prevHash = true;//Por si se utiliza el hashbang
            //@TODO Revisar la seguridad acá
            if(url!=$.KumbiaPHP.publicPath+'#' && url!=$.KumbiaPHP.publicPath+'#/' && url!='#' && url!='#/') {                         
                options = { capa: capa, spinner: spinner, msg: message, url: url, change_url: change_url};            
                if($.dwload(options)) {                     
                    if(after_load!=null) {                        
                        try { eval(after_load); } catch(e) { }                    
                    }                     
                }
            }            
        } 
        return true;
    });    
});

/** Mustra/Oculta el spinner **/
function DwSpinner(action, target) {     
    if(target==null) { 
        target='dw-spinner'; 
    }                 
    if(action=='show') {         
        $("#dw-spinner").attr('style','top: 50%; left:50%; margin-left:-50px; margin-top:-50px;'); 
        $("#dw-shell-load").addClass('dw-blur'); 
        $("#dw-loading-content").show(); 
        $("#"+target).show().spin('large', 'white'); 
    } else { 
        $("#dw-loading-content").hide(); 
        $("#dw-shell-load").removeClass('dw-blur');
        $("#"+target).hide().spin(false); 
    } 
}

/**
* Función que actualiza la url con popstate, hashbang o normal
*/
function DwUpdateUrl(url) { 
    /** Se quita el public path de la url */
    if($.KumbiaPHP.publicPath != '/') {
        url = url.split($.KumbiaPHP.publicPath); 
        url = (url.length > 1) ? url[1] : url[0];    
    } else  {
        url = ltrim(url, '/');
    }    
    if(typeof window.history.pushState == 'function') { 
        url = $.KumbiaPHP.publicPath+url;        
        history.pushState({ path: url }, url, url); 
    } else {         
        window.location.hash = "#!/"+url;
    }
    return true; 
}

/**
 * Función que cambia la url, si el navegador lo soporta
 */
function DwPushState(){             
    // Función para enlazar cuando cambia la url de la página.
    $(window).bind('popstate', function(event) {                   
        if (!event.originalEvent.state)//Para Chrome
            return;          
        $.dwload({url: location.pathname});        
    });
}

/**
 * Función que verifica el hash, se utiliza cuando no soporta el popstate
 */
function DwCheckHash(){   
    var direccion = ""+window.location+"";
    var nombre = direccion.split("#!/");
    if(nombre.length > 1){ 
        direccion = '/'+ltrim(nombre[1], '/');
        $.dwload({url: direccion});
    }
}
/**
 * Función que cambia actualiza el content cuando cambia el hash
 */
function DwHashChange() {     
    // Función para determinar cuando cambia el hash de la página.        
    $(window).bind("hashchange",function(event) {   
        if(prevHash) {
            prevHash = false;
            return;
        }
        console.log(event.originalEvent.oldURL)
        console.log(event.originalEvent.newURL)        
        var hash = ""+window.location.hash+"";
        hash = hash.replace("#!/","");
        if(hash && hash!="") {
            $.dwload({url: hash});
        }
    });
}
function DwConsole(text) { if($("#dw-console").length == 0) { $("#dw-shell-load").prepend('<div id="dw-console" class="container"></div>'); } $("#dw-console").append('<p>'+text+'</p>'); }
function DwCheckLength(contenedor,campo, texto, min) { if (campo.val().length <= min ) { campo.addClass('error'); DwUpdateTips(contenedor, texto+' debe tener mínimo '+(min+1)+' caracteres.'); campo.focus(); return false; } else { return true; } }
function DwCheckRegexp(contenedor,campo,regexp,texto) { if ( !( regexp.test(campo.val() ) ) ) { campo.addClass('error'); DwUpdateTips(contenedor, texto); campo.focus(); return false; } else { return true; } }
function DwUpdateTips(contenedor,texto) { contenedor.html('<span class="label label-important">'+texto+'</span>');  }
function DwUcWords(string){ var arrayWords; var returnString = ""; var len; arrayWords = string.split(" "); len = arrayWords.length; for(i=0;i < len ;i++){ if(i != (len-1)){ returnString = returnString+ucFirst(arrayWords[i])+" "; } else{ returnString = returnString+ucFirst(arrayWords[i]); } } return returnString; }
function DwUcFirst(string){ return string.substr(0,1).toUpperCase()+string.substr(1,string.length).toLowerCase(); }
function DwPopupReport(url) { var report = window.open(url , 'impresion', "width=800,height=500,left=50,top=50,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO"); report.focus(); }
function DwPopupTicket(url) { var ticket = window.open(url , 'ticket', "width=800,height=500,left=50,top=50,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO"); ticket.focus(); }
function DwDatePicker() { var dp = document.createElement("input"); dp.setAttribute("type", "date"); if(dp.type == 'date') { return true; } else { var inputs = $('input.js-datepicker'); if(!inputs.is('input')) { return true; } inputs.datepicker({format: 'yyyy-mm-dd'}); inputs.datepicker().on('changeDate', function(ev){ $(this).datepicker('hide'); }); } return true; }

/**
 * Funciones para limpiar caracteres al igual que el trim de php
 */
function ltrim(str, opt){
    if(opt) {
        while (str.charAt(0) == opt) 
            str = str.substr(1, str.length - 1); 
    } else {
        while (str.charAt(0) == " ") 
            str = str.substr(1, str.length - 1); 
    }    
    return str;
}
function rtrim(str, opt){ 
    if(opt) {
        while (str.charAt(str.length - 1) == opt) 
            str = str.substr(0, str.length - 1); 
    } else {
        while (str.charAt(str.length - 1) == " ") 
            str = str.substr(0, str.length - 1); 
    }    
    return str;
}
function trim(str, opt){ return rtrim(ltrim(str, opt), opt); }
