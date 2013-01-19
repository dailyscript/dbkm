<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de los menús del sistema
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

class MenuController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Gestión de menús';
    }
    
    /**
     * Método principal
     */
    public function index() {
        DwRedirect::toAction('listar');
    }
    
    /**
     * Método para listar
     */
    public function listar($order='order.padre.asc', $page='pag.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $menu = new Menu();
        $this->menus = $menu->getListadoMenu('todos', $order, $page);                
        $this->order = $order;        
        $this->page_title = 'Listado de menús del sistema';
    }
    
    /**
     * Método para agregar
     */
    public function agregar() {
        if(Input::hasPost('menu')) {
            if(Menu::setMenu('create', Input::post('menu'), array('activo'=>Menu::ACTIVO))){
                DwMessage::valid('El menú se ha creado correctamente! <br/>Por favor recarga la página para verificar los cambios.');
                return DwRedirect::toAction('listar');
            }          
        }
        $this->page_title = 'Agregar menú';
    }
    
    /**
     * Método para editar
     */
    public function editar($key) {        
        if(!$id = DwSecurity::isValidKey($key, 'upd_menu', 'int')) {
            return DwRedirect::toAction('listar');
        }
        
        $menu = new Menu();
        if(!$menu->find_first($id)) {
            DwMessage::get('id_no_found');
            return DwRedirect::toAction('listar');
        }
        
        if($menu->id <= 2) {
            DwMessage::warning('Lo sentimos, pero este menú no se puede editar.');
            return DwRedirect::toAction('listar');
        }
        
        if(Input::hasPost('menu')) {
            if(DwSecurity::isValidKey(Input::post('menu_id_key'), 'form_key')) {
                if(Menu::setMenu('update', Input::post('menu'), array('id'>$id))){
                    DwMessage::valid('El menú se ha actualizado correctamente! <br/>Por favor recarga la página para verificar los cambios.');
                    return DwRedirect::toAction('listar');
                }
            }
        }
            
        $this->menu = $menu;
        $this->page_title = 'Actualizar menú';
        
    }
    
    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = DwSecurity::isValidKey($key, $tipo.'_menu', 'int')) {
            return DwRedirect::toAction('listar');
        }        
        
        $menu = new Menu();
        if(!$menu->find_first($id)) {
            DwMessage::get('id_no_found');            
        } else {
            if($menu->id <= 2) {
                DwMessage::warning('Lo sentimos, pero este menú no se puede editar.');
                return DwRedirect::toAction('listar');
            }
            if($tipo=='inactivar' && $menu->activo == Menu::INACTIVO) {
                DwMessage::info('El menú ya se encuentra inactivo');
            } else if($tipo=='reactivar' && $menu->activo == Menu::ACTIVO) {
                DwMessage::info('El menú ya se encuentra activo');
            } else {
                $estado = ($tipo=='inactivar') ? Menu::INACTIVO : Menu::ACTIVO;
                if(Menu::setMenu('update', $menu->to_array(), array('id'=>$id, 'activo'=>$estado))){
                    ($estado==Menu::ACTIVO) ? DwMessage::valid('El menú se ha reactivado correctamente!') : DwMessage::valid('El menú se ha inactivado correctamente!');
                }
            }                
        }
        
        return DwRedirect::toAction('listar');
    }
    
    /**
     * Método para eliminar
     */
    public function eliminar($key) {         
        if(!$id = DwSecurity::isValidKey($key, 'eliminar_menu', 'int')) {
            return DwRedirect::toAction('listar');
        }        
        
        $menu = new Menu();
        if(!$menu->find_first($id)) {
            DwMessage::get('id_no_found');
            return DwRedirect::toAction('listar');
        }      
        if($menu->id <= 2) {
            DwMessage::warning('Lo sentimos, pero este menú no se puede eliminar.');
            return DwRedirect::toAction('listar');
        }
        try {
            if($menu->delete()) {
                DwMessage::valid('El menú se ha eliminado correctamente!');
            } else {
                DwMessage::warning('Lo sentimos, pero este menú no se puede eliminar.');
            }
        } catch(KumbiaException $e) {
            DwMessage::error('Este menú no se puede eliminar porque se encuentra relacionado con otro registro.');
        }
        
        return DwRedirect::toAction('listar');
    }
    
}

