Datagrid
====================
.. contents:: El ``dbkm`` posee un plugin de jQuery adaptado para kumbiaphp y de uso básico para datagrid responsivos o adaptables a cualquier tipo de pantalla.

Modo básico
--------------------
Podemos especificar un modo básico de datagrid responsivo.  Para este caso armamos la siguiente vista:

.. code-block:: php

    <?php View::notify(); ?>

    <script type="text/javascript">
        $(function() {
            $('table').dwGrid({
                col_collapse: false //Se define que ninguna columna se colapsará
            });
        });
    </script>

    <div class="container-fluid dw-shell-view">

        <?php View::process($page_module, $page_title, $set_title); ?>

        <!-- Contenedor para los botones superiores -->
        <div class="btn-toolbar btn-toolbar-top">
            <div class="btn-actions">
                <?php echo DwHtml::button("config/servicio/agregar/", 'agregar', array('class'=>'btn-success'), 'check', APP_AJAX); ?>
            </div>
        </div>

        <div class="dw-overflow">

            <!-- Se debe indicar la clase "tabla-responsive" -->
            <table class="table table-bordered table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>NUM</th>
                        <th>SERVICIO</th>
                        <th class="btn-actions no-responsive" style="width: 100px;">ACCIONES</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

Con lo anterior tendremos la siguiente vista:

.. figure:: img/dwgrid_1.png

   Vista móvil

.. figure:: img/dwgrid_2.png

   Vista desktop


Ocultando columnas
--------------------
Para ocultar las columnas tan solo se indica en las cabeceras la clase ``col-collapsed`` de las mismas.  Para este caso armamos la siguiente vista:

.. code-block:: php

    <?php View::notify(); ?>

    <script type="text/javascript">
        $(function() {
            $('table').dwGrid();
        });
    </script>

    <div class="container-fluid dw-shell-view">

        <?php View::process($page_module, $page_title, $set_title); ?>

        <!-- Contenedor para los botones superiores -->
        <div class="btn-toolbar btn-toolbar-top">
            <div class="btn-actions">
                <?php echo DwHtml::button("sistema/usuario/agregar/", 'agregar', array('class'=>'btn-success'), 'check', APP_AJAX); ?>
            </div>
        </div>

        <div class="dw-overflow">

            <!-- Se debe indicar la clase "tabla-responsive" -->
            <table class="table table-bordered table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>NUM</th>
                        <th class="col-collapse">NOMBRE</th>
                        <th class="col-collapse">APELLIDO</th>
                        <th class="col-collapse">EMAIL</th>
                        <th class="col-collapse">PERFIL</th>
                        <th class="col-collapse">ESTADO</th>
                        <th class="no-responsive btn-actions" style="width: 150px;">ACCIONES</th>
                    </tr>
                </thead>
            </table>

        </div>

    </div>

Con lo anterior tendremos la siguiente vista:

.. figure:: img/dwgrid_3.png

   Vista desktop


Vista con formulario de búsqueda
--------------------
Podemos especificar un modo con formulario de búsqueda según las columnas definidas en la cabecera con el atributo ``data-search="campo"`` donde ``campo`` será la columna de búsqueda en la base de datos.  Para este caso armamos la siguiente vista:

.. code-block:: php

    <?php View::notify(); ?>

    <script type="text/javascript">
        $(function() {
            $('table').dwGrid({
                form_search: true, //Se define que incluya el formulario de búsqueda
                form_action: '<?php echo PUBLIC_PATH; ?>sistema/usuario/buscar/', //Se define a donde envía los datos el formulario
            });
        });
    </script>

    <div class="container-fluid dw-shell-view">

        <?php View::process($page_module, $page_title, $set_title); ?>

        <!-- Contenedor para los botones superiores -->
        <div class="btn-toolbar btn-toolbar-top">
            <div class="btn-actions">
                <?php echo DwHtml::button("sistema/usuario/agregar/", 'agregar', array('class'=>'btn-success'), 'check', APP_AJAX); ?>
            </div>
        </div>

        <div class="dw-overflow">

            <!-- Se debe indicar la clase "tabla-responsive" -->
            <table class="table table-bordered table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>NUM</th>
                        <th class="col-collapse" data-search="login">LOGIN</th>
                        <th class="col-collapse" data-search="nombre">NOMBRE</th>
                        <th class="col-collapse" data-search="apellido">APELLIDO</th>
                        <th class="col-collapse" data-search="email">EMAIL</th>
                        <th class="col-collapse" data-search="perfil">PERFIL</th>
                        <th class="col-collapse" data-search="estado_usuario">ESTADO</th>
                        <th class="btn-actions no-responsive" style="width: 100px;">ACCIONES</th>
                    </tr>
                </thead>
            </table>

        </div>

    </div>


