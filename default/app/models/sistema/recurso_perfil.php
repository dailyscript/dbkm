<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que gestiona todo lo relacionado con los
 * recursos de los usuarios con su respectivo grupo
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)  
 */

class RecursoPerfil extends ActiveRecord {
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('recurso');
        $this->belongs_to('usuario');
    }

    /**
     * Método que retorna los recursos asignados a un perfil de usuario
     * @param int $perfil Identificador el perfil del usuario
     * @return array object ActieRecord
     */
    public function getRecursoPerfil($perfil) {
        $perfil = Filter::get($perfil,'numeric');
        $columnas = 'recurso_perfil.*, recurso.modulo, recurso.controlador, recurso.accion, recurso.descripcion, recurso.estado';
        $join = 'INNER JOIN recurso ON recurso.id = recurso_perfil.recurso_id';        
        $condicion = "recurso_perfil.perfil_id = '$perfil'";
        $order = 'recurso.modulo ASC, recurso.controlador ASC,  recurso.registrado_at ASC';
        if($perfil) {
            return $this->find("columns: $columnas", "join: $join", "conditions: $condicion", "order: $order");
        }
        return false;                                
    }
    
}
?>