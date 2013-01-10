var ah = $("#dw-shell-load").innerHeight();
var aw = $("#dw-shell-load").innerWidth();  
var DwDiv = 'dw-shell-content';         
/** Message **/
$("div.dw-message").on({ mouseenter: function(){ $(this).addClass("dw-blur dw-opacity"); }, mouseleave: function(){ $(this).removeClass("dw-blur dw-opacity"); } });
/** Buttons forward y back **/
$(function() { $(".btn-back").on('click', function(event) { history.back();}); $(".btn-forward").on('click', function(event) { history.forward();});   });
/** Enlazo la url **/
$(document).ready(function() { if (typeof window.history.pushState == 'function') { DwPushState();} else { DwCheckHash();DwHashChange(); } });
$(function() { $('.nav-list-phone').on('click', '.dropdown-toggle', function(){ $('.nav-list-phone').css('height', 'auto');});})
$(function() {
    $('body').on('click', '.dw-ajax', function(e) {        
        e.preventDefault();
        var este = $(this);
        if(este.hasClass('no-ajax')) {
            if(este.attr('href') != '#' && este.attr('href') != '#/' && este.attr('href') != '#!/') {
                location.href = ""+este.attr('href')+"";                
            }            
        }
        if(este.hasClass('no-load') || este.hasClass('dw-confirm') || este.hasClass('dw-dialog')) {
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
* Función que actualiza la url con popstate o hasbang
*/
function DwUpdateUrl(url) { 
    url = url.split($.KumbiaPHP.publicPath); 
    url = (url.length > 1) ? url[1] : url[0];
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
        if (!window.history.ready && !event.originalEvent.state)
            return;
        $.dwload({url: location.href});                
    });
}

/**
 * Función que verifica el hash, se utiliza cuando no soporta el popstate
 */
function DwCheckHash(){    
    var direccion = ""+window.location+"";
    var nombre = direccion.split("#!/");
    if(nombre.length > 1){
        var url = nombre[1];
        rest = $.dwload({url: url});
    }
}
/**
 * Función que cambia actualiza el content cuando cambia el hash
 */
function DwHashChange() {  
    var prev = ""+window.location.hash+"";
    // Función para determinar cuando cambia el hash de la página.
    $(window).bind("hashchange",function() {        
        var hash = ""+window.location.hash+"";
        hash = hash.replace("#!/","");
        prev = prev.replace("#!/","");        
        if(hash!=prev){
            if(hash && hash!="") {
                rest = $.dwload({url: hash});                
            } else {
                rest = $.dwload({url: window.location});                
            }
            if(!res) {
                window.location.hash = (prev) ? prev : '#!/';
            }
        }                 
    });
}
function DwConsole(text) { if($("#dw-console").length == 0) { $("#dw-shell-load").prepend('<div id="dw-console" class="container"></div>'); } $("#dw-console").append('<p>'+text+'</p>'); }
function DwCheckLength(contenedor,campo, texto, min) { if (campo.val().length <= min ) { campo.addClass('error'); DwUpdateTips(contenedor, texto+' debe tener mínimo '+(min+1)+' caracteres.'); campo.focus(); return false; } else { return true; } }
function DwCheckRegexp(contenedor,campo,regexp,texto) { if ( !( regexp.test(campo.val() ) ) ) { campo.addClass('error'); DwUpdateTips(contenedor, texto); campo.focus(); return false; } else { return true; } }
function DwUpdateTips(contenedor,texto) { contenedor.html('<span class="label label-important">'+texto+'</span>');  }
function DwUcWords(string){ var arrayWords; var returnString = ""; var len; arrayWords = string.split(" "); len = arrayWords.length; for(i=0;i < len ;i++){ if(i != (len-1)){ returnString = returnString+ucFirst(arrayWords[i])+" "; } else{ returnString = returnString+ucFirst(arrayWords[i]); } } return returnString; }
function DwUcFirst(string){ return string.substr(0,1).toUpperCase()+string.substr(1,string.length).toLowerCase(); }