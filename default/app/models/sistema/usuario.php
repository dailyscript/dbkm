<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Modelo para el manejo de usuarios
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

Load::models('sistema/estado_usuario', 'sistema/perfil', 'sistema/recurso', 'sistema/recurso_perfil');

class Usuario extends ActiveRecord {
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('persona');
        $this->belongs_to('perfil');
        $this->has_many('estado_usuario');        
    }
    
    /**
     * Método que devuelve el inner join con el estado_usuario
     * @return string
     */
    public static function getInnerEstado() {
        return "INNER JOIN (SELECT usuario_id, CASE estado_usuario WHEN ".EstadoUsuario::COD_ACTIVO." THEN '".EstadoUsuario::ACTIVO."' WHEN ".EstadoUsuario::COD_BLOQUEADO." THEN '".EstadoUsuario::BLOQUEADO."' ELSE 'INDEFINIDO' END AS estado_usuario, descripcion, fecha_estado_at FROM (SELECT * FROM estado_usuario ORDER BY estado_usuario.id DESC ) AS estado_usuario GROUP BY estado_usuario.usuario_id ) AS estado_usuario ON estado_usuario.usuario_id = usuario.id ";        
    }
    
    /**
     * Método para abrir y cerrar sesión
     * @param type $opt
     * @return boolean
     */
    public static function setSession($opt='open', $user=NULL, $pass=NULL, $mode=NULL) {  
        if($opt=='close') {
            if(DwAuth::logout()) {                
                return true;
            }                
            DwMessage::error(DwAuth::getError()); 
        } else if($opt=='open') {            
            if(DwAuth::isLogged()) {
                return true;
            } else {
                if(DwSecurity::isValidForm()) {
                    if(DwAuth::login(array('login'=>$user), array('password'=>sha1($pass)), $mode)) {
                        $usuario = self::getUsuarioLogueado();                         
                        if( ($usuario->id!=2) &&  ($usuario->estado_usuario != EstadoUsuario::ACTIVO) ) { 
                            DwAuth::logout();
                            DwMessage::error('Lo sentimos pero tu cuenta se encuentra inactiva. <br />Si esta información es incorrecta contacta al administrador del sistema.');
                            return false;
                        } 
                        Session::set('nombre', $usuario->nombre);
                        Session::set('apellido', $usuario->apellido);                        
                        Session::set("ip", DwUtils::getIp());
                        Session::set('perfil', $usuario->perfil);
                        Session::set('tema', $usuario->tema);
                        Session::set('app_ajax', $usuario->app_ajax);
                        DwMessage::info("¡ Bienvenido <strong>$usuario->login</strong> !.");     
                        return true;
                    } else {
                        DwMessage::error(DwAuth::getError());
                    }
                } else {
                    DwMessage::info('La llave de acceso ha caducado. <br />Por favor intenta nuevamente.');                
                }
            }                      
        } else {
            DwMessage::error('No se ha podido establecer la sesión actual.');            
        }
        return false;  
    }
            
    /**
     * Método para obtener la información de un usuario logueado
     * @return object Usuario
     */
    public static function getUsuarioLogueado() {
        $columnas = 'usuario.*, perfil.perfil, persona.nombre, persona.apellido, estado_usuario.estado_usuario';
        $join = "INNER JOIN persona ON persona.id = usuario.persona_id ";
        $join.= "INNER JOIN perfil ON perfil.id = usuario.perfil_id ";
        $join.= self::getInnerEstado();
        $condicion = "usuario.id = '".Session::get('id')."'";
        $obj = new Usuario();
        return $obj->find_first("columns: $columnas", "join: $join", "conditions: $condicion");
    }  
    
    
    /**
     * Método para listar los usuarios por perfil
     */
    public function getUsuarioPorPerfil($perfil, $order='', $page=0) {
        $perfil = Filter::get($perfil, 'int');
        if(empty($perfil)) {
            return NULL;
        }
        $columns = 'usuario.*, persona.nombre, persona.apellido, perfil.perfil';
        $join = 'INNER JOIN persona ON persona.id = usuario.persona_id ';
        $join.= 'INNER JOIN perfil ON perfil.id = usuario.perfil_id ';
        $conditions = "perfil.id = $perfil";
        $order = $this->get_order($order, 'nombre');        
        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        } 
        return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
    }
    
    
    public function getListadoUsuario($estado, $order='', $page=0) {
        $columns = 'usuario.*, persona.nombre, persona.apellido, estado_usuario.estado_usuario, estado_usuario.descripcion, sucursal.sucursal';
        $join = self::getInnerEstado();
        $join.= 'INNER JOIN perfil ON perfil.id = usuario.perfil_id ';
        $join.= 'INNER JOIN persona ON persona.id = usuario.persona_id ';        
        $join.= 'LEFT JOIN sucursal ON sucursal.id = usuario.sucursal_id ';
        $conditions = "usuario.id > '2'";//Por el super usuario
        
        $order = $this->get_order($order, 'nombre', array(  'id'        =>'usuario.id', 
                                                            'nombre'    =>array(
                                                                                'ASC'=>'persona.nombre ASC, persona.apellido DESC', 
                                                                                'DESC'=>'persona.nombre DESC, persona.apellido DESC')) );        
        if($estado == 'activos') {
            $conditions.= " AND estado_usuario.estado_usuario = '".EstadoUsuario::USR_ACTIVO."'";
        } else if($estado == 'bloqueados') {
            $conditions.= " AND estado_usuario.estado_usuario = '".EstadoUsuario::USR_BLOQUEADO."'";
        }          
        
        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        }  
    }
    
    /**
     * Método para crear/modificar un objeto de base de datos
     * 
     * @param string $medthod: create, update
     * @param array $data: Data para autocargar el modelo
     * @param array $otherData: Data adicional para autocargar
     * 
     * @return object ActiveRecord
     */
    public static function setUsuario($method, $data, $otherData=null) {
        $obj = new Usuario($data);
        if($otherData) {
            $obj->dump_result_self($otherData);
        }
        if(!empty($obj->id)) { //Si va a actualizar
            $old = new Usuario();
            $old->find_first_by_id($old->id);
            if(!empty($obj->oldpassword)) { //Si cambia de claves
                $obj->oldpassword = md5(sha1($obj->oldpassword));
                if($obj->oldpassword !== $old->password) {
                    DwMessage::error("La contraseña anterior no coincide con la registrada. Verifica los datos e intente nuevamente");
                   return false;
                }
            }
        }
        $rs = $obj->$method();
        return ($rs) ? $obj : FALSE;
    }
    
    /**
     * Método para verificar si existe un campo registrado
     */
    protected function _getRegisteredField($field, $value, $id=NULL) {                
        $conditions = "$field = '$value'";
        $conditions.= (!empty($id)) ? " AND id != $id" : '';
        return $this->count("conditions: $conditions");
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    protected function before_save() {
        //Verifico la sucursal al crear el usuario
        if(empty($this->id)) {
            if(APP_OFFICE) {                                
                $this->sucursal_id = ($this->sucursal_id=='todas') ? NULL : Filter::get($this->sucursal_id, 'int');                
            } else {
                $this->sucursal_id = Sucursal::OFICINA_PRINCIPAL;
            }
        }        
        //Verifico si las contraseñas coinciden (password y repassword)
        if(!empty($this->password) && isset($this->repassword)) {            
            $this->password = md5(sha1($this->password));
            $this->repassword = md5(sha1($this->repassword));            
            if($this->password !== $this->repassword) {
                DwMessage::error('Las contraseñas no coinciden. Verifica los datos e intenta nuevamente.');
                return 'cancel';
            }
        }
        //Verifico las exclusiones de los nombres de usuarios del config.ini   
        $exclusion = DwConfig::read('config', array('custom'=>'login_exclusion') );        
        $exclusion = explode(',', $exclusion);
        if(!empty($exclusion)) {
            if(in_array($this->login, $exclusion)) {
                DwMessage::error('El nombre de usuario indicado, no se encuentra disponible.');
                return 'cancel';
            }
        }        
        //Verifico si el login está disponible
        if($this->_getRegisteredField('login', $this->login, $this->id)) {
            DwMessage::error('El nombre de usuario no se encuentra disponible.');
            return 'cancel';
        }
        //Verifico si ya se encuentra registrado
        if($this->_getRegisteredField('persona_id', $this->persona_id, $this->id)) {
            DwMessage::error('La persona registrada ya posee una cuenta de usuario.');
            return 'cancel';
        } 
        //Verifico si se encuentra el mail registrado
        if($this->_getRegisteredField('email', $this->email, $this->id)) {
            DwMessage::error('El correo electrónico ya se encuentra registrado.');
            return 'cancel';
        }
        
    }
    
    /**
     * Callback que se ejecuta despues de insertar un usuario
     */
    protected function after_create() {        
        if(!EstadoUsuario::setEstadoUsuario('registrar', array('usuario_id'=>$this->id, 'descripcion'=>'Activado por registro inicial'))){
            DwMessage::error('Se ha producido un error interno al activar el usuario. Pofavor intenta nuevamente.');
            return 'cancel';
        }
    }
    
    /**
     * Método para obtener la información de un usuario
     * @return type
     */
    public function getInformacionUsuario($usuario) {
        $usuario = Filter::get($usuario, 'int');
        if(!$usuario) {
            return NULL;
        }
        $columnas = 'usuario.*, perfil.perfil, persona.nombre, persona.apellido, estado_usuario.estado_usuario, estado_usuario.descripcion, sucursal.sucursal';
        $join = self::getInnerEstado();
        $join.= 'INNER JOIN perfil ON perfil.id = usuario.perfil_id ';
        $join.= 'INNER JOIN persona ON persona.id = usuario.persona_id ';        
        $join.= 'LEFT JOIN sucursal ON sucursal.id = usuario.sucursal_id ';
        $condicion = "usuario.id = $usuario";        
        return $this->find_first("columns: $columnas", "join: $join", "conditions: $condicion");
    } 
       
    
}
?>
