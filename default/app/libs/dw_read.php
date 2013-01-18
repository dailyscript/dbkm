<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Librería para el manejo de lectura de archivos planos
 * @package     Libs
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class DwRead {

    /**
     * Path donde se encuentra el archivo
     *
     * @var string
     */
    protected static $file_path = '';

    /**
     * Especifica el PATH donde se encuentra el archivo
     *
     * @param string $path
     */
    public static function set_path($path) {
        self::$file_path = $path;
    }

    /**
     * Obtener el path actual
     *
     * @return $path
     */
    public static function get_path() {
        return self::$file_path;
    }
    
    /**
     * Método que para leer un archivo plano
     * @return array
     */
    public static function file($name, $ext='txt') {
        self::$file_path = APP_PATH . 'temp/logs/' . $name . '.'.  $ext;
        if(is_file(self::$file_path)) {
            return file(self::$file_path);
        } else {
            return false;
        }        
    }    
    
}
?>
