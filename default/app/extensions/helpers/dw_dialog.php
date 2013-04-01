<?php
/**
 * Dailyscript - Web | App | media
 *
 * Extension para el manejo de dialogos
 *
 * @category    Helpers
 * @author      Iván D. Meléndez
 * @package     Helpers
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

class DwDialog {

    /**
     * Contador de mensajes
     * @var int
     */
    protected static $_counter = 1;

    /**
     * Método para generar un mensaje de alerta, párametros que puede recibir: "icon: icono", "title: ", "subtext: ", "name: ", "autoOpen: "
     * @param type $text
     * @param type $params
     * @return type
     */
    public static function alert($text, $params='') {
        //Extraigo los parametros
        $params = Util::getParams(func_get_args());
        $icon = (isset($params['icon'])) ? $params['icon'] : 'icon-exclamation-sign';
        $title = isset($params['title']) ? '<i class="'.$icon.'" style="padding-right:5px; margin-top:5px;"></i>'.$params['title'] : null;
        $subtext = isset($params['subtext']) ? "<p style='margin-top: 10px'>{$params['subtext']}</p>" : null;
        $name = isset($params['name']) ? trim($params['name'],'()') : "dwModal".rand(10, 5000);
        $autoOpen = (isset($params['autoOpen'])) ? true : false;

        $modal = '<div class="modal hide" id="'.$name.'">';
            $modal.= '<div class="modal-header">';
                $modal.= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
                $modal.= ($title) ? "<h3>$title</h3>" : '';
            $modal.= '</div>';
            $modal.= "<div class=\"modal-body\">$text $subtext</div>";
            $modal.= '<div class="modal-footer">';
                $modal.= '<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Aceptar</button>';
            $modal.= '</div>';
        $modal.= '</div>';

        $modal.= '<script type="text/javascript">';
        $modal.= "function $name() { $('#$name').modal('show'); }; ";
        if($autoOpen) {
            $modal.='$(function(){ '.$name.'(); });';
        }
        $modal.= "$('#$name').on('shown', function () { $('.btn-primary', '#$name').focus(); });";
        $modal.= '</script>';
        return $modal.PHP_EOL;
    }
}
