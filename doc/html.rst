Utilidades Html
====================

.. contents:: Otra de los helper interesantes es la creación de los link y link tipo botón, que se comportarán acorde al estado de las peticiones de la app, si en ajax o normal.

Creando un link básico
--------------------
Para crear un link básico hacemos uso del helper DwHtml.

.. code-block:: php

    <?php

    /**
     * Método para genera un link con ícono
     * @param string $action Url a enlazar
     * @param type $text Texto a mostrar
     * @param type $attrs Array con atributos adicionales
     * @param type $icon Si se muestra un ícono se especifica
     * @param type $loadAjax Si el link carga con ajax o no. Por defecto toma el comportamiento de la app.
     * @return type
     */
    link ($action, $text, $attrs = NULL, $icon='', $loadAjax = APP_AJAX)

    //Ejemplo Básico:
    <?php echo DwHtml::link('sistema/usuario', 'Usuarios') ?>

    //Ejemplo con ícono
    <?php echo DwHtml::link('sistema/usuario', 'Usuarios', NULL, 'icon-user'); ?>

    //Ejemplo con ícono
    <?php echo DwHtml::link('sistema/usuario', 'Usuarios', NULL, 'icon-user'); ?>

    //Si la app está en ajax pero aún así no queremos que cargue con ajax el link
    <?php echo DwHtml::link('sistema/usuario', 'Usuarios', array('class'=>'no-ajax no-spinner'); ?>

    //Si se quiere cambiar el contenedor
    <?php echo DwHtml::link('sistema/usuario', 'Usuarios', array('data-div'=>'contenedor'); ?>

    //Si no se quiere cambiar la url en el navegador (Aplica cuando se trabaja con ajax)
    <?php echo DwHtml::link('sistema/usuario', 'Usuarios', array('class'=>'dw-no-change'); ?>

    //Si se utilizan callback (Aplica cuando se trabaja con ajax)
    <?php echo DwHtml::link('sistema/usuario', 'Usuarios', array('before-load'=>'funcion1();', 'after-load'=>'funcion2();'); ?>

Creando link tipo button
--------------------
El comportamiento es igual al ``DwHtml::link`` sólo que este lo genera tipo ``button``:

.. code-block:: php

    <?php

    /**
     * Método para genera un link tipo botón con ícono
     * @param string $action Url a enlazar
     * @param type $text Texto a mostrar
     * @param type $attrs Array con atributos adicionales
     * @param type $icon Si se muestra un ícono se especifica
     * @param type $loadAjax Si el link carga con ajax o no. Por defecto toma el comportamiento de la app.
     * @return type
     */
    button($action, $text = NULL, $attrs = NULL, $icon='', $loadAjax = APP_AJAX)

    //Ejemplo Básico:
    <?php echo DwHtml::button('sistema/usuario', 'Usuarios') ?>

    //Ejemplo con ícono
    <?php echo DwHtml::button('sistema/usuario', 'Usuarios', NULL, 'icon-user'); ?>

    //Ejemplo con ícono
    <?php echo DwHtml::button('sistema/usuario', 'Usuarios', NULL, 'icon-user'); ?>

    //Si la app está en ajax pero aún así no queremos que cargue con ajax el link
    <?php echo DwHtml::button('sistema/usuario', 'Usuarios', array('class'=>'no-ajax no-spinner'); ?>

    //Si se quiere cambiar el contenedor (Aplica cuando se trabaja con ajax)
    <?php echo DwHtml::button('sistema/usuario', 'Usuarios', array('data-div'=>'contenedor'); ?>

    //Si no se quiere cambiar la url en el navegador (Aplica cuando se trabaja con ajax)
    <?php echo DwHtml::button('sistema/usuario', 'Usuarios', array('class'=>'dw-no-change'); ?>

    //Si se utilizan callback (Aplica cuando se trabaja con ajax)
    <?php echo DwHtml::button('sistema/usuario', 'Usuarios', array('before-load'=>'funcion1();', 'after-load'=>'funcion2();'); ?>

**Nota:** Si se ha especificado un ícono y está visualizado en un dispositivo móvil, este se ocultará.

Creando link para los datagrid
--------------------
El comportamiento es similar a los anteriores y se utiliza en los datagrid

.. code-block:: php

    <?php

    /**
     * Método para genera un link tipo botón con ícono para las acciones del datagrid
     * @param string $title Título a mostrar (Se usa el toltip de bootstrap)
     * @param string $action Url a enlazar
     * @param type $attrs Array con atributos adicionales
     * @param type $type Tipo de botón para el color (info, success, warning, danger, inverse)
     * @param type $icon Icono a mostrar
     * @param type $loadAjax Si el link carga con ajax o no. Por defecto toma el comportamiento de la app.
     * @return type
     */
    buttonTable($title, $action, $attrs = NULL, $type='info', $icon='search', $loadAjax = APP_AJAX)

    //Ejemplo Básico:
    <?= DwHtml::buttonTable('Modificar sucursal', "config/sucursal/editar/5/", NULL, 'info', 'edit'); ?>

    //Ejemplo con confirmación
    <?= DwHtml::buttonTable('Eliminar sucursal', "config/sucursal/eliminar/5/", array('class'=>'js-confirm', 'confirm-title'=>'Eliminar sucursal', 'confirm-body'=>'Está seguro de eliminar esta sucursal? <br />Recuerda que esta operación no se puede reversar.'), 'danger', 'ban-circle'); ?>
