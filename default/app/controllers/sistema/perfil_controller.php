<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de los perfiles de usuario
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

class PerfilController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Perfiles de usuarios';
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
    public function listar($order='order.perfil.asc', $page='pag.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $perfiles = new Perfil();
        $this->perfiles = $perfiles->getListadoPerfil('todos', $order, $page);        
        $this->order = $order;        
        $this->page_title = 'Listado de perfiles de usuario';
    }
    
    /**
     * Método para agregar
     */
    public function agregar() {
        
        $this->page_title = 'Agregar perfil';
    }
    
}

