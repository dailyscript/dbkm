<?php
/**
 * Dailyscript - Web | App | media
 *
 * Extension para el manejo de formularios que hereda atributos de la clase Form
 *
 * @category    Views
 * @author      Iván D. Meléndez
 * @package     Helpers
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class DwForm extends Form {
    
    /**
     * Contador para los checbox y radios
     * @var int
     */
    protected static $_counter = 0;    
    
    /**
     * Contador para los formularios abiertos
     * @var int
     */
    protected static $_form = 0;
    
    /**
     * Identificación del formulario abierto
     * @var array
     */
    protected static $_name = array();
    
    /**
     *Tipo de estilo de formulario 
     */
    protected static $_style='form-vertical';
    
    /**
     * Variable que indica si muestra el label
     */
    protected static $_show_label = false;
    
    /**
     * Variable que indica si muestra el help block
     */
    protected static $_help_block = false;
    
    /**
     * Método para obtener el id y el nombre de un campo bajo el patrón modelo.campo
     * @param string $field
     * @return array
     */
    protected static function _getFieldName($field) {
        $formField = explode('.', $field, 2);
        if(isset($formField[1])) {
            $id = "{$formField[0]}_{$formField[1]}";
            $name = "{$formField[0]}[{$formField[1]}]";
        } else {
            $id = "{$formField[0]}";
            $name = "{$formField[0]}";
        }        
        return array('id' => $id, 'name' => $name);
    }
    
    /**
     * Método que utiliza los atributos de un input o form para aplicar parámetros por defecto
     * @param type $attrs
     * @param type $type
     * @return string 
     */    
    protected static function _getAttrsClass($attrs, $type) {
        if($type=='form' or $type=='form-multipart') { 
            $formAjax = true;
            if(isset($attrs['class'])) {  
                if(preg_match("/\bno-ajax\b/i", $attrs['class'])){
                    $formAjax = false;
                }                
                //Verifico si está definida la clase dw-form                
                if(!preg_match("/\bdw-form\b/i", $attrs['class'])) {
                    $attrs['class'] = 'dw-form '.$attrs['class'];
                }    
                //Verifico si el aplicativo está con ajax
                if(APP_AJAX && $type=='form' && $formAjax) {
                    //Verifico si está definida la clase para ajax
                    if(!preg_match("/\bjs-remote\b/i", $attrs['class'])) {
                        $attrs['class'] = 'js-remote '.$attrs['class'];
                    }                    
                }
            } else {
                //Asigno que pertenece a la clase dw-form y si utiliza ajax
                $attrs['class'] = (APP_AJAX && $type=='form') ? 'dw-form js-remote' : 'dw-form';
            }
                        
            if(APP_AJAX && $type=='form' && $formAjax && !isset($attrs['data-div'])) { //Si es un form con ajax verifico si está definido el data-div
                $attrs['data-div'] = 'dw-shell-content';
            }                                   
            if(!isset($attrs['id'])) { //Verifico si está definido el id
                $attrs['id'] = 'form-'.self::$_form; 
            }
            if(!isset($attrs['name'])) { //Verifico si está definido el name
                $attrs['name'] = $attrs['id'];
            }
            self::$_style = (isset($attrs['style-form'])) ? $attrs['style-form'] : 'form-vertical';                        
            self::$_show_label = (self::$_style=='form-search' or self::$_style=='form-inline') ? false : true;
            self::$_help_block = (self::$_style=='form-search') ? false : true;            
            self::$_name['id'] = $attrs['id'];           
            self::$_name['name'] = $attrs['name'];            
            //asigno el estilo al formulario
            $attrs['class'] = $attrs['class'].' '.self::$_style;
            self::$_form++;
            
        } else {            
            if(isset($attrs['class'])) { 
                //Verifico las clases segun el tipo text, select, textarea, file, numeric, date
                if(!preg_match("/\b$type\b/i", $attrs['class'])) {
                    $attrs['class'] = $type.' '.$attrs['class'];
                }
                //Verifico si está la clase field
                if(!preg_match("/\bfield\b/i", $attrs['class'])) {
                    $attrs['class'] = 'field '.$attrs['class'];
                }
                //Verifico si el campo es moneda
                if(preg_match("/\bmoney\b/i", $attrs['class'])) {
                    //@see var.js
                    $attrs['onkeyup'] = 'jsMiles(this,this.value.charAt(this.value.length-1))';                    
                }
            } else {
                //Si no está definida las clases las asigno según el tipo
                $attrs['class'] = ( ($type != 'checkbox') && ($type != 'radio') ) ? "field $type input-full " : "field $type ";
            }            
            //Verifico si se utiliza la mayúscula solo para los text y textarea
            if( ($type=='text') && ($type=='textarea') ) {
                if( !preg_match("/\blower\b/i", $attrs['class']) or preg_match("/\bupper\b/i", $attrs['class']) )  {
                    $attrs['onchange'] = !isset($attrs['onchange']) ? 'this.value=this.value.toUpperCase()' : rtrim($attrs['onchange'],';').'; this.value=this.value.toUpperCase()';
                }
            }        
            //Reviso si es readonly
            if(preg_match("/\breadonly\b/i", $attrs['class'])) {
                $attrs['readonly'] = 'readonly';
            }
            //Reviso si esta deshabilitado
            if(preg_match("/\bdisabled\b/i", $attrs['class'])) {
                $attrs['disabled'] = 'disabled';
            }
            //Verifico el data-action del input (cuando utiliza ajax)
            if(isset($attrs['data-action'])) {
                $attrs['data-action'] = PUBLIC_PATH.trim($attrs['data-action'], '/').'/';
            }
        }                        
        return $attrs;
    }
    
    /**
     * Método para cargar los archivos necesarios para las validaciones del los fomularios
     * @staticvar boolean $js_form
     * @param boolean $valid Indica si el formulario se valida
     * @param boolean $extension Indica si la validadción tiene una extensión utilizando la función extValidarForm()
     * @param type $confirm Indica si el formulario requiere un mensaje de confirmación
     * @return string
     */
    protected static function _getValidationForm($valid, $extension, $confirm) {                        
        $validation = '';
        if($valid) {            
            static $f = true;
            if($f == true) {
                $f = false;
                //Incluyo el archivo de validación una sola vez
                //@see javascript/utils/validador.js
                $validation.= Tag::js('utils/validador').PHP_EOL;                
            }            
            $extension = trim($extension, '()');
            $confirm = ($confirm) ? 'true' : 'false';
            $validation.= '<script type="text/javascript">$(function() {$("#'.self::$_name['id'].'").submit(function(){ ';
            //Verifico si necesita utilizar una extensión
            if($extension) {
                $validation.= 'if('.$extension.'()) { return validForm(this.name,'.$confirm.'); } else { return false; }';
            } else {
                $validation.= 'return validForm(this.name,'.$confirm.');';
            }
            $validation .= '});});</script>'; 
        } else if(!$valid && $extension) {
            $extension = trim($extension, '()');
            $confirm = ($confirm) ? 'true' : 'false';
            $validation.= '<script type="text/javascript">$(function() {$("#'.self::$_name['id'].'").submit(function(){ ';
            $validation.= "return $extension();";
            $validation .= '});});</script>'; 
            
        }
        return $validation.PHP_EOL;                
    }
    
    /**
     * Método para aplicar el foco a un input
     * @param string $field Nombre del campo: modelo.campo
     * @return string
     */
    public static function focus($field) {
        //Extraigo el id
        extract(self::_getFieldName($field));        
        return '<script text="type/javascript">$(function() { $("#'.$id.'").focus(); });</script>';
    }
    
    /**
     * Método para generar automáticamente las etiquetas <label> de los input
     * @param string $label Texto a mostrar
     * @param string $field Nombre del campo asignado
     * @param array $attrs Atributos de la etiqueta
     * @param boolean $req Indica si se muestra el campo como requerido     
     * @param string $type Nombre del tipo de input: radio, checkbox o textarea
     * @param int $range Rango mínimo utilizado en los textarea     
     * @return string 
     */
    public static function label($text, $field, $attrs=NULL, $req='', $type='text', $range=0) {
        //Extraigo el id y name
        extract(self::_getFieldName($field));
        //Verifico si tiene atributos
        if(is_array($attrs)) {
            //Reviso si esta deshabilitado
            if(!preg_match("/\bcontrol-label\b/i", $attrs['class'])) {
                $attrs['class'] = $attrs['class'].' control-label';
            }
            $attrs = Tag::getAttrs($attrs);            
        } else {            
            $attrs = 'class="control-label"';
        }        
        $label = '';
        if($text!='') {
            //Si es checkbox o radio
            if( ($type == 'checkbox') or ($type == 'radio') ) {
                $label.= "<label for=\"$id".self::$_counter."\" class=\"choice\">$text";
                self::$_counter++;
            } else {                          
                $label.= "<label for=\"$id\" $attrs>$text";
            }            
            //Verifico si es requerido        
            $label.= (preg_match("/\brequerido\b/i", $req)) ? '<span class="req">*</span>' : '';            
            $label .= "</label>";
        }                               
        return $label;        
    }
    
    /**
     * Método que devuelve el help-block de un input
     * @param type $field
     * @param type $help
     * @param type $error
     * @param type $range
     * @return string
     */
    public static function help($field, $help='', $error='',  $range=0) {
        //Extraigo el id y name
        extract(self::_getFieldName($field));      
        //Se arma el help
        $help = "<p class=\"help-block\">$help ";        
        if(preg_match("/\bshow-error\b/i", $error)) {
            $help.= "<span class=\"help-error\" id=\"err_$id\">&nbsp;</span>";
        }
        $help.= '</p>';
        if($range>0) {
            $help.= "<br /><var id=\"rangeMaxMsg{$id}\">&nbsp;&nbsp;&nbsp; Tamaño máximo: {$range}</var> caracteres.&nbsp;&nbsp;&nbsp; <em class=\"currently\">Usuados: <var id=\"rangeUsedMsg{$id}\">0</var> caracteres.</em>";
        }
        return $help;        
    }
    
    /**
     * Abre una etiqueta de formulario
     * @param string $action Lugar al que envía
     * @param string $method Método de envío
     * @param string $attrs Atrributos
     * @param boolean $valid Indica si el formulario se valida
     * @param boolean $ext Método a cargar por extensión
     * @param boolean $confirm Indica si el formulario requiere un confirmación
     * @return string
     */
    public static function open($action=null, $method='post', $attrs=null, $valid=false, $ext='', $confirm=false) {        
        $attrs = self::_getAttrsClass($attrs, 'form'); //Verifico los atributos
        $form = self::_getValidationForm($valid, $ext, $confirm); //Verifico las validaciones
        if($method=='') {
            $method= 'post';
        }        
        if(empty($action)) {
            extract(Router::get());
            $action = ($module)  ? "$module/$controller/$action/" : "$controller/$action/";            
            if($parameters) {
                $action.= join('/', $parameters).'/';                
            } 
        }
        $form.= parent::open($action, $method, $attrs);//Obtengo la etiqueta para abrir el formulario
        return $form.PHP_EOL;
    }
    
    /**
     * Abre una etiqueta de formulario tipo multipart
     * @param string $action Lugar al que envía     
     * @param string $attrs Atrributos
     * @param boolean $valid Indica si el formulario se valida
     * @param boolean $ext Indica si la validación tiene una extensión
     * @param boolean $confirm Indica si el formulario requiere un confirmación
     * @return string
     */
    public static function openMultipart($action=null, $attrs=null, $valid=false, $ext=false, $confirm=false) {
        $attrs = self::_getAttrsClass($attrs, 'form-multipart'); //Verifico los atributos
        $form = self::_getValidationForm($valid, $ext, $confirm); //Verifico las validaciones
        $form.= parent::openMultipart($action, $attrs); //Obtengo la etiqueta para abrir el formulario
        return $form.PHP_EOL;
    }    
    
    /**
     * Método para cerrar una etiqueta form
     * @return string
     */
    public static function close() {
        return parent::close().PHP_EOL;        
    }
        
    /**
     * Método que genera un input text basandose en el bootstrap de twitter
     * @param type $field Nombre del input
     * @param type $attrs Atributos del input
     * @param type $value Valor por defecto
     * @param type $label Detalle de la etiqueta label
     * @param type $help Descripción del campo
     * @param type $type tipo de campo (text, numeric, etc)
     * @return string
     */
    public static function text($field, $attrs=null, $value=null, $label='', $help='', $type='text') {
        //Tomo los nuevos atributos definidos en las clases
        $attrs = self::_getAttrsClass($attrs, $type);
        //Armo el input
        $input = self::getControls();
        if(self::$_style=='form-search') {
            $attrs['placeholder'] = $label;
        }
        //Tomo el input del form
        $input.= parent::text($field, $attrs, $value);
        //Verifico si el formato del formulario muestra el help
        if(self::$_help_block) {              
            $input.= self::help($field, $help, $attrs['class']);
        }       
        //Cierro el controls
        $input.= self::getControls();
        if(!self::$_help_block) {
            return $input.PHP_EOL;
        }
        //Verifico si tiene un label
        $label = ($label && self::$_show_label) ? self::label($label, $field, null, $attrs['class'])  : '';        
        return '<div class="control-group">'.$label.$input.'</div>'.PHP_EOL;                  
    }
    
    
    /**
     * Método que genera un campo select
     * @param type $field Nombre del input
     * @param array $data Datos a mostrar
     * @param type $attrs Atributos para el select
     * @param type $value Valor por defecto
     * @param type $label Texto a mostrar en la etiqueta label
     * @param type $help Texto a mostrar en el help-block
     * @return string
     */
    public static function select($field, $data, $attrs = NULL, $value = NULL, $label='', $help='') {        
        $attrs = self::_getAttrsClass($attrs, 'select');
        if(is_null($data)) {
            $data = array(''=>'[SELECCION]');
        }                
        $input = self::getControls();        
        $input.= parent::select($field, $data, $attrs, $value);        
        if(self::$_help_block) {           
            $input.= self::help($field, $help, $attrs['class']);
        } 
        $input.= self::getControls();
        if(!self::$_help_block) {
            return $input.PHP_EOL;
        }        
        //Verifico si tiene un label
        $label = ($label && self::$_show_label) ? self::label($label, $field, null, $attrs['class'])  : '';        
        return '<div class="control-group">'.$label.$input.'</div>'.PHP_EOL;                  
    }
    
    /**
     * Método para abrir/cerrar un fieldset
     * @staticvar boolean $i
     * @param type $text Texto a mostrar del fieldset
     * @param type $attrs
     * @return string
     */
    public static function fieldset($text='', $attrs=null){
        static $i = true;
        if($i==false) {  
            $i = true;
            return '</fieldset>';
        }        
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        $i = false;
        return "<fieldset $attrs><legend>$text</legend>";
    }        
    
    /**
     * Método para abrir y cerrar un div controls en los input
     * @staticvar boolean $i
     * @return string
     */
    public static function getControls() {
        if(self::$_style=='form-horizontal') {
            static $i = true;
            if($i==false) {  
                $i = true;
                return '</div>'.PHP_EOL;
            }                
            $i = false;
            return '<div class="controls">'.PHP_EOL;                             
        }
        return null;
    }
    
    /**
     * Crea un botón
     *
     * @param string $text Texto del botón
     * @param array $attrs Atributos de campo (opcional)
     * @return string
     */
    public static function button2($text, $attrs = NULL, $value = NULL, $icon = Null) {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        $btn = '';
        if($icon) {
            $btn.='<i class="icon-expand icon-'.$icon.'"></i>';
        }
        $btn.= $text;
        return "<button $attrs>$btn</button>";        
    }
    
    /**
     * Crea un botón
     *
     * @param string $text Texto del botón
     * @param array $attrs Atributos de campo (opcional)
     * @return string
     */
    public static function submit2($text, $attrs = NULL, $icon = Null) {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        $btn = '';
        if($icon) {
            $btn.='<i class="icon-expand icon-'.$icon.'"></i>';
        }
        $btn.= $text;
        return "<button type=\"submit\" $attrs>$btn</button>";        
    }
}
