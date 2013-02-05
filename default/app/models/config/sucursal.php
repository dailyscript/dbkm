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

Load::models('params/ciudad');

class Sucursal extends ActiveRecord {
    
    /**
     * Constante para definir el id de la oficina principal
     */
    const OFICINA_PRINCIPAL = 1;

    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('empresa');
        $this->belongs_to('ciudad');
        $this->has_many('usuario');

        $this->validates_presence_of('sucursal', 'message: Ingresa el nombre de la sucursal');        
        $this->validates_presence_of('direccion', 'message: Ingresa la dirección de la sucursal.');
        $this->validates_presence_of('ciudad_id', 'message: Indica la ciudad de ubicación de la sucursal.');
                
    }  
    
    /**
     * Método para ver la información de una sucursal
     * @param int|string $id
     * @return Sucursal
     */
    public function getInformacionSucursal($id, $isSlug=false) {
        $id = ($isSlug) ? Filter::get($id, 'string') : Filter::get($id, 'numeric');
        $columnas = 'sucursal.*, empresa.razon_social, empresa.siglas, empresa.representante_legal, ciudad.ciudad';
        $join = 'INNER JOIN empresa ON empresa.id = sucursal.empresa_id INNER JOIN ciudad ON ciudad.id = sucursal.ciudad_id';
        $condicion = ($isSlug) ? "sucursal.slug = '$id'" : "sucursal.id = '$id'";
        return $this->find_first("columns: $columnas", "join: $join", "conditions: $condicion");
    } 
    
    /**
     * Método que devuelve las sucursales
     * @param string $order
     * @param int $page 
     * @return ActiveRecord
     */
    public function getListadoSucursal($order='order.sucursal.asc', $page='', $empresa=null) {
        $empresa = Filter::get($empresa, 'int');
        
        $columns = 'sucursal.*, empresa.siglas, ciudad.ciudad';
        $join = 'INNER JOIN empresa ON empresa.id = sucursal.empresa_id INNER JOIN ciudad ON ciudad.id = sucursal.ciudad_id';        
        $conditions = (empty($empresa)) ? 'sucursal.id > 0' : "empresa.id = '$empresa'";
        
        $order = $this->get_order($order, 'sucursal', array('sucursal'=>array('ASC'=>'sucursal.sucursal ASC, ciudad.ciudad ASC, empresa.siglas ASC',
                                                                              'DESC'=>'sucursal.sucursal DESC, ciudad.ciudad ASC, empresa.siglas ASC'),
                                                            'ciudad'=>array('ASC'=>'ciudad.ciudad ASC, sucursal.direccion ASC, sucursal.sucursal ASC, empresa.siglas ASC',
                                                                              'DESC'=>'ciudad.ciudad DESC, sucursal.direccion ASC, sucursal.sucursal ASC, empresa.siglas ASC'),
                                                            'telefono',
                                                            'fax',
                                                            'direccion'));
        if($page) {                
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");            
        }
    }
    
    /**
     * Método para setear
     * @param string $method Método a ejecutar (create, update, save)
     * @param array $data Array con la data => Input::post('model')
     * @param array $otherData Array con datos adicionales
     * @return Obj
     */
    public static function setSucursal($method, $data, $optData=null) {
        //Se aplica la autocarga
        $obj = new Sucursal($data);
        //Se verifica si contiene una data adicional para autocargar
        if ($optData) {
            $obj->dump_result_self($optData);
        }   
        if($method!='delete') {
            $obj->ciudad_id = Ciudad::setCiudad($obj->ciudad)->id;        
        }
        $rs = $obj->$method();
        
        return ($rs) ? $obj : FALSE;
    }

    /**
     * Método que se ejecuta antes de guardar y/o modificar     
     */
    public function before_save() {        
        $this->sucursal = Filter::get($this->sucursal, 'string');        
        $this->slug = DwUtils::getSlug($this->sucursal); 
        $this->direccion = Filter::get($this->direccion, 'string');
        $this->telefono = Filter::get($this->telefono, 'numeric');
        $this->celular = Filter::get($this->celular, 'numeric');
        $this->fax = Filter::get($this->fax, 'numeric');        
        
        $conditions = "sucursal = '$this->sucursal' AND ciudad_id = $this->ciudad_id AND empresa_id = $this->empresa_id";
        $conditions.= (isset($this->id)) ? " AND id != $this->id" : '';
        if($this->count("conditions: $conditions")) {
            DwMessage::error('Lo sentimos, pero ya existe una sucursal registrada con el mismo nombre y ciudad.');
            return 'cancel';
        }
        
    }
    
    /**
     * Callback que se ejecuta antes de eliminar
     */
    public function before_delete() {
        if($this->id == 1) { //Para no eliminar la información de sucursal
            DwMessage::warning('Lo sentimos, pero esta sucursal no se puede eliminar.');
            return 'cancel';
        }
    }
    
    
}