<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que gestiona todo lo relacionado con los recursos del sistema
 *
* @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Recurso extends ActiveRecord {

    const ACTIVO = 1;
    
    const INACTIVO = 2;
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {        
        $this->has_many('recurso_usuario');        
    }
    
    
}
?>