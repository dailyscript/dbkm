<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que gestiona lo relacionado con los tipos de identificacion
 *
 * @category    Parámetros
 * @package     Models
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class TipoNuip extends ActiveRecord {

    /**
     * Método contructor
     */
    public function initialize() {
        $this->has_many('empresa');
        $this->has_many('persona');
    }

    /**
     * Método para listar los tipos de identificación
     * @return array
     */
    public function getListadoTipoNuip() {
        return $this->find('order: tipo_nuip ASC');
    }

}
?>