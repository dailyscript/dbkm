<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Modelo encargado de registrar las personas en el sistema
 *
 * @category
 * @package     Models
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2012 Dailyscript Team (http://www.dailyscript.com.co)
 * @revision    1.0
 */

Load::models('sistema/usuario');

class Persona extends ActiveRecord {

    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function initialize() {
        $this->has_one('usuario');
        $this->belongs_to('tipo_nuip');
    }

    /**
     * Método para setear un Objeto
     * @param string    $method     Método a ejecutar (create, update)
     * @param array     $data       Array para autocargar el objeto
     * @param array     $optData    Array con con datos adicionales para autocargar
     */
    public static function setPersona($method, $data=array(), $optData=array()) {
        $obj = new Persona($data);
        if(!empty($optData)) {
            $obj->dump_result_self($optData);
        }
        //Creo otro objeto para comparar si existe
        $old = new Persona($data);
        $check = $old->_getPersonaRegistrada('find_first');
        if($check) { //Si existe
            if(empty($obj->id)) {
                $obj->id = $old->id; //Asigno el id del encontrado al nuevo
            } else { //Si se actualiza y existe otro con la misma información
                if($obj->id != $old->id) {
                    DwMessage::info('Lo sentimos, pero ya existe una persona registrada con el mismo número y tipo de identificación');
                    return FALSE;
                }
            }
            if($method=='create') { //Si se crea la persona, pero ya está registrada la actualizo
                $method = 'update';
            }
        }
        $rs = $obj->$method();
        return ($rs) ? $obj : FALSE;
    }

    /**
     * Método para verificar si una persona ya se encuentra registrada
     * @return obj
     */
    protected function _getPersonaRegistrada($method='count') {
        $conditions = "nuip = $this->nuip AND tipo_nuip_id = $this->tipo_nuip_id";
        $conditions.= (isset($this->id)) ? " AND id != $this->id" : '';
        if($method != 'count' && $method !='find_first') {
            $method = 'count';
        }
        return $this->$method("conditions: $conditions");
    }

    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
        $this->nombre = Filter::get($this->nombre, 'string');
        $this->apellido = Filter::get($this->apellido, 'string');
        $this->nuip = Filter::get($this->nuip, 'numeric');
        $this->telefono = Filter::get($this->telefono, 'numeric');
    }

}

