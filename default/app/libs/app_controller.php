<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador principal que heredan los controladores
 *
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */

//Cargo los parámetros de configuración
DwConfig::Load();

class AppController extends Controller {        

    /**
     * Callback que se ejecuta antes de los métodos de todos los controladores
     */
    final protected function initialize() {
        
    }

    /**
     * Callback que se ejecuta después de los métodos de todos los controladores
     */
    final protected function finalize() {        
        
    }

}
