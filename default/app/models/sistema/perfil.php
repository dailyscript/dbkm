<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Clase que gestiona los perfiles de usuarios
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Perfil extends ActiveRecord {
    
    /**
     * Constante para definir el perfil de Super Usuario
     */
    const SUPER_USUARIO = 1;
    
    /**
     * Constante para definir un perfil como activo
     */
    const ACTIVO = 1;
    
    /**
     * Constante para definir un perfil como inactivo
     */
    const INACTIVO = 2;
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->has_many('usuario');
        $this->has_many('recurso_perfil');
    }
    
    /**
     * Método para obtener el listado de los perfiles del sistema
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoPerfil($estado='todos', $order='', $page=0) {                   
        $columns = 'perfil.*, COUNT(usuario.id) AS usuarios';        
        $join = 'LEFT JOIN usuario ON perfil.id = usuario.perfil_id ';
        $conditions = 'perfil.id IS NOT NULL';        
        if($estado=='acl') {
            $conditions.= " AND perfil.estado = ".self::ACTIVO;
        } else{
            $conditions.= " AND perfil.id > 1";            
            if($estado!='todos') {
                $conditions.= ($estado==self::ACTIVO) ? " AND estado=".self::ACTIVO : " AND estado=".self::INACTIVO;                
            }
        }        
        $order = $this->get_order($order, 'perfil'); 
        $group = 'perfil.id';
        if($page) {            
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order", "page: $page");
        }
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
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
    public static function setPerfil($method, $data, $optData=null) {        
        $obj = new Perfil($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }                               
        //Verifico que no exista otro perfil, y si se encuentra inactivo lo active
        $conditions = empty($obj->id) ? "perfil = '$obj->perfil'" : "perfil = '$obj->perfil' AND id != '$obj->id'";
        $old = new Perfil();
        if($old->find_first($conditions)) {            
            if($method=='create' && $old->estado != Perfil::ACTIVO) {
                $obj->id = $old->id;
                $obj->estado = Perfil::ACTIVO;
                $method = 'update';
            } else {
                DwMessage::info('Ya existe un perfil registrado bajo ese nombre.');
                return FALSE;
            }
        }
        return ($obj->$method()) ? $obj : FALSE;
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
        $this->perfil = Filter::get($this->perfil, 'string');
        $this->plantilla = DwUtils::getSlug(Filter::get($this->plantilla, 'string'), '_');                
        if(!empty($this->id)) {
            if($this->id == Perfil::SUPER_USUARIO) {
                DwMessage::warning('Lo sentimos, pero este perfil no se puede editar.');
                return 'cancel';
            }
        }
    }
    
    /**
     * Método para obtener los ecursos de un perfil
     * @param type $perfil
     * @return type
     */
    public function getRecursos($perfil=null){        
        $columnas = "recurso.*";
        $join = "INNER JOIN recurso_perfil ON perfil.id = recurso_perfil.perfil_id ";
        $join.= "INNER JOIN recurso ON recurso.id = recurso_perfil.recurso_id ";
        $conditions = ($perfil)  ? "perfil.id = '$perfil'" : "perfil.id = '$this->id'";
        return $this->find("columns: $columnas" , "join: $join", "conditions: $conditions");
    }
    
}
?>
