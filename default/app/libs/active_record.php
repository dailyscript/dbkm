<?php
/**
 * @see KumbiaActiveRecord
 */
Load::coreLib('kumbia_active_record');

/**
 * ActiveRecord
 *
 * Esta clase es la clase padre de todos los modelos
 * de la aplicacion
 *
 * @category Kumbia
 * @package Db
 * @subpackage ActiveRecord
 */
class ActiveRecord extends KumbiaActiveRecord  {
    
    /**
     * Método que devuelve el order en SQL tomado de la url
     * @param string $s
     * @param string $default
     * @return string
     */
    protected function get_order($s, $default=NULL) {
        $s = explode('.', $s);        
        $column  = (empty($s[1])) ? $default : Filter::get($s[1], 'string');        
        $type = (empty($s[2])) ? NULL : strtoupper($s[2]);        
        return ($type!='ASC' && $type!='DESC') ? $column.' ASC' : $column.' '.$type;        
    }
    
    /**
     * Método para listar resultados de un find_all
     * @return Array ActiveRecord
     */
    public function paginated() {
        $args = func_get_args();
        array_unshift($args, $this);
        require_once APP_PATH . 'libs/dw_paginate.php';
        return call_user_func_array(array('DwPaginate' , 'paginate'), $args);
    }       

    /**
     * Método para listar resultados a través de un sql directo
     * @param string $sql
     * @return Array ActiveRecord
     */
    public function paginated_by_sql($sql) {
        $args = func_get_args();
        array_unshift($args, $this);
        require_once APP_PATH . 'libs/dw_paginate.php';
        return call_user_func_array(array('DwPaginate' , 'paginate_by_sql'), $args);
    }
}
