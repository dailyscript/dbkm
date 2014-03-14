<?php
/**
 * Dailyscript - Web | App | media
 *
 * Extension para el manejo de formularios que hereda atributos de la clase Form
 *
 * @category    Views
 * @package     Helpers
 * @author      Iván D. Meléndez (ivan.melendez@dailyscript.com.co)
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
            $formAjax = (APP_AJAX && Session::get('app_ajax')) ? TRUE : FALSE;
            if(isset($attrs['class'])) {
                if(preg_match("/\bno-ajax\b/i", $attrs['class'])){
                    $formAjax = false;
                }
                //Verifico si está definida la clase dw-form
                if(!preg_match("/\bdw-form\b/i", $attrs['class'])) {
                    $attrs['class'] = 'dw-form '.$attrs['class'];
                }
                //Verifico si está definida la clase para ajax, pero si no se encuentra el aplicativo para ajax
                if(preg_match("/\bjs-remote\b/i", $attrs['class']) && !$formAjax) {
                    $formAjax = TRUE;
                }
                //Verifico si el aplicativo está con ajax
                if($formAjax) {
                    //Verifico si está definida la clase para ajax
                    if(!preg_match("/\bjs-remote\b/i", $attrs['class'])) {
                        $attrs['class'] = 'js-remote '.$attrs['class'];
                    }
                    if(!preg_match("/\bdw-no-validate\b/i", $attrs['class'])) {
                        $attrs['class'] = 'dw-validate '.$attrs['class'];
                    }
                }
            } else {
                //Asigno que pertenece a la clase dw-form y si utiliza ajax
                $attrs['class'] = ($formAjax) ? 'dw-form dw-validate js-remote' : 'dw-form';
            }

            if($formAjax && !isset($attrs['data-to'])) { //Si es un form con ajax verifico si está definido el data-div
                $attrs['data-to'] = 'dw-shell-content';
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
                if(preg_match("/\binput-money\b/i", $attrs['class'])) {
                    //@see var.js
                    $attrs['onkeyup'] = 'jsMiles(this,this.value.charAt(this.value.length-1))';
                }
            } else {
                //Si no está definida las clases las asigno según el tipo
                $attrs['class'] = ( ($type != 'checkbox') && ($type != 'radio') ) ? "field $type input-full " : "field $type ";
            }
            //Verifico si se utiliza la mayúscula solo para los text y textarea
            if( ($type=='text') && ($type=='textarea') ) {
                if( !preg_match("/\binput-lower\b/i", $attrs['class']) or preg_match("/\binput-upper\b/i", $attrs['class']) )  {
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
            //Verifico si es requerido
            if(preg_match("/\binput-required\b/i", $attrs['class'])) {
                $attrs['required'] = 'required';
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
    protected static function _getValidationForm() {
        return '<script type="text/javascript"> $(function() { $("#'.self::$_name['id'].'").on("submit", function(){ este = $(this); before_send = este.attr("before-send"); after_send = este.attr("after-send"); confirmation = este.hasClass("dw-confirm") ? true : false; val = true; if(before_send!=undefined) { try { val = eval(before_send); } catch(e) { } } if(!val) { return false; } val = validForm(este.attr("name"), confirmation); if(!val) { return false; } if(after_send!=undefined) { try { eval(after_send); } catch(e) { } } }); }); </script>'.PHP_EOL;
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
        if(!empty($field)) {
            extract(self::_getFieldName($field));
        }
        //Verifico si tiene atributos
        if(is_array($attrs)) {
            //Reviso si esta deshabilitado
            if(!preg_match("/\bcontrol-label\b/i", $attrs['class'])) {
                $attrs['class'] = $attrs['class'].' control-label';
            }
        } else {
            $attrs = array('class'=>'control-label');
        }
        $label = '';
        if($text!='') {
            $id = (empty($id)) ? NULL : $id; //Por si el field=NULL
            //Si es checkbox o radio
            if( ($type == 'checkbox') or ($type == 'radio') ) {
                $type = (self::$_style != 'form-horizontal' OR preg_match("/\binline\b/i", $req) ) ? $type.' inline' : $type;
                $id = str_replace(array('[', ']'), '_', $id);
                $label.= "<label for=\"$id".self::$_counter."\" class=\"$type\">$text";
                self::$_counter++;
            } else {
                $attrs = Tag::getAttrs($attrs);
                $label.= "<label for=\"$id\" $attrs>$text";
            }
            //Verifico si es requerido
            $label.= (preg_match("/\binput-required\b/i", $req)) ? '<span class="req">*</span>' : '';
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
        if(preg_match("/\bshow-error\b/i", $error) OR preg_match("/\binput-required\b/i", $error) OR preg_match("/\binput-list\b/i", $error)) {
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
     * @param boolean $validate Indica si el formulario se valida
     * @return string
     */
    public static function open($action=null, $method='post', $attrs=null, $validate=false) {
        $form = '';
        $attrs = self::_getAttrsClass($attrs, 'form'); //Verifico los atributos
        //Verifico si se valida
        if( (preg_match("/\bdw-validate\b/i", $attrs['class']) OR $validate) && !preg_match("/\bjs-remote\b/i", $attrs['class']) ) {           
            $form.= self::_getValidationForm();
        }
        if($method=='') {
            $method= 'post';
        }
        $method2 = $method;
        if(empty($action)) {
            extract(Router::get());
            $action = ($module)  ? "$module/$controller/$action/" : "$controller/$action/";
            if($parameters) {
                $action.= join('/', $parameters).'/';
            }
        }
        $form.= parent::open($action, $method2, $attrs);//Obtengo la etiqueta para abrir el formulario
        return $form.PHP_EOL;
    }

    /**
     * Abre una etiqueta de formulario tipo multipart
     * @param string $action Lugar al que envía
     * @param string $attrs Atrributos
     * @param boolean $confirm Indica si el formulario requiere un confirmación
     * @return string
     */
    public static function openMultipart($action=null, $attrs=null, $validate=FALSE) {
        $form = '';
        $attrs = self::_getAttrsClass($attrs, 'form-multipart'); //Verifico los atributos
        //Verifico si se valida
        if(preg_match("/\bdw-validate\b/i", $attrs['class']) OR $validate) {
            $form.= self::_getValidationForm();
        }
        if(empty($action)) {
            extract(Router::get());
            $action = ($module)  ? "$module/$controller/$action/" : "$controller/$action/";
            if($parameters) {
                $action.= join('/', $parameters).'/';
            }
        }
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
        if(self::$_style=='form-search' OR self::$_style=='form-inline') {
            $attrs['placeholder'] = $label;
        }
        //Armo el input del form
        $input.= parent::text($field, $attrs, $value, $type);
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
     * Método para crear un input tipo textarea
     * @param string $field Nombre del input
     * @param array $attrs Atributos del input
     * @param string $value Valor del input
     * @param string $label Texto a mostrar en la etiqueta <label>
     * @param string $help Texto a mostrar como descripcion
     * @return string
     */
    public static function textarea($field, $attrs=null, $value=null, $label='', $help='') {
        //Tomo los nuevos atributos definidos en las clases
        $attrs = self::_getAttrsClass($attrs, 'textarea');
        //Armo el input
        $input = self::getControls();
        if(self::$_style=='form-search' OR self::$_style=='form-inline') {
            $attrs['placeholder'] = $label;
        }

        //Tomo el input del form
        $input.= parent::textarea($field, $attrs, $value);
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
        if(empty($data)) {
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
     * Método para crear un select a partir de un array de objetos de ActiveRecord. <br />
     * Permite mostrar varios valores por fila y valor con slug
     * @param string $field Nombre del select: modelo.campo
     * @param string, array $show Campo a mostrar de la consulta.  Es posible mostrar mas de un campo con array('campo1', 'campo2')
     * @param object $data Array de objetos. Puede dejarse nulo y carga automáticamente la data o indicar el modelo, método y parámetros
     * @param string|array $blank Texto a mostrar en blanco
     * @param array $attrs Atributos del input
     * @param string $value Valor del select
     * @param string $label Texto a mostrar en la etiqueta <label>
     * @param boolean $help Texto de descripción del campo
     * @return string
     */
    public static function dbSelect($field, $show=null, $data=null, $blank='Selección', $attrs=null, $value=null, $label='', $help='') {

        $attrs = self::_getAttrsClass($attrs, 'select');
        if(empty($data)) {
            $data = array(''=>'Selección');
        }

        if(empty($blank)) {
            $blank = 'Selección';
        }

        $attrs2 = $attrs;

        $input = self::getControls();

        if(is_array($attrs)) { //Cargo los atributos
            $attrs = Tag::getAttrs($attrs);
        }
        extract(self::getFieldData($field, $value), EXTR_OVERWRITE);

        $options = '';

        //Muestro el blank
        if(!empty($blank)) {
            if(is_array($blank)) {
                $options_key = @array_shift(array_keys($blank));
                $options = '<option value="'.$options_key.'">' . htmlspecialchars($blank[$options_key], ENT_COMPAT, APP_CHARSET) . '</option>';
            } else {
                $options = '<option value="">' . htmlspecialchars($blank, ENT_COMPAT, APP_CHARSET) . '</option>';
            }
        }
        //Verifico si existe una data
        if($data === null){
            //por defecto el modelo de modelo(_id)
            $model_asoc = explode('.', $field, 2);
            $model_asoc = substr(end($model_asoc), 0, -3);//se elimina el _id
            $model_name = $model_asoc; //Tomo el nombre del modelo
            $model_asoc = Load::model($model_asoc); //Cargo el modelo
            $pk = $model_asoc->primary_key[0];//Tomo la llave primaria
            if(!$show){
                $show = $model_asoc->non_primary[0]; //por defecto el primer campo no pk
            }
            $data = $model_asoc->find("columns: $pk,$show","order: $show asc");//mejor usar array
        } else if(isset($data[0]) && is_string($data[0])) { //Verifico si ha enviado el modelo, método y/o parámetros
            $model_name = explode('/', $data[0]); //Tomo el nombre del modelo
            $model_name = end($model_name);
            $model_asoc = Load::model($data[0]);//Cargo el modelo
            $pk = $model_asoc->primary_key[0];//Tomo la llave primaria
            // Verifica si existe el argumento
            if(isset($data[2]) && isset($data[3])) {
                $data = $model_asoc->$data[1]($data[2],$data[3]);
            } else if(isset($data[2])) {
                $data = $model_asoc->$data[1]($data[2]);
            } else {
                $data = $model_asoc->$data[1]();
            }
        } else { //Si ha enviado una data determino la llave primaria
            $model_asoc = explode('.', $field, 2);
            $model_name = $model_asoc[0];
            $tam = strlen(end($model_asoc));
            $pk = substr(end($model_asoc), $tam-2, $tam);//se utiliza el id
        }
        //Recorro la data
        foreach($data as $p) {
            //Muestro el valor del id como show value, a menos que tenga un {nombre_modelo}_slug
            $slug = $model_name."_slug";
            if(is_array($show) && in_array($slug, $show)) {
                $show_value = (isset($p->$slug)) ? $p->$slug : $p->$pk; //Verifico si existe un campo llamado {nombre_modelo}_slug, lo tomo sino la pk
            } else {
                $show_value = $p->$pk;
            }
            $options .= "<option value=\"$show_value\"";
            if($show_value == $value) {
                $options .= ' selected="selected"';
            }
            if(is_array($show)) { //Verifico si se muestran varios campos
                $opt = '';
                $i=0;
                foreach($show as $item) {
                    if($show[$i] != $slug) {
                        if(isset($p->{$show[$i]})) {
                            $opt.= htmlspecialchars($p->$item, ENT_COMPAT, APP_CHARSET). ' | ';
                        } else {
                            $opt.= htmlspecialchars($show[$i], ENT_COMPAT, APP_CHARSET). ' | ';
                        }
                    }
                    $i++;
                }
                $options .= '>' . trim($opt, ' | '). '</option>';
            } else {
                $options .= '>' . htmlspecialchars($p->$show, ENT_COMPAT, APP_CHARSET). '</option>';
            }
        }
        $input.=  "<select id=\"$id\" name=\"$name\" $attrs>$options</select>";
        if(self::$_help_block) {
            $input.= self::help($field, $help, $attrs2['class']);
        }
        $input.= self::getControls();
        if(!self::$_help_block) {
            return $input.PHP_EOL;
        }
        //Verifico si tiene un label
        $label = ($label && self::$_show_label) ? self::label($label, $field, null, $attrs2['class'])  : '';
        return '<div class="control-group">'.$label.$input.'</div>'.PHP_EOL;

    }


    /**
     * Método que genera un input tipo password
     * @param type $field Nombre del input
     * @param type $attrs Atributos del input
     * @param type $value Valor por defecto
     * @param type $label Detalle de la etiqueta label
     * @param type $help Descripción del campo
     * @param type $type tipo de campo (text, numeric, etc)
     * @return string
     */
    public static function pass($field, $attrs=null, $value=null, $label='', $help='') {
        //Tomo los nuevos atributos definidos en las clases
        $attrs = self::_getAttrsClass($attrs, 'text');
        //Armo el input
        $input = self::getControls();
        if(self::$_style=='form-search' OR self::$_style=='form-inline') {
            $attrs['placeholder'] = $label;
        }
        //Armo el input del form
        $input.= parent::pass($field, $attrs, $value);
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
     * Método para crear un input tipo file
     * @param string $field Nombre del input
     * @param array $attrs Atributos del input
     * @param string $label Texto a mostrar en la etiqueta <label>
     * @param boolean $req Indica si el campo es requerido
     * @param boolean $err Indica si el campo muestra errores
     * @return string
     */
    public static function file($field, $attrs=null, $label='', $help='') {

        //Tomo los nuevos atributos definidos en las clases
        $attrs = self::_getAttrsClass($attrs, 'file');
        //Armo el input
        $input = self::getControls();
        if(self::$_style=='form-search' OR self::$_style=='form-inline') {
            $attrs['placeholder'] = $label;
        }
        if(!APP_AJAX) {
            //Armo el input del form
            $input.= parent::file($field, $attrs);
        } else {
            if (is_array($attrs)) {
                $attrs2 = Tag::getAttrs($attrs);
            }
            // Obtiene name y id, y los carga en el scope
            extract(self::getFieldData($field, false), EXTR_OVERWRITE);
            $input.="<input id=\"$id\" name=\"$name\" type=\"file\" $attrs2/>";
        }
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
     * Método que genera un input text basandose en el bootstrap de twitter
     * @param type $field Nombre del input
     * @param type $attrs Atributos del input
     * @param type $value Valor por defecto
     * @param type $label Detalle de la etiqueta label
     * @param type $help Descripción del campo
     * @param type $type tipo de campo (text, numeric, etc)
     * @return string
     */
    public static function date($field, $attrs=null, $value=null, $label='', $help='') {
        //Tomo los nuevos atributos definidos en las clases
        $attrs = self::_getAttrsClass($attrs, 'text');
        //Armo el input
        $input = self::getControls();
        if(self::$_style=='form-search' OR self::$_style=='form-inline') {
            $attrs['placeholder'] = $label;
        }
        $input.= '<div class="input-append date">';
        //Armo el input del form
        if(!DESKTOP) {
            $input.= parent::text($field, $attrs, $value, 'date');
        } else {
            $attrs['class'] = 'js-datepicker '.$attrs['class'];
            $input.= parent::text($field, $attrs, $value);
        }
        $input.= '<span class="add-on"><i class="icon-calendar"></i></span>';
        $input.= '</div>';
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
     * Método para generar un select con un único registro
     * @param string $field Nombre del campo
     * @param string | array $value Valor del campo
     * @param array $attrs Atributos para el select
     * @param string $label Nombre del label
     * @param string $help
     * @return string
     */
    public static function oneSelect($field, $value, $attrs=NULL, $label='', $help='') {
        $data = is_array($value) ? $value : array($value=>$value);
        $value = is_array($value) ? @array_shift(array_keys($value)) : $value;
        $input = self::select($field, $data, $attrs, $value, $label, $help);
        return $input.PHP_EOL;
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
     * Método para crear un legend
     * @param type $text
     * @param type $attrs
     * @return type
     */
    public static function legend($text, $attrs = NULL) {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        return "<legend $attrs>$text</legend>";
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

    /**
     * Método para generar un hidden con el id y otro con el id_key
     * @param string $field Modelo.campo del hidden
     * @param string $value Valor del campo
     * @return string
     */
    public static function hiddenKey($field, $value, $textKey='form_key') {
        $input = parent::hidden($field, array('class'=>''), $value).PHP_EOL;
        extract(self::_getFieldName($field));
        str_replace('[',  '', $name, $checked);
        if($checked) {
            $field = str_replace(array("[","]"), '_', $name)."key";
        } else {
            $field = "{$field}_key";
        }
        $value = DwSecurity::getKey($value, $textKey);
        $input.= parent::hidden($field, null, $value).PHP_EOL;
        return $input;
    }

    /**
     * Método que genera un input type="number"
     * @param type $field Nombre del input
     * @param type $attrs Atributos del input
     * @param type $value Valor por defecto
     * @param type $label Detalle de la etiqueta label
     * @param type $help Descripción del campo
     * @return string
     */
    public static function number($field, $attrs=null, $value=null, $label='', $help='') {
        return self::text($field, $attrs, $value, $label, $help, 'number');
    }

    /**
     * Método que genera un input type="tel"
     * @param type $field Nombre del input
     * @param type $attrs Atributos del input
     * @param type $value Valor por defecto
     * @param type $label Detalle de la etiqueta label
     * @param type $help Descripción del campo
     * @return string
     */
    public static function tel($field, $attrs=null, $value=null, $label='', $help='') {
        return self::text($field, $attrs, $value, $label, $help, 'tel');
    }

    /**
     * Método que genera un input type="email"
     * @param type $field Nombre del input
     * @param type $attrs Atributos del input
     * @param type $value Valor por defecto
     * @param type $label Detalle de la etiqueta label
     * @param type $help Descripción del campo
     * @return string
     */
    public static function email($field, $attrs=null, $value=null, $label='', $help='') {
        return self::text($field, $attrs, $value, $label, $help, 'email');
    }

    /**
     * Método que genera un input type="url"
     * @param type $field Nombre del input
     * @param type $attrs Atributos del input
     * @param type $value Valor por defecto
     * @param type $label Detalle de la etiqueta label
     * @param type $help Descripción del campo
     * @return string
     */
    public static function url($field, $attrs=null, $value=null, $label='', $help='') {
        return self::text($field, $attrs, $value, $label, $help, 'url');
    }

    /**
     * Método que genera un input type="radio"
     * @param type $field Nombre del input
     * @param string $radioValue Valor del radio
     * @param type $attrs Atributos del input, se puede especificar array('class'=>'inline') para mostrar en línea
     * @param type $checked Indica si está seleccionado o no
     * @param type $label Detalle de la etiqueta label
     * @return string
     */
    public static function radio($field, $radioValue, $attrs = NULL, $checked = NULL, $label='') {
        //Tomo los nuevos atributos definidos en las clases
        $attrs = self::_getAttrsClass($attrs, 'radio');
        $input = self::label($label, $field, null, $attrs['class'], 'radio');
        $input = str_replace('</label>', '', $input); //Quito el cierre de la etiqueta label
        $input = str_replace($label, '', $input); //Quito el texto del label para ponerlo al final
        //Armo el input del form
        $input.= parent::radio($field, $radioValue, $attrs, $checked);
        $input.= $label;
        $input.= '</label>';//Cierro el label
        return $input.PHP_EOL;
    }

    /**
     * Método que genera un input type="checkbox"
     * @param type $field Nombre del input
     * @param string $checkValue Valor del checkbox
     * @param type $attrs Atributos del input, se puede especificar array('class'=>'inline') para mostrar en línea
     * @param type $checked Indica si está seleccionado o no
     * @param type $label Detalle de la etiqueta label
     * @return string
     */
    public static function check2($field, $checkValue, $attrs = NULL, $checked = NULL, $label='') {
        //Tomo los nuevos atributos definidos en las clases
        $attrs = self::_getAttrsClass($attrs, 'checkbox');
        $input = self::label($label, $field, null, $attrs['class'], 'checkbox');
        $input = str_replace('</label>', '', $input); //Quito el cierre de la etiqueta label
        $input = str_replace($label, '', $input); //Quito el texto del label para ponerlo al final
        //Armo el input del form

        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        // Obtiene name y id para el campo y los carga en el scope
        extract(self::getFieldDataCheck($field, $checkValue, $checked), EXTR_OVERWRITE);
        $name = str_replace("[]", '', $name);
        $id = str_replace(array('[', ']'), '_', $id);
        if ($checked) {
            $checked = 'checked="checked"';
        }
        self::$_counter--;//Para que tome el contador del label
        $input.= "<input id=\"$id".self::$_counter."\" name=\"{$name}[]\" type=\"checkbox\" value=\"$checkValue\" $attrs $checked/>";
        self::$_counter++;//Para que siga
        $input.= $label;
        $input.= '</label>';//Cierro el label
        return $input.PHP_EOL;
    }

    /**
     * Método para generar un token en los formularios
     */
    public static function token() {
        $h = date("G")>12 ? 1 : 0;
        $time = uniqid().mktime($h, 0, 0, date("m"), date("d"), date("Y"));
        $key = sha1($time);
        Session::set('rsa32_key',$key);
        return self::hidden('rsa32_key', NULL, $key);
    }

    /**
     * Devuelve el resultado del token almacenado en sesion con la enviada en el form
     * @return boolean
     */
    public static function isValidToken() {
        $key = Session::get('rsa32_key');
        if( (!is_null($key) ) && ($key === Input::post('rsa32_key')) ) {
            return true;
        } else {
            return false;
        }
    }
}
