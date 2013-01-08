<?php
/**
 * Dailyscript - Web | App | media
 *
 * Extension para el manejo de javascript
 *
 * @category    Helpers
 * @author      Iván D. Meléndez
 * @package     Helpers
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class DwJs {
    
    /**
     * Método para modificar la url despues de enrutar
     * @param type $url
     * @return string 
     */
    public static function setUrl($url) {
        $url = trim($url, '/').'/';
        $js = self::open();
        $js.= "DwUpdateUrl('$url');";        
        $js.= self::close();        
        return $js;
    } 
    
    /**
     * Abre una etiqueta para javascript
     * @return string
     */
    public static function open() {
        return '<script type="text/javascript">'.PHP_EOL;
    }

    /**
     * Cierra una etiqueta de código javascript
     * @return string
     */
    public static function close() {
        return '</script>'.PHP_EOL;
    }
    
    
}
