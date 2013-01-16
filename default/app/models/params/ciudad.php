<?php

/**
 * Dailyscript - Web | App | Media
 *
 *
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Ciudad extends ActiveRecord {

    /**
     * Método que se ejecuta antes de inicializar cualquier acción
     */
    public function initialize() {        
        $this->has_many('sucursal');        
        $this->validates_presence_of('ciudad', 'message: Ingresa el nombre de la ciudad');        
    }

    /**
     * Método para setear
     * 
     * @param array $data
     * @return
     */
    public static function setCiudad($name) {
        //Se aplica la autocarga
        $obj = new Ciudad();        
        $obj->ciudad = ucfirst(Filter::get($name, 'string'));
        //Verifico si existe otra ciudad bajo el mismo nombre
        $old = new Ciudad();
        if($old->find_first("ciudad LIKE '%$obj->ciudad%'")) {
            return $old;
        }        
        return $obj->create() ? $obj : FALSE;        
    }
    
    /**
     * Método que devuelve las ciudades paginadas o para un select
     * @param int $pag Número de página a mostrar.
     * @return ActiveRecord
     */
    public function getListadoCiudad($order='order.ciudad.asc', $page=0) {        
        $order = $this->get_order($order, 'ciudad');
        if($page) {
            return $this->paginated("order: $order", "page: $page");
        } else {
            return $this->find("order: $order");
        }         
    }
    
    /**
     * Método para obtener las ciudades como json
     * @return type
     */
    public function getCiudadesToJson() {
        $rs =  $this->find("columns: ciudad", 'group: ciudad', 'order: ciudad ASC');
        $ciudades = array();
        foreach($rs as $ciudad) {            
            $ciudades[] = $ciudad->ciudad; 
        }
        return json_encode($ciudades);
    }
    
}