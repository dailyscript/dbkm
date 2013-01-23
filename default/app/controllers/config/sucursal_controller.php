<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de las sucursales de la empresa
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

Load::models('config/sucursal');

class SucursalController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Configuraciones';
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
    public function listar($order='order.sucursal.asc', $page='pag.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $sucursal = new Sucursal();        
        $this->sucursales = $sucursal->getListadoSucursal($order, $page);
        $this->order = $order;        
        $this->page_title = 'Listado de sucursales';
    }
    
    /**
     * Método para agregar
     */
    public function agregar() {
        $empresa = Session::get('empresa', 'config');
        if(Input::hasPost('sucursal')) {
            if(Sucursal::setSucursal('create', Input::post('sucursal'), array('empresa_id'=>$empresa->id, 'ciudad'=>Input::post('ciudad')))) {
                DwMessage::valid('La sucursal se ha registrado correctamente!');
                return DwRedirect::toAction('listar');
            }            
        } 
        $this->ciudades = Load::model('params/ciudad')->getCiudadesToJson();
        $this->page_title = 'Agregar sucursal';
    }
    
    /**
     * Método para editar
     */
    public function editar($key) {        
        if(!$id = DwSecurity::isValidKey($key, 'upd_sucursal', 'int')) {
            return DwRedirect::toAction('listar');
        }        
        
        $sucursal = new Sucursal();
        if(!$sucursal->getInformacionSucursal($id)) {            
            DwMessage::get('id_no_found');
            return DwRedirect::toAction('listar');
        }
        
        if(Input::hasPost('sucursal') && DwSecurity::isValidKey(Input::post('sucursal_id_key'), 'form_key')) {
            if(Sucursal::setSucursal('update', Input::post('sucursal'), array('id'=>$id, 'empresa_id'=>$sucursal->empresa_id, 'ciudad'=>Input::post('ciudad')))) {
                DwMessage::valid('La sucursal se ha actualizado correctamente!');
                return DwRedirect::toAction('listar');
            }
        } 
        $this->ciudades = Load::model('params/ciudad')->getCiudadesToJson();
        $this->sucursal = $sucursal;
        $this->page_title = 'Actualizar sucursal';        
    }
    
    /**
     * Método para eliminar
     */
    public function eliminar($key) {         
        if(!$id = DwSecurity::isValidKey($key, 'del_sucursal', 'int')) {
            return DwRedirect::toAction('listar');
        }        
        
        $sucursal = new Sucursal();
        if(!$sucursal->getInformacionSucursal($id)) {            
            DwMessage::get('id_no_found');
            return DwRedirect::toAction('listar');
        }                
        try {
            if(Sucursal::setSucursal('delete', array('id'=>$sucursal->id))) {
                DwMessage::valid('La sucursal se ha eliminado correctamente!');
            }
        } catch(KumbiaException $e) {
            DwMessage::error('Esta sucursal no se puede eliminar porque se encuentra relacionada con otro registro.');
        }
        
        return DwRedirect::toAction('listar');
    }
    
}

