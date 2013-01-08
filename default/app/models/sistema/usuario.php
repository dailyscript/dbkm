<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Modelo para el manejo de usuarios
 *
 * @category    
 * @package     Models
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

Load::models('sistema/estado_usuario', 'sistema/perfil', 'sistema/recurso', 'sistema/recurso_perfil');

class Usuario extends ActiveRecord {
    
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
        return "INNER JOIN (SELECT usuario_id, CASE estado_usuario WHEN ".EstadoUsuario::COD_ACTIVO." THEN '".EstadoUsuario::ACTIVO."' WHEN ".EstadoUsuario::COD_BLOQUEADO." THEN '".EstadoUsuario::BLOQUEADO."' ELSE 'INDEFINIDO' END AS estado_usuario, fecha_estado_at FROM (SELECT id, estado_usuario, usuario_id, fecha_estado_at FROM estado_usuario ORDER BY estado_usuario.id DESC ) AS estado_usuario GROUP BY estado_usuario.usuario_id ) AS estado_usuario ON estado_usuario.usuario_id = usuario.id ";        
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
                        if( ($usuario->id!=1) &&  ($usuario->estado_usuario != EstadoUsuario::ACTIVO) ) { 
                            DwMessage::error('Lo sentimos pero tu cuenta se encuentra inactiva. <br />Si esta información es incorrecta contacta al administrador del sistema.');
                            DwAuth::logout();
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
    }
    
    /*
     * 
     * public static function getSession($opt='open') {  
        if($opt=='close') {
            //Verifico si tiene sesión
            if(!self::isLogin()) { 
                DwMessage::error("No has iniciado sesión o ha caducado. <br /> Por favor identifícate nuevamente.");
                return false;
            } else {                  
                $auth = Auth2::factory('model');
                $auth->logout();            
                unset($_SESSION['KUMBIA_SESSION'][APP_PATH]);
                return true;
            }
        } else if($opt=='open') {
            //Verifico si tiene una sesión válida
            if(self::isLogin()) {
                return true;
            } else {
                //Verifico si el formulario es válido
                if(DwSecurity::isValidForm()) {                     
                    $auth = Auth2::factory('model');                    
                    $auth->setLogin('login');
                    $auth->setPass('password');
                    $auth->setCheckSession(true);
                    $auth->setModel('sistema/usuario');                                        
                    $auth->setFields(array('id', 'persona_id', 'login', 'tema', 'ajax', 'perfil_id'));
                    if($auth->identify() && $auth->isValid()) {                         
                        $usuario = self::getUsuarioLogueado();                           
                        //Los id's de los usuarios comienzan desde el 101, salvo los super usuarios
                        if( ($usuario->id!=1) &&  ($usuario->estado_usuario != EstadoUsuario::ACTIVO) ) { 
                            DwMessage::error('Lo sentimos pero tu cuenta se encuentra inactiva. <br />Si esta información es incorrecta contacta al administrador del sistema.');
                            $auth->logout();                                                                             
                            return false;
                        }                    
                        
                        //Verifico los recursos
                        $recursos = new RecursoUsuario();
                        $recursos = $recursos->getRecursoUsuario($usuario->id);
                        if(!$recursos) {
                            DwMessage::error("Lo sentimos pero t cuenta no tiene recursos asignados.<br />Contacta al administrador del sistema.");
                            $auth->logout();
                            return false;
                        }                 
                        //Session::set('recursos',$recursos);                                                            
                        Session::set('nombre', $usuario->nombre);
                        Session::set('apellido', $usuario->apellido);                        
                        Session::set("ip", DwUtils::getIp());
                        Session::set('perfil', $usuario->perfil);
                        DwMessage::info("¡ Bienvenido <strong>$usuario->login</strong> !.");     
                        return true;
                    } else {
                        sleep(2);
                        DwMessage::error('El usuario y/o la contraseña son incorrectos.');
                    }
                } else {
                    DwMessage::info('La llave de acceso ha caducado. <br />Por favor intenta nuevamente.');
                }
                return false;
            }
        } else {
            DwMessage::error('No se ha podido establecer la sesión actual.');
            return false;
        }
    }
     */
    
}
?>