**Nota:** El form_action mandará a la siguiente url: /sistema/usuario/buscar/campo/valor/, donde ``campo`` es el valor del select y ``valor`` es el texto digitado de búsqueda

Con lo anterior tendremos la siguiente vista:

.. figure:: img/dwgrid_4.png

   Vista desktop


Vista con ordenamiento
--------------------
Podemos especificar un modo adicional y es el del ordenamiento según las columnas definidas en la cabecera con el atributo ``data-search="campo"`` donde ``campo`` será la columna de ordenamiento.  Para este caso armamos la siguiente vista:

.. code-block:: php

    <?php View::notify(); ?>

    <script type="text/javascript">
        $(function() {
            $('table').dwGrid({
                order_attr: '<?php echo (APP_AJAX) ? 'class="dw-ajax dw-spinner"' : ''; ?>', //Atributos básicos para los link de ordenamiento
                order_action: '<?php echo PUBLIC_PATH; ?>sistema/usuario/listar/' //Url donde se listará los elementos según el orden seleccionado
            });
        });
    </script>

    <div class="container-fluid dw-shell-view">

        <?php View::process($page_module, $page_title, $set_title); ?>

        <!-- Contenedor para los botones superiores -->
        <div class="btn-toolbar btn-toolbar-top">
            <div class="btn-actions">
                <?php echo DwHtml::button("sistema/usuario/agregar/", 'agregar', array('class'=>'btn-success'), 'check', APP_AJAX); ?>
            </div>
        </div>

        <div class="dw-overflow">

            <!-- Se debe indicar la clase "tabla-responsive" -->
            <table class="table table-bordered table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>NUM</th>
                        <th class="col-collapse" data-search="login">LOGIN</th>
                        <th class="col-collapse" data-search="nombre">NOMBRE</th>
                        <th class="col-collapse" data-search="apellido">APELLIDO</th>
                        <th class="col-collapse" data-search="email">EMAIL</th>
                        <th class="col-collapse" data-search="perfil">PERFIL</th>
                        <th class="col-collapse" data-search="estado_usuario">ESTADO</th>
                        <th class="btn-actions no-responsive" style="width: 100px;">ACCIONES</th>
                    </tr>
                </thead>
            </table>

        </div>

    </div>

**Nota:** El order_action mandará a la siguiente url: /sistema/usuario/listar/order.campo.asc/, donde ``campo`` es el valor de la columna seleccionada

Con lo anterior tendremos la siguiente vista:

.. figure:: img/dwgrid_5.png

   Vista desktop


Vista con formulario y ordenamiento
--------------------
Podemos especificar un modo adicional y es el del ordenamiento según las columnas definidas en la cabecera con el atributo ``data-search="campo"`` donde ``campo`` será la columna de ordenamiento.  Para este caso armamos la siguiente vista:

.. code-block:: php

    <?php View::notify(); ?>

    <script type="text/javascript">
        $(function() {
            $('table').dwGrid({
                form_search: true,
                form_action: '<?php echo PUBLIC_PATH; ?>sistema/usuario/buscar/',
                order_attr: '<?php echo (APP_AJAX) ? 'class="dw-ajax dw-spinner"' : ''; ?>', //Atributos básicos para los link de ordenamiento
                order_action: '<?php echo PUBLIC_PATH; ?>sistema/usuario/listar/' //Url donde se listará los elementos según el orden seleccionado
            });
        });
    </script>

    <div class="container-fluid dw-shell-view">

        <?php View::process($page_module, $page_title, $set_title); ?>

        <!-- Contenedor para los botones superiores -->
        <div class="btn-toolbar btn-toolbar-top">
            <div class="btn-actions">
                <?php echo DwHtml::button("sistema/usuario/agregar/", 'agregar', array('class'=>'btn-success'), 'check', APP_AJAX); ?>
            </div>
        </div>

        <div class="dw-overflow">

            <!-- Se debe indicar la clase "tabla-responsive" -->
            <table class="table table-bordered table-hover table-striped table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>NUM</th>
                        <th class="col-collapse" data-search="login">LOGIN</th>
                        <th class="col-collapse" data-search="nombre">NOMBRE</th>
                        <th class="col-collapse" data-search="apellido">APELLIDO</th>
                        <th class="col-collapse" data-search="email">EMAIL</th>
                        <th class="col-collapse" data-search="perfil">PERFIL</th>
                        <th class="col-collapse" data-search="estado_usuario">ESTADO</th>
                        <th class="btn-actions no-responsive" style="width: 100px;">ACCIONES</th>
                    </tr>
                </thead>
            </table>

        </div>

    </div>

Con lo anterior tendremos la siguiente vista:

.. figure:: img/dwgrid_6.png

   Vista desktop
