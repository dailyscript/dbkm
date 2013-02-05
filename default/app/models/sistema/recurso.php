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
    
    //Se desabilita el logger para no llenar el archivo de "basura"
    public $logger = FALSE;

    /**
     * Constante para definir un recurso como activo
     */
    const ACTIVO = 1;
    
    /**
     * Constante para definir un recurso como inactivo
     */
    const INACTIVO = 2;
    
    /**
     * Constante para identificar el comodín *
     */
    const COMODIN = 1;
    
    /**
     * Constante para definir el recurso principal
     */
    const DASHBOARD = 2;
    
    /**
     * Constante para definir el recurso "Mi Cuenta"
     */
    const MI_CUENTA = 2;
       
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {        
        $this->has_many('recurso_perfil');        
        $this->has_many('menu');
        
        $this->validates_presence_of('controlador', 'message: Ingresa el nombre del controlador.');
        $this->validates_presence_of('descripcion', 'message: Ingresa la descripción del recurso.');        
    }
    
    /**
     * Método para obtener el listado de los recursos del sistema
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoRecurso($estado='todos', $order='', $page=0) {                           
        $conditions = 'recurso.id IS NOT NULL';                
        if($estado!='todos') {
            $conditions.= ($estado==self::ACTIVO) ? " AND activo=".self::ACTIVO : " AND activo=".self::INACTIVO;
        }        
        $order = $this->get_order($order, 'modulo', array(            
            'controlador' => array(
                'ASC' => 'controlador ASC, modulo ASC, accion ASC',
                'DESC' => 'controlador DESC, modulo DESC, accion DESC'
            ),
            'accion' => array(
                'ASC' => 'accion ASC, modulo ASC, controlador ASC',
                'DESC' => 'accion DESC, modulo DESC, controlador DESC'
            )
        ));
        if($page) {            
            return $this->paginated("conditions: $conditions", "order: $order", "page: $page");
        }
        return $this->find("conditions: $conditions", "order: $order");
    }
    
    /**
     * Método para crear/modificar un objeto de base de datos
     * 
     * @param string $medthod: create, update
     * @param array $data: Data para autocargar el modelo
     * @param array $optData: Data adicional para autocargar
     * 
     * return object ActiveRecord
     */
    public static function setRecurso($method, $data, $optData=null) {        
        $obj = new Recurso($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }
        //Verifico que no exista otro recurso, y si se encuentra inactivo lo active
        $conditions = empty($obj->id) ? "modulo='$obj->modulo' AND controlador='$obj->controlador' AND accion='$obj->accion'" : "modulo='$obj->modulo' AND controlador='$obj->controlador' AND accion='$obj->accion' AND id != '$obj->id'";
        $old = new Recurso();
        if($old->find_first($conditions)) {            
            if($method=='create' && $old->activo != Recurso::ACTIVO) {
                $obj->id = $old->id;
                $obj->activo = Recurso::ACTIVO;
                $method = 'update';
            } else {
                DwMessage::info('Ya existe un recurso registrado bajo esos parámetros.');
                return FALSE;
            }
        }
        return ($obj->$method()) ? $obj : FALSE;
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
        $this->modulo = Filter::get($this->modulo, 'string');
        $this->controlador = Filter::get($this->controlador, 'string');
        $this->accion = Filter::get($this->accion, 'string');
        if(empty($this->accion)) {
            $this->accion = '*';
        }
        $this->recurso = trim($this->modulo.'/'.$this->controlador.'/'.$this->accion.'/', '/');
        $this->descripcion = Filter::get($this->descripcion, 'string');
        if(!empty($this->id) && $this->id <= 16) {
            DwMessage::warning('Lo sentimos, pero este recurso no se puede editar.');
            return 'cancel';            
        }
    }
    
    /**
     * Callback que se ejecuta antes de eliminar
     */
    public function before_delete() {
        if($this->id <= 16) {
            return 'cancel';
        }
        if($this->modulo=='sistema' OR ($this->recurso == '*' && empty($this->controlador)) OR ($this->recurso=='principal/*') ) {
            return 'cancel';
        }
    }
    
    
}
?>