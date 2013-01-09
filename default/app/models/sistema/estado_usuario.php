<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que gestiona todos los estados de los usuarios
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)  
 */

class EstadoUsuario extends ActiveRecord {

    const COD_BLOQUEADO = 1;
    const COD_ACTIVO = 2;
    const ACTIVO = 'ACTIVO';
    const BLOQUEADO = 'BLOQUEADO';

    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('usuario');        
    }

    /**
     * Método para obtener el estado de un usuarios
     * @param int $usuario
     * @return string
     */
    public function getEstadoUsuario($usuario) {
        $usuario = Filter::get($usuario, 'numeric');
        $condicion = "usuario_id = '$usuario'";
        $sql = $this->find_first('columns: estado_usuario', "conditions: $condicion", 'order: id DESC');
        if($sql) {
            return ($sql->estado_usuario == self::COD_ACTIVO) ? self::ACTIVO : self::BLOQUEADO;
        }
        return false;
    }

    /**
     * Método para listar todos los estados del usuario
     * @param int $usuario 
     * @param int $pag Número de la página. Si es mayor que 0 se utiliza el paginador
     * @return EstadoUsuario
     */
    public function getListadoEstadoUsuario($usuario, $pag=0) {
        $usuario = Filter::get($usuario,'numeric');
        $sql = "SELECT id, IF(estado_usuario=1,'".self::ACTIVO."','".self::BLOQUEADO."') AS estado_usuario, descripcion, fecha_estado_at FROM estado_usuario WHERE usuario_id = '$usuario' ORDER BY id DESC";
        return ($pag) ? $this->paginated_by_sql($sql, "page: $pag") : $this->find_all_by_sql($sql);        
        return false;
    }  
    
    /**
     * Callback que se ejecuta antes de crear un registro
     */
    public function before_create() {
        $this->descripcion = Filter::get($this->descripcion, 'string');
    }
      
}
