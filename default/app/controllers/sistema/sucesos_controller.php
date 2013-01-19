<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la visualización de los logs del sistema
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

Load::models('sistema/sistema');

class SucesosController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        $this->page_title = 'Visor de sucesos';
        //Se cambia el nombre del módulo actual        
        $this->page_module = 'Logs del sistema';
    }
    
    /**
     * Método principal
     */
    public function index() {
        if(Input::hasPost('log')) {
            return DwRedirect::toAction('listar', Input::post('log'));
        }
        if(!APP_LOGGER) {
            DwMessage::info('No se encuentra activo el registro de los logs del sistema.');
        }
    }
    
    /**
     * Método para listar los logs del sistema
     * @param type $fecha
     * @return type
     */
    public function listar($fecha='', $page=1) {
        $fecha = empty($fecha) ? date("Y-m-d") : Filter::get($fecha, 'date');
        if(empty($fecha)) {
            DwMessage::info('Selecciona la fecha del archivo');
            return DwRedirect::toAction('index');
        }
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        
        $loggers = Sistema::getLogger($fecha, $page);
        $this->loggers = $loggers;
        $this->fecha = $fecha;
        $this->page_module = 'Logs del sistema '.$fecha;
        
    }
        
}

