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
        if(Input::hasPost('perfil')) {
            if(Perfil::setPerfil('create', Input::post('perfil'), array('estado'=>Perfil::ACTIVO))){
                DwMessage::valid('El perfil se ha registrado correctamente!');
                return DwRedirect::toAction('listar');
            }          
        }
        $this->page_title = 'Agregar perfil';
    }
    
    /**
     * Método para editar
     */
    public function editar($key) {        
        if(!$id = DwSecurity::isValidKey($key, 'upd_perfil', 'int')) {
            return DwRedirect::toAction('listar');
        }        
        
        $perfil = new Perfil();
        if(!$perfil->find_first($id)) {
            DwMessage::get('id_no_found');
            return DwRedirect::toAction('listar');
        }
        
        if(Input::hasPost('perfil')) {                         
            if(DwSecurity::isValidKey(Input::post('perfil_id_key'), 'form_key')) {
                if(Perfil::setPerfil('update', Input::post('perfil'), array('id'>$id))){
                    DwMessage::valid('El perfil se ha actualizado correctamente!');
                    return DwRedirect::toAction('listar');
                }
            }
        }
            
        $this->perfil = $perfil;
        $this->page_title = 'Actualizar perfil';                
        
    }
    
    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = DwSecurity::isValidKey($key, $tipo.'_perfil', 'int')) {
            return DwRedirect::toAction('listar');
        }        
        
        $perfil = new Perfil();
        if(!$perfil->find_first($id)) {
            DwMessage::get('id_no_found');            
        } else {
            if($tipo=='inactivar' && $perfil->estado == Perfil::INACTIVO) {
                DwMessage::info('El perfil ya se encuentra inactivo');
            } else if($tipo=='reactivar' && $perfil->estado == Perfil::ACTIVO) {
                DwMessage::info('El perfil ya se encuentra activo');
            } else {
                $estado = ($tipo=='inactivar') ? Perfil::INACTIVO : Perfil::ACTIVO;
                if(Perfil::setPerfil('update', $perfil->to_array(), array('id'=>$id, 'estado'=>$estado))){
                    ($estado==Perfil::ACTIVO) ? DwMessage::valid('El perfil se ha reactivado correctamente!') : DwMessage::valid('El perfil se ha inactivado correctamente!');
                }
            }                
        }
        
        return DwRedirect::toAction('listar');
    }
    
    /**
     * Método para ver
     */
    public function ver($key, $order='order.perfil.asc', $page='pag.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;     
        if(!$id = DwSecurity::isValidKey($key, 'show_perfil', 'int')) {
            return DwRedirect::toAction('listar');
        }        
        
        $perfil = new Perfil();
        if(!$perfil->find_first($id)) {
            DwMessage::get('id_no_found');
            return DwRedirect::toAction('listar');
        }        
        
        $usuario = new Usuario();
        $this->usuarios = $usuario->getUsuarioPorPerfil($perfil->id, $order, $page);
        
        $this->perfil = $perfil;
        $this->order = $order;        
        $this->page_title = 'Información del Perfil';
        $this->key = $key;        
    }
    
}

