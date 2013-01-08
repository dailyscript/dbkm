<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que gestiona todo lo relacionado con los recursos del sistema
 *
 * @category    Sistema
 * @package     Models
 * @subpackage  Usuarios
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Recurso extends ActiveRecord {

    const ACTIVO = 1;
    
    const INACTIVO = 2;
    
    /**
     * Método principal
     */
    public function initialize() {        
        $this->has_many('recurso_usuario');        
    }
    
    
}
?>