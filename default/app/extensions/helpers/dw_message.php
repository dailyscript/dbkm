<?php
/**
 * Dailyscript - Web | App | media
 *
 * Extension para el manejo de mensajes sin hacer uso del "echo" en los controladores o modelos
 *
 * @category    Flash
 * @author      Iván D. Meléndez
 * @package     Helpers
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 *  
 * Se utiliza en el método content de la clase view.php
 * if(DwMessage::has()) {
 *      DwMessage::output();
 * }
 * 
 */

class DwMessage {
    
    /**
     * Variable que contiene los diferentes mensajes repetitivos para mostrarlos en el método fixed
     * @var array
     */
    protected static $_msg = array( 'error_form'        =>array('error', 'Se ha producido un error al registrar la información. <br />Verifica los datos e intenta nuevamente.'),
                                    'error_key_form'    =>array('error', 'Se ha producido un error en la validación del formulario. Verifica los datos e intenta nuevamente.'),
                                    'error_key_url'     =>array('error', 'Acceso denegado. La url de la barra de direcciones es incorrecta.'),
                                    'id_no_found'       =>array('info', 'No se ha podido establecer la información del registro. <br />Verfica el proceso e inenta nuevamente.'));

    /**
     * Mensajes almacenados en un request
     */
    private static $_contentMsj = array();
        
    /**
     * Setea un mensaje dw-flash
     *
     * @param string $name Tipo de mensaje y para CSS class='$name'.
     * @param string $msg Mensaje a mostrar
     * @param boolean $audit Indica si el mensaje se almacena como auditoría
     */
    public static function set($name, $msg, $audit=FALSE) {        
        //Verifico si hay mensajes almacenados en sesión por otro request.
        if(self::has('dw-messages')) {            
            self::$_contentMsj = Session::get('dw-messages');                
        }        
        //Guardo el mensaje en el array
        if (isset($_SERVER['SERVER_SOFTWARE'])) {                    
            self::$_contentMsj[] = '<div class="alert alert-block alert-'.$name.'"><button type="button" class="close" data-dismiss="alert">×</button>'.$msg.'</div>'.PHP_EOL;
        } else {
            self::$_contentMsj[] = $name.': '.Filter::get($msg, 'striptags').PHP_EOL;            
        }        
        //Almaceno los mensajes guardados en una variable de sesión, para mostrar los mensajes provenientes de otro request.
        Session::set('dw-messages', self::$_contentMsj);
        //Verifico si el mensaje se almacena como looger
        if($audit) {
            ($name=='success') ? DwAudit::debug($msg) : DwAudit::$name($msg);
        }            
    }
    
    /**
     * Verifica si tiene mensajes para mostrar.
     *
     * @return bool
     */
    public static function has() {
        return Session::has('dw-messages') ?  TRUE : FALSE;
    }
    
    /**
     * Método para limpiar los mensajes almacenados
     */
    public static function clean() {
        //Reinicio la variable de los mensajes
        self::$_contentMsj = array();
        //Elimino los almacenados en sesión
        Session::delete('dw-messages');
    }

    /**
     * Muestra los mensajes
     */
    public static function output() {
        //Asigno los mensajes almacenados en sesión en una variable temporal
        $tmp = Session::get('dw-messages');
        //Recorro los mensajes
        foreach($tmp as $msg) {
            // Imprimo los mensajes
            echo $msg;
        }
        self::clean();
    }
    
    /**
     * Retorna los mensajes cargados como string
     */
    public static function toString() {
        //Asigno los mensajes almacenados en sesión en una variable temporal
        $tmp = self::has() ? Session::get('dw-messages') : array();
        $msg = array();
        //Recorro los mensajes
        foreach($tmp as $item) {
            //Limpio los mensajes
            $msg[] = str_replace('×', '', Filter::get($item, 'striptags'));
        }
        $flash = Filter::get(ob_get_clean(), 'striptags', 'trim'); //Almaceno los mensajes que hay en el buffer por el Flash::
        $msg = Filter::get(join('<br />', $msg), 'trim');
        self::clean(); //Limpio los mensajes de la sesión               
        return ($flash) ? $flash.'<br />'.$msg : $msg;
    }

    /**
     * Carga un mensaje de error
     *
     * @param string $msg
     * @param boolean $autid Indica si se registra el mensaje como una auditoría
     */
    public static function error($msg, $audit=FALSE) {
        self::set('error',$msg, $audit);          
    }

    /**
     * Carga un mensaje de advertencia en pantalla
     *
     * @param string $msg
     * @param boolean $autid Indica si se registra el mensaje como una auditoría
     */
    public static function warning($msg, $audit=FALSE) {
        self::set('warning',$msg, $audit);
    }

    /**
     * Carga informacion en pantalla
     *
     * @param string $msg
     * @param boolean $autid Indica si se registra el mensaje como una auditoría
     */
    public static function info($msg, $audit=FALSE) {
        self::set('info',$msg, $audit);
    }
    
    /**
     * Carga información de suceso correcto en pantalla
     *
     * @param string $msg
     * @param boolean $autid Indica si se registra el mensaje como una auditoría
     */
    public static function valid($msg, $audit=FALSE) {
        self::set('success',$msg, $audit);
    }

    /** 
     * Carga mensajes por defecto almacenados en un array
     * 
     * @param type $num Nombre del mensaje a mostrar
     * @param type $audit 
     */
    public static function get($msg, $audit=FALSE) {
        $message = self::$_msg[$msg];
        self::set($message[0], $message[1], $audit);        
    }    
    
}
