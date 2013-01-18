<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Clase que permite el mantenimiento a las tablas de la base de datos
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Sistema {
    
    /**
     * Variable que contiene las tablas del sistema
     * @var type 
     */
    protected $_tables = array();
    
    /**
     * Varible que contiene la conexión
     */
    protected $_db;
    
    /**
     * Variable con el pull de conexión
     */
    protected $_database;

    /**
     * Método contructor
     */
    public function __construct() {
        //Reviso la configuración actual
        $config = Config::read('config');
        $this->_database =  $config['application']['database'];
        //Conecto a la bd
        $this->_connect(); 
        //Cargo las tablas
        $this->_loadTables();
    }
    
    /**
     * Se conecta a la base de datos 
     *
     * @param boolean $new_connection
     */
    protected function _connect($new_connection = false) {
        if (!is_object($this->_db) || $new_connection) {
            $this->_db = Db::factory($this->_database, $new_connection);
        }        
    }
    
    /**
     * Método almacenar las tablas
     */
    protected function _loadTables() {
        $tablas = $this->_db->list_tables();
        foreach($tablas as $tabla) {            
            $this->_tables[] = $tabla[0];
        }
    }
    
    /**
     * Método para listar las tablas
     */
    public function getEstadoTablas() {        
        $all_status = array();        
        $tables = $this->_db->fetch_all("SHOW TABLE STATUS"); 
        foreach($tables as $table) {
            $status = $this->_db->fetch_all('CHECK TABLE '.$table['Name']);
            $status = $status[0];
            $table['Op'] = $status['Op'];
            $table['Msg_type'] = $status['Msg_type'];
            $table['Msg_text'] = $status['Msg_text'];            
            $all_status[] = $table;            
        }                
        return $all_status;        
    }
    
    /**
     * Método para desfragmentar una tabla
     */
    public function getDesfragmentacion($tabla) {
        if(in_array($tabla, $this->_tables)) {
            return ($this->_db->query("ALTER TABLE $tabla ENGINE=INNODB"));            
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para vaciar el cache de una tabla
     */
    public function getVaciadoCache($tabla) {
        if(in_array($tabla, $this->_tables)) {
            return ($this->_db->query("FLUSH TABLE $tabla"));            
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para reparar una tabla
     */
    public function getReparacionTabla($tabla) {
        if(in_array($tabla, $this->_tables)) {
            return ($this->_db->query("REPAIR TABLE $tabla"));
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para optimizar una tabla
     */    
    public function getOptimizacion($tabla) {
        if(in_array($tabla, $this->_tables)) {
            return ($this->_db->query("OPTIMIZE TABLE $tabla"));            
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para leer los logs del sistema
     */
    public function getLogger($fecha, $page) {
        $log = DwRead::file('log'.$fecha);
        //Armo un nuevo array para ordenarlos 
        $contador = 0;
        $new_log = array();
        if(!empty($log)) {
            foreach($log as $key => $row) {
                $data = explode(']', $row);
                $new_log[$contador]['item'] = $contador;
                $new_log[$contador]['fecha'] = date("Y-m-d H:i:s", strtotime(trim($data[0],'[')));
                $new_log[$contador]['tipo'] = trim($data[1],'[');
                $new_log[$contador]['descripcion'] = trim($data[2],'[');
                $contador++;
            }                
        }
        $result = DwUtils::orderArray($new_log, 'item', TRUE);                
        //Pagino el array
        $paginate = new DwPaginate();
        return $paginate->paginate($result, "page: $page", "per_page: 50");                
    }
    
    
    /**
     * Método para actualizar el archivo config.ini según los parámetros enviados
     * 
     * @param type $data Campos de los formularios
     * @param type $source Production o Deveploment
     * @param type $createDb Indica si se crea o no la base de datos
     * @return boolean
     */
    public static function setConfig($data, $source='application') {        
        //Verifico si tiene permisos de escritura para crear y editar un archvivo.ini
        if(!is_writable(APP_PATH.'config')) {            
            DwMessage::warning('Asigna temporalmente el permiso de escritura a la carpeta "config" de tu app!.');
            return false;
        }     
        //Filtro el array
        $data = Filter::data($data, null, 'trim');         
        return DwConfig::write('config', $data, $source);
    }
}
?>
