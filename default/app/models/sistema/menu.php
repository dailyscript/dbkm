<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que gestiona los menús de los usuarios según los recursos asignados
 *
 * @category    Sistema
 * @package     Models
 * @subpackage  Usuarios
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Menu extends ActiveRecord {
    
    const ACTIVO = 1;
    const INACTIVO = 2;
    
    /**
     * Variable que contiene los menús 
     */
    protected static $_main = null;
    
    /**
     * Variable que contien los items del menú
     */        
    protected static $_items = null;
        
    /**
     * Método principal
     */
    public function initialize() {        
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
                $main.= DwHtml::link($menu->url, $text, array('class'=>'dropdown-toggle no-load', 'data-toggle'=>'dropdown'));
                if(array_key_exists($menu->menu, self::$_items)) {
                    $main.= '<ul class="dropdown-menu">';
                    foreach(self::$_items[$menu->menu] as $item) {                        
                        $active = ($item->url==$route) ? 'active' : null;
                        $main.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, null, $menu->icon).'</li>';
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
    
}
?>