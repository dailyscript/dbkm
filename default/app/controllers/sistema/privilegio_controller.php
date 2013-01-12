<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de los permisos a los perfiles de usuarios
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

class PrivilegioController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Gestión de permisos';
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
    public function listar($order='order.modulo.asc', $page='pag.1') { 
        
        if(Input::hasPost('privilegios') OR Input::hasPost('old_privilegios')) {
            if(RecursoPerfil::setRecursoPerfil(Input::post('privilegios'), Input::post('old_privilegios'))) {
                DwMessage::valid('Los privilegios se han registrado correctamente!');                
                Input::delete('privilegios');//Para que no queden persistentes
                Input::delete('old_privilegios');
            }
        }
        
        
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;                 
        $recurso = new Recurso();
        $this->recursos = $recurso->getListadoRecurso(Recurso::ACTIVO, $order, $page);
        $perfil = new Perfil();
        $this->perfiles = $perfil->getListadoPerfil(Perfil::ACTIVO);
        
        $privilegio = new RecursoPerfil();
        $this->privilegios = $privilegio->getPrivilegiosToArray();
        
        $this->order = $order;        
        $this->page_title = 'Permisos y privilegios de usuarios';        
    }   
}

