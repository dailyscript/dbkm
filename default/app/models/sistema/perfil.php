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
    
    const SUPER_USUARIO = 1;
    
    const ACTIVO = 1;
    
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
        $columns = 'perfil.*, IF(perfil.activo=1, "ACTIVO", "BLOQUEADO") AS estado';
        $conditions = 'perfil.id IS NOT NULL';
        if($estado!='todos') {
            $conditions.= ($estado!=self::INACTIVO) ? " AND estado=".self::ACTIVO : " AND estado=".self::INACTIVO;
        }        
        $order = $this->get_order($order, 'perfil');        
        if($page) {            
            return $this->paginated_by_sql("SELECT $columns FROM $this->source WHERE $conditions ORDER BY $order", "page: $page");
        }
        return $this->find_all_by_sql("SELECT $columns FROM $this->source WHERE $conditions ORDER BY $order");
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
        $obj = new Perfil($data);
        if($optData) {
            $obj->dump_result_self($optData);
        }
        return ($obj->$method()) ? $obj : FALSE;
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
