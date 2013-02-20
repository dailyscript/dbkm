<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Clase que se encarga de todo lo relacionado con la información de la empresa
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class Empresa extends ActiveRecord {

    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {        
        $this->belongs_to('tipo_nuip');
        $this->has_many('sucursal');                                
        $this->validates_presence_of('razon_social', 'message: Ingresa el nombre de la empresa');
        $this->validates_presence_of('representante_legal', 'message: Ingresa el nombre del propietario o representante legal.');
        $this->validates_presence_of('nuip', 'message: Ingresa el NUIP o NIT de la empresa.');
        $this->validates_presence_of('tipo_nuip_id', 'message: Selecciona el tipo de identificación.');        
        $this->validates_email_in('email', 'message: El correo electrónico es incorrecto.');
    }

    /**
     * Método para obtener la información de la empresa
     * @return obj
     */
    public function getInformacionEmpresa() {
        $columnas = 'empresa.*, tipo_nuip.tipo_nuip';
        $join = 'INNER JOIN tipo_nuip ON tipo_nuip.id = empresa.tipo_nuip_id';
        return $this->find_first("columns: $columnas", "join: $join", 'conditions: empresa.id IS NOT NULL', 'order: empresa.registrado_at DESC');
    }    
    
    /**
     * Método para registrar y modificar los datos de la empresa
     * 
     * @param string $method Método para guardar en la base de datos (create, update)
     * @param array $data Array de datos para la autocarga de objetos
     * @param arraty $other Se utiliza para autocargar datos adicionales al objeto
     * @return Empresa
     */
    public static function setEmpresa($method, $data, $optData=null) {
        $obj = new Empresa($data);
        if($optData) {
            $obj->dump_result_self($optData);
        }
        $rs = $obj->$method();
        return ($rs) ? $obj : NULL;            
    }
    
    public function after_save() {
        Session::delete('empresa', 'config');
        //Si no está habilitado para el manejo de sucursal
        //registro la ubicación de la empresa como sucursal
        if(!APP_OFFICE && Input::hasPost('sucursal')) {             
            Sucursal::setSucursal('save', Input::post('sucursal'), array('sucursal'=>'Oficina Principal', 'ciudad'=>Input::post('ciudad'), 'empresa_id'=>$this->id));
        }
    }

    /**
     * Método para filtrar la información de la empresa
     */
    public function getFiltradoEmpresa() {        
        $this->razon_social = Filter::get($this->razon_social, 'string');
        $this->siglas = Filter::get($this->siglas, 'string');
        $this->representante_legal = Filter::get($this->representante_legal, 'string');
        $this->nit = Filter::get($this->nit, 'numeric');
        $this->dv = Filter::get($this->dv, 'numeric');        
        $this->nuip = Filter::get($this->nuip, 'numeric');
        $this->email = Filter::get($this->email, 'string');
        $this->pagina_web = Filter::get($this->pagina_web, 'string');        
    }
    
}
?>

