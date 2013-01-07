<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Clase que hereda para el manejo de redirecciones internas
 *
 * @category    
 * @package     Libs 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2012 Dailyscript Team (http://www.dailyscript.com.co)
 * @revision     1.0
 */

class DwRedirect extends Redirect {
     
    /**
     * Redirecciona la ejecución a otro controlador en un
     * tiempo de ejecución determinado
     *
     * @param string $route ruta a la que será redirigida la petición.
     * @param integer $seconds segundos que se esperarán antes de redirigir
     * @param integer $statusCode código http de la respuesta, por defecto 302
     */
    public static function to($route = null, $seconds = null, $statusCode = 302) {
        $route OR $route = Router::get('controller_path') . '/';        
        $route = PUBLIC_PATH . ltrim($route, '/');        
        if ($seconds) {
            header("Refresh: $seconds; url=$route");
        } else {
            header('HTTP/1.1 ' . $statusCode);
            header("Location: $route");
            $_SESSION['KUMBIA.CONTENT'] = ob_get_clean();
            View::select(null, null);
        }
    }
}

