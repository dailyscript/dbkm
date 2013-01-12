/*
 * This plugin filters keyboard input by specified regular expression.
 * Version 1.7
 * $Id$
 *
 * Source code inspired by Ext.JS (Ext.form.TextField, Ext.EventManager)
 *
 * Procedural style:
 * $('#ggg').keyfilter(/[\dA-F]/);
 * Also you can pass test function instead of regexp. Its arguments:
   * this - HTML DOM Element (event target).
   * c - String that contains incoming character.
 * $('#ggg').keyfilter(function(c) { return c != 'a'; });
 *
 * Class style:
 * <input type="text" class="mask-num" />
 *
 * Available classes:
   * mask-pint:     /[\d]/
   * mask-int:      /[\d\-]/
   * mask-pnum:     /[\d\.]/
   * mask-money     /[\d\.\s,]/
   * mask-num:      /[\d\-\.]/
   * mask-hex:      /[0-9a-f]/i
   * mask-email:    /[a-z0-9_\.\-@]/i
   * mask-alpha:    /[a-z_]/i
   * mask-alphanum: /[a-z0-9_]/i
   * 
   * Update by argordmel: 2012-01-18 - Se ajusta para que filtre mediante el método live
   * para poder filtrar también los formularios cargados con ajax
 */

(function(h){
    var f={
        pint:/[\d]/,
        "int":/[\d\-]/,
        pnum:/[\d\.]/,
        money:/[\d\.\s,]/,
        num:/[\d\-\.]/,
        hex:/[0-9a-f]/i,
        email:/[a-z0-9_\.\-@]/i,
        alpha:/^[a-zA-Z áéíóúÁÉÍÓÚüÜñÑ]+$/i,
        alphanum:/[a-zA-Z0-9_*\-\ áéíóúÁÉÍÓÚüÜñÑ]/i,        
        text:/^[a-z0-9 áéíóúÁÉÍÓÚüÜñÑ.,_:;\-\&\=\*\+\/\#\%\$\"\(\)\@\/\s]+$/i
    };var c={TAB:9,RETURN:13,ESC:27,BACKSPACE:8,DELETE:46};var a={63234:37,63235:39,63232:38,63233:40,63276:33,63277:34,63272:46,63273:36,63275:35};var e=function(j){var i=j.keyCode;i=h.browser.safari?(a[i]||i):i;return(i>=33&&i<=40)||i==c.RETURN||i==c.TAB||i==c.ESC};var d=function(j){var i=j.keyCode;var l=j.charCode;return i==9||i==13||(i==40&&(!h.browser.opera||!j.shiftKey))||i==27||i==16||i==17||(i>=18&&i<=20)||(h.browser.opera&&!j.shiftKey&&(i==8||(i>=33&&i<=35)||(i>=36&&i<=39)||(i>=44&&i<=45)))};var b=function(j){var i=j.keyCode||j.charCode;return h.browser.safari?(a[i]||i):i};var g=function(i){return i.charCode||i.keyCode||i.which};h.fn.keyfilter=function(i){return this.keypress(function(m){if(m.ctrlKey||m.altKey){return}var j=b(m);if(h.browser.mozilla&&(e(m)||j==c.BACKSPACE||(j==c.DELETE&&m.charCode==0))){return}var o=g(m),n=String.fromCharCode(o),l=true;if(!h.browser.mozilla&&(d(m)||!n)){return}if(h.isFunction(i)){l=i.call(this,n)}else{l=i.test(n)}if(!l){m.preventDefault()}})};h.extend(h.fn.keyfilter,{defaults:{masks:f},version:1.7});   
    if($().jquery > 1.7) { $(document).on("input[class*=mask],textarea[class*=mask]", 'keydown', function(){ for (var key in $.fn.keyfilter.defaults.masks) { $(this).filter('.mask-' + key).keyfilter($.fn.keyfilter.defaults.masks[key]); } }); } else { $("input[class*=mask],textarea[class*=mask]").live('keydown', function(){ for (var key in $.fn.keyfilter.defaults.masks) { $(this).filter('.mask-' + key).keyfilter($.fn.keyfilter.defaults.masks[key]);} });}})(jQuery);