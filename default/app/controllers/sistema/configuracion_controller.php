<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de la configuración del sistema
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

Load::models('sistema/sistema');

class ConfiguracionController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_title = 'Configuración del sistema';
    }
    
    /**
     * Método principal
     */
    public function index() {
        if(Input::hasPost('application') && Input::hasPost('custom')) {
            try {                
                Sistema::setConfig(Input::post('application'), 'application');
                Sistema::setConfig(Input::post('custom'), 'custom'); 
                DwMessage::valid('El archivo de configuración se ha actualizaco correctamente!');
            } catch(KumbiaException $e) {
                DwMessage::error('Oops!. Se ha realizado algo mal internamente. <br />Intentalo de nuevo!.');
            } 
        }
        $this->config = DwConfig::read('config', '', true);        
        $this->page_module = 'Configuración general';
    }               
    
    /**
     * Método para resetear las configuraciones del sistema
     * @return type
     */
    public function reset() {
        try {
            if(Sistema::reset()) {
                DwMessage::valid('El sistema se ha reseteado correctamente!');                            
            }
        } catch(KumbiaException $e) {                    
            DwMessage::error('Se ha producido un error al resetear la configuración del sistema.');                        
        }        
        return DwRedirect::toAction('index');
    }
}

