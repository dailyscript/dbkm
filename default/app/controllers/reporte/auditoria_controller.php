<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la visualización de los reportes de las acciones en el sistema
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

Load::models('sistema/sistema');

class AuditoriaController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        $this->page_title = 'Auditoría y seguimientos';
        //Se cambia el nombre del módulo actual        
        $this->page_module = 'Listado de acciones de los usuarios';
    }
        
    /**
     * Método para listar las autitorías del sistema
     * @param type $fecha
     * @return type
     */
    public function listar($fecha='', $formato='html') {
        $fecha = empty($fecha) ? date("Y-m-d") : Filter::get($fecha, 'date');
        if(empty($fecha)) {
            DwMessage::info('Selecciona la fecha del archivo');
            return View::error();
        }        
        
        $audits = Sistema::getAudit($fecha);
        $this->audits = $audits;
        $this->fecha = $fecha;
        $this->page_module = 'Auditorías del sistema '.$fecha;
        $this->page_format = $formato;        
    }
        
}

