<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que se encarga de todo lo relacionado con la información de la empresa
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Empresa extends ActiveRecord {

    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {        
        $this->belongs_to('tipo_nuip');
        $this->has_many('sucursal');                                
        $this->validates_presence_of('nombre', 'message: Ingresa el nombre de la empresa');
        $this->validates_presence_of('representante_legal', 'message: Ingresa el nombre del propietario o representante legal.');
        $this->validates_presence_of('nuip', 'message: Ingresa el NUIP o NIT de la empresa.');
        $this->validates_presence_of('tipo_nuip_id', 'message: Selecciona el tipo de identificación.');        
        $this->validates_email_in('email', 'message: El correo electrónico es incorrecto.');
    }

    /**
     * Método para obtener la información de la empresa
     * @return obj
     */
    public function getInformacionEmpresa() {
        $columnas = 'empresa.*, tipo_nuip.tipo_nuip';
        $join = 'INNER JOIN tipo_nuip ON tipo_nuip.id = empresa.tipo_nuip_id';
        return $this->find_first("columns: $columnas", "join: $join", 'conditions: empresa.id IS NOT NULL', 'order: empresa.registrado_at DESC');
    }    
    
}
?>

