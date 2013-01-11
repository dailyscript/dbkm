<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Filtra etiquetas html, tildes, espacios entre otras.
 *
 * @category    Extensions
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @package     Filters
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.co) 
 */

class StringFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para los string
     *
     * @param string $s
     * @param array $options
     * @return string
     */    
    public static function execute ($s, $options) {
        $string = filter_var($s, FILTER_SANITIZE_STRING);
        $string = strip_tags((string) $string);
        $string = stripslashes((string) $string);
        $string = trim($string);
        $find = array('á','é','í','ó','ú','ü','Á','É','Í','Ó','Ú','Ü');
        $replace = array('a','e','i','o','u','u','A','E','I','O','U','U');
        $string = str_replace($find, $replace, $string);
        return $string;
    }    

}
?>
