<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de las cuentas de usuario
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

Load::models('personas/persona', 'config/sucursal');

class MiCuentaController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Mi Cuenta';
    }
    
    /**
     * Método principal
     */
    public function index() {
        $usuario = new Usuario();
        if(!$usuario->getInformacionUsuario(Session::get('id'))) {
            DwMessage::get('id_no_found');    
            return DwRedirect::to('dashboard');
        }                
        
        $app_ajax_old = $usuario->app_ajax;
        
        if(Input::hasPost('usuario')) {
            if(DwSecurity::isValidKey(Input::post('usuario_id_key'), 'form_key')) {
                ActiveRecord::beginTrans();
                //Guardo la persona
                $persona = Persona::setPersona('update', Input::post('persona'), array('id'=>$usuario->persona_id));
                if($persona) {
                    $usuario = Usuario::setUsuario('update', Input::post('usuario'), array('persona_id'=>$persona->id, 'repassword'=>Input::post('repassword'), 'oldpassword'=>Input::post('oldpassword'), 'id'=>$usuario->id, 'login'=>$usuario->login, 'sucursal_id'=>$usuario->sucursal_id));
                    if($usuario) {
                        ActiveRecord::commitTrans();
                        DwMessage::valid('El usuario se ha actualizado correctamente.');                        
                        if($app_ajax_old != $usuario->app_ajax) {
                            Session::set('app_ajax', $usuario->app_ajax);
                            if(APP_AJAX){
                                View::redirect('/sistema/mi_cuenta/'); 
                            } else {
                                DwRedirect::to('sistema/mi_cuenta');
                            }
                        }
                    }
                } else {
                    ActiveRecord::rollbackTrans();
                } 
            }
        }        
        $this->temas = DwUtils::getFolders(dirname(APP_PATH).'/public/css/themes/');
        $this->usuario = $usuario;
        $this->page_title = 'Actualizar mis datos';
    }
        
}

