<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que gestiona los menús de los usuarios según los recursos asignados
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Menu extends ActiveRecord {
    
    /**
     * Constante para definir un menú como activo
     */
    const ACTIVO = 1;
    
    /**
     * Constante para definir un menú como inactivo
     */
    const INACTIVO = 2;
    
    /**
     * Constante para definir un menú visible en el backend
     */
    const BACKEND = 1;
    
    /**
     * Constante para definir un menú visible en el frontend
     */
    const FRONTEND = 2;
    
    /**
     * Variable que contiene los menús 
     */
    protected static $_main = null;
    
    /**
     * Variable que contien los items del menú
     */        
    protected static $_items = null;
        
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {        
        $this->has_many('menu');
        $this->belongs_to('recurso');
    }
    
    /**
     * Método para cargar en variable el menú
     * @param type $usuario
     */
    public static function load($perfil) {
        $obj = new Menu();
        $columns = 'menu.*';        
        if($perfil==Perfil::SUPER_USUARIO) {          
            $join = null;
            $conditions = "menu.activo = ".self::ACTIVO;
        } else {
            $join = 'join: ';
            $join.= 'INNER JOIN recurso ON recurso.id = menu.recurso_id ';
            $join.= 'INNER JOIN recurso_perfil ON recurso.id = recurso_perfil.perfil_id ';            
            $conditions = "recurso_perfil.perfil_id = $perfil AND menu.activo = ".self::ACTIVO;
        }        
        $order = 'menu.posicion ASC';        
        if(self::$_main==NULL) {            
            $conditions2 = $conditions." AND menu.menu_id IS NULL";
            self::$_main = $obj->find("columns: $columns", "$join", "conditions: $conditions2", "order: $order");            
        }
        if(self::$_items==NULL && self::$_main) {
            foreach(self::$_main as $menu) {
                $conditions2 = $conditions." AND menu.menu_id = $menu->id";
                self::$_items[$menu->menu] = $obj->find("columns: $columns", "join: $join", "conditions: $conditions2", "order: $order");
            }
        }
    }
        
    /**
     * Método para obtener el menú principal
     */
    public static function getMain($view='desktop') {        
        $route = trim(Router::get('route'), '/');        
        $main = '';
        if($view=='phone') {
            foreach(self::$_main as $menu) {
                $text = $menu->menu.'<b class="caret"></b>';                 
                $main.= '<li class="dropdown">';
                $main.= DwHtml::link('#', $text, array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'), NULL, FALSE);
                if(array_key_exists($menu->menu, self::$_items)) {
                    $main.= '<ul class="dropdown-menu">';
                    foreach(self::$_items[$menu->menu] as $item) {                        
                        $active = ($item->url==$route) ? 'active' : null;                        
                        $main.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, NULL, $item->icon, FALSE).'</li>';
                    }
                    $main.= '</ul>';                    
                }
                $main.= '</li>'.PHP_EOL;                
            }
        } else {   
            $main.= '<ul class="nav">'.PHP_EOL;
            foreach(self::$_main as $menu) {         
                $active = ($menu->url==$route) ? 'active' : null;
                $main.= '<li class="'.$active.'">'.DwHtml::link($menu->url, $menu->menu, array('class'=>'main-menu-link', 'data-filter'=>"sub-menu-".strtolower($menu->menu)), $menu->icono).'</li>'.PHP_EOL;
            }
            $main.= '</ul>'.PHP_EOL;
            return $main;
        }
        return $main;
    }
          
    /**
     * Método para obtener los items de cada menú en modo 'desktop'
     * @return string
     */
    public static function getItems() {        
        $route = trim(Router::get('route'), '/');
        $str = '';        
        foreach(self::$_items as $menu => $items) {
            $str.= '<div id="sub-menu-'.strtolower($menu).'" class="subnav hidden">'.PHP_EOL;
            $str.= '<ul class="nav nav-pills">'.PHP_EOL;
            if(array_key_exists($menu, self::$_items)) {
                foreach(self::$_items[$menu] as $item) {                    
                    $active = ($item->url==$route or $item->url=='principal') ? 'active' : null;
                    $str.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, null, $item->icono).'</li>'.PHP_EOL;
                }
            }
            $str.= '</ul>'.PHP_EOL;
            $str.= '</div>'.PHP_EOL;
        }
        return $str;         
    }
    
    /**
     * Método para obtener el listado de los menús del sistema
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoMenu($estado='todos', $order='', $page=0) {                   
        $columns = 'menu.*, (padre.menu) AS padre, (padre.posicion) AS padre_posicion, recurso.recurso';
        $join = 'LEFT JOIN recurso ON recurso.id = menu.recurso_id ';
        $join.= 'LEFT JOIN menu AS padre ON padre.id = menu.menu_id ';
        $conditions = 'menu.id IS NOT NULL';        
        if($estado!='todos') {
            $conditions.= ($estado==self::ACTIVO) ? " AND menu.activo=".self::ACTIVO : " AND menu.activo=".self::INACTIVO;
        }        
        $order = 'padre_posicion ASC, '.$this->get_order($order, 'posicion');
        if($page) {            
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        }
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
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
    public static function setMenu($method, $data, $optData=null) {        
        $obj = new Menu($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }                               
        //Verifico que no exista otro menu, y si se encuentra inactivo lo active
        $conditions = empty($obj->id) ? "recurso_id='$obj->recurso_id' AND visibilidad=$obj->visibilidad" : "recurso_id='$obj->recurso_id' AND visibilidad=$obj->visibilidad AND id != '$obj->id'";
        $old = new Menu();
        if($old->find_first($conditions)) {            
            if($method=='create' && $old->activo != Menu::ACTIVO) {
                $obj->id = $old->id;
                $obj->activo = Menu::ACTIVO;
                $method = 'update';
            } else {
                DwMessage::info('Ya existe un menú registrado para ese recurso y visibilidad.');
                return FALSE;
            }
        }
        return ($obj->$method()) ? $obj : FALSE;
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
        $this->menu = Filter::get($this->menu, 'string');
        $this->url = Filter::get($this->url, 'string');
        $this->icono = Filter::get($this->icono, 'string');
        $this->posicion = Filter::get($this->posicion, 'int');        
        if($this->id == 1 OR $this->id == 2) {
            DwMessage::warning('Lo sentimos, pero este menú no se puede editar.');
            return 'cancel';            
        }
    }
    
}
?>