<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que se utiliza para autenticar los usuarios
 *
 * @category    Sistema
 * @package     Libs
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

Load::lib('auth2');

//Cambiar la key por una propia
//Se puede utilizar las de wordpress: https://api.wordpress.org/secret-key/1.1/salt/
define('SESSION_KEY', 'sv}-c*2h_SoM]jM-Putiat`|h[Sih9GjLh=Qz.TU$<<7_RwPmLNcxz(pq4c2ueJ{');

class DwAuth {
    
    /**
     * Mensaje de Error
     *
     * @var String
     */
    protected static $_error = null;
    
    /**
    * Método para iniciar Sesion
    *
    * @param $username mixed Array con el nombre del campo en la bd del usuario y el valor
    * @param $password mixed Array con el nombre del campo en la bd de la contraseña y el valor
    * @return true/false
    */
    public static function login($fieldUser, $fieldPass) {        
        //Verifico si tiene una sesión válida
        if(self::isLogged()) {
            return true;
        } else {
            //Verifico si envía el array array('usuario'=>'admin') o string 'usuario'
            $keyUser = (is_array($fieldUser)) ? @array_shift(array_keys($fieldUser)) : NULL;
            $keyPass = (is_array($fieldPass)) ? @array_shift(array_keys($fieldPass)) : NULL;
            $valUser = ($keyUser) ? $fieldUser[$keyUser] : NULL;
            $valPass = ($keyPass) ? $fieldPass[$keyPass] : NULL;
            $auth = Auth2::factory('model');            
            ($keyUser) ? $auth->setLogin($keyUser) : $auth->setLogin($fieldUser);
            ($keyPass) ? $auth->setPass($keyPass) : $auth->setPass($fieldPass);
            $auth->setCheckSession(true);
            $auth->setModel('sistema/usuario');                                        
            $auth->setFields(array('id', 'persona_id', 'login', 'tema', 'ajax', 'datagrid', 'perfil_id'));            
            if($auth->identify($valUser, $valPass) && $auth->isValid()) {  
                Session::set(SESSION_KEY, true);
                return true;
            } else {
                self::setError('El usuario y/o la contraseña son incorrectos.');
                Session::set(SESSION_KEY, false);                
                return false;
            }
        }
    }
    
    /**
    * Método para cerrar sesión
    *
    * @param void
    * @return void
    */
    public static function logout() {
        //Verifico si tiene sesión
        if(!self::isLogged()) { 
            self::setError("No has iniciado sesión o ha caducado. <br /> Por favor identifícate nuevamente.");
            return false;
        } else {                  
            $auth = Auth2::factory('model');
            $auth->logout();            
            Session::set(SESSION_KEY, false);
            unset($_SESSION['KUMBIA_SESSION'][APP_PATH]);
            return true;
        }
    }
        
    /**
    * Método para verificar si tiene una sesión válida
    *
    * @param void
    * @return ture/false
    */
    public static function isLogged() {         
        $auth = Auth2::factory('model');
        $bValid = $auth->isValid();        
        $bValid = $bValid && Session::get(SESSION_KEY);        
        return $bValid;         
    }
    
    /**
    * @return string
    */
    public static function getError() {
        return self::$_error;
    }
    
    /**
    * @param string $_error
    */
    public static function setError($error) {
        self::$_error = $error;
    }
}

?>
