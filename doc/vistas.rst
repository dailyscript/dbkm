Creación de vistas
====================

.. contents:: Las vistas se trabajan como cualquier otra anteriormente realizada por ti.  Hay una estructura específica en el html para que vaya acorde al estilo del backend el cual deberás seguir si quieres mantener la armonía en el diseño.

Creando vistas para el Backend
--------------------

Para crear una vista el html que debes incluir lo siguiente:

.. code-block:: php

    //Para mostrar los mensajes del sistema
    <?php View::notifi(); ?>

    <div class="container-fluid dw-shell-view">

        <!--
         Para mostrar la información del módulo, proceso (tomado del título de la página)
         también para indicar si se cambia el título. Esta última variable está definida automáticamente
         del BackendController el cual verifica si se debe o no cambiar el título de la página
        -->
        <?php View::process($page_module, $page_title, $set_title); ?>

        <!-- Este div contenedor se utiliza sólo en los datagrid para que no haya desborde en los dispositivos móviles -->
        <div class="dw-overflow">

        </div>

    </div>