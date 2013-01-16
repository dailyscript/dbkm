<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de la empresa
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

Load::models('config/empresa', 'config/sucursal');

class EmpresaController extends BackendController {
    
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
        
        if(Input::hasPost('empresa')) { 
            if(DwSecurity::isValidKey(Input::post('empresa_id_key'), 'form_key')) {                
                if(Empresa::setEmpresa('save', Input::post('empresa'))) {
                    DwMessage::valid('Los datos se han actualizado correctamente');
                } else {
                    DwMessage::get('error_form');
                }
            }
        }        
        
        $empresa = new Empresa();
        if(!$empresa->getInformacionEmpresa()) {
            DwMessage::get('id_no_found');    
            return DwRedirect::to('dashboard');
        }
        
        $this->empresa = $empresa;
        $this->page_title = 'Información de la empresa';
    }
        
}

