<?php
/**
 * Dailyscript - Web | App | media
 *
 * Extension para el manejo de botones de formularios
 *
 * @category    Helpers
 * @author      Iván D. Meléndez
 * @package     Helpers
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class DwButton {
    
    /**
     * Contador de mensajes
     * @var int
     */
    protected static $_counter = 1;
    
    /**
     * Método para crear un botón
     * @param type $title Título a mostrar
     * @param type $icon Icono a mostrar
     * @param type $attrs Atributos adicionales
     * @param type $text Texto a mostrar
     * @return type
     */
    public static function save($title='Guardar registro', $icon='save', $attrs=NULL, $text='guardar') {
        if (is_array($attrs) OR empty($attrs)) {
            $attrs['class'] = (empty($attrs['class'])) ? 'btn-success' : 'btn-success '.$attrs['class'];
            $attrs['title'] = $title;            
        }        
        return self::showButton($icon, $attrs, $text, 'submit');
    }
    
    /**
     * Método para resetear un formulario
     * @param type $form ID del formulario
     * @param type $formUpdate Indica si el formulario es de modificación o creación
     * @param type $icon Icono a mostrar
     * @return type
     */
    public static function reset($form='formulario', $formUpdate=FALSE, $icon='undo') {
        $title = (!$formUpdate) ? 'Limpiar formulario' : 'Retomar valores por defecto';
        $attrs = array();
        $attrs['class'] = 'btn-info';
        $attrs['title'] = $title;            
        $attrs['onclick'] = "document.getElementById('$form').reset();";
        return self::showButton($icon, $attrs, 'limpiar', 'button');
    }
    
    /**
     * Método para cancelar un formulario
     * @param type $redir Página a redirigir al presionar el botón
     * @param type $title Título a mostrar
     * @param type $icon Icono a mostrar
     * @return type
     */
    public static function cancel($redir=NULL, $title='', $icon='ban-circle') {
        $attrs = array();
        $attrs['class'] = 'btn-danger';
        $attrs['title'] = empty($title) ? 'Cancelar operación' : $title;
        if(empty($redir) && APP_AJAX) {
            $attrs['class'].= ' btn-back';
            return self::showButton($icon, $attrs, 'cancelar', 'button');
        } else {
            return DwHtml::button($redir, 'CANCELAR', $attrs, $icon); 
        }
    }
    
    /**
     * Método para crear un botón para regresar a la página anterior
     * @param type $redir Página a redirigir al presionar el botón
     * @param type $title Título a mostrar
     * @param type $icon Icono a mostrar
     * @return type
     */
    public static function back($redir=NULL, $title='', $icon='backward') {
        $attrs = array();
        $attrs['class'] = 'btn-info';
        $attrs['title'] = empty($title) ? 'Regresar' : $title;
        if(empty($redir) && APP_AJAX) {
            $attrs['class'].= ' btn-back';
            return self::showButton($icon, $attrs, 'regresar', 'button');
        } else {
            return DwHtml::button($redir, 'REGRESAR', $attrs, $icon); 
        }
    }

    /**
     * Método para crear un botón para envío de formularios
     * @param type $title Título a mostrar
     * @param type $icon Icono a mostrar
     * @param type $attrs Atributos adicionales
     * @param type $text Texto a mostrar
     * @return type
     */
    public static function submit($title='Guardar registro', $icon='save', $attrs=NULL, $text='guardar') {        
        return self::save($title, $icon, $attrs, $text);
    }

    
    /**
     * Método que se encarga de crear el botón
     * @param type $icon
     * @param type $attrs
     * @param type $text
     * @param type $type
     * @return type
     */
    public static function showButton($icon='', $attrs = array(), $text='', $type='button') {        
        $text = strtoupper($text);
        $attrs['class'] = 'btn '.$attrs['class'];
        if(!preg_match("/\bdw-text-bold\b/i", $attrs['class'])) {
            $attrs['class'] = $attrs['class'].' dw-text-bold';
        }        
        $attrs = Tag::getAttrs($attrs);
        $text = (!empty($text) && $icon) ? '<span class="hidden-phone">'.strtoupper($text).'</span>' : strtoupper($text);
        if($icon) {
            $text = '<i class="btn-icon-only icon-'.$icon.'"></i> '.$text;
        }
        return "<button type=\"$type\" $attrs>$text</button>";           
    }
}
