<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga del mantenimiento a las tablas
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

Load::models('sistema/sistema');

class MantenimientoController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre de la página actual
        $this->page_title = 'Mantenimiento del sistema';        
    }
    
    /**
     * Método principal
     */
    public function index() {
        $sistema = new Sistema();
        $this->tablas = $sistema->getEstadoTablas();        
        
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Estado de las tablas';
    } 
    
    /**
     * Método para desfragmentar tablas
     */
    public function desfragmentar($key) {
        if(!$tabla = DwSecurity::isValidKey($key, 'desfragmentar')) {
            return DwRedirect::toAction('index');
        }
        try {
            $sistema = new Sistema();
            if($sistema->getDesfragmentacion($tabla)) {
                DwMessage::valid("Se ha desfragmentado la tabla '$tabla' correctamente!");
            } else {
                DwMessage::error('Se ha presentado un error interno al desfragmantar la tabla. <br />Por favor intenta más tarde.');
            }
        } catch(KumbiaException $e) {
            DwMessage::error('Oops! hemos realizado algo mal. <br />Por favor intenta más tarde.');
        }                        
        return DwRedirect::toAction('index');                    
    }
    
    /**
     * Método para vaciar el caché tablas
     */
    public function cache($key) {
        if(!$tabla = DwSecurity::isValidKey($key, 'cache')) {
            return DwRedirect::toAction('index');
        }
        try {
            $sistema = new Sistema();
            if($sistema->getVaciadoCache($tabla)) {
                DwMessage::valid("Se ha vaciado el caché de la tabla '$tabla' correctamente!");
            } else {
                DwMessage::error('Se ha presentado un error interno al vaciar el caché de la tabla. <br />Por favor intenta más tarde.');
            }
        } catch(KumbiaException $e) {
            DwMessage::error('Oops! hemos realizado algo mal. <br />Por favor intenta más tarde.');
        }                        
        return DwRedirect::toAction('index');                    
    }
    
    /**
     * Método para reparar tablas
     */
    public function reparar($key) {
        if(!$tabla = DwSecurity::isValidKey($key, 'reparar')) {
            return DwRedirect::toAction('index');
        }
        try {
            $sistema = new Sistema();
            if($sistema->getReparacionTabla($tabla)) {
                DwMessage::valid("Se ha reparado la tabla '$tabla' correctamente!");
            } else {
                DwMessage::error('Se ha presentado un error interno al reparar la tabla. <br />Por favor realízalo manualmente.');
            }
        } catch(KumbiaException $e) {
            DwMessage::error('Oops! hemos realizado algo mal. <br />Por favor intenta más tarde.');
        }                        
        return DwRedirect::toAction('index');                    
    }
    
    /**
     * Método para optimizar tablas
     */
    public function optimizar($key) {
        if(!$tabla = DwSecurity::isValidKey($key, 'optimizar')) {
            return DwRedirect::toAction('index');
        }
        try {
            $sistema = new Sistema();
            if($sistema->getOptimizacion($tabla)) {
                DwMessage::valid("Se ha optimizado la tabla '$tabla' correctamente!");
            } else {
                DwMessage::error('Se ha presentado un error interno al optimizar la tabla. <br />Por favor intenta más tarde.');
            }
        } catch(KumbiaException $e) {
            DwMessage::error('Oops! hemos realizado algo mal. <br />Por favor intenta más tarde.');
        }                        
        return DwRedirect::toAction('index');                    
    }
    
}

