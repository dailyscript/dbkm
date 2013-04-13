Redireccionamiento interno
====================

.. contents:: El Backend está construido para trabajar 100% en ajax o peticiones normales, por tal motivo se ha creado la librería ``DwRedirect``, la cual permite hacer una redirección sin importar cómo se está trabajando la aplicación.

Hacia un método dentro del controlador
--------------------
Para aplicar una redirección dentro de un controlador hacemos lo siguiente:

.. code-block:: php

    <?php

    /**
     * Redirecciona a un método del mismo controlador
     *
     * @param string $action Nombre del método dentro del controlador
     * @param string $params Parámetros a pasar por la url
     */
    toAction($action, $params=null) {

    //Redireccionando al método agregar
    return DwRedirect::toAction('agregar');

    //Redireccionando al método editar, pasando parámetros adicionales
    return DwRedirect::toAction('estado', 'suspender/5');

    return DwRedirect::toAction('listar', 'pag/2');

Hacia un método dentro de otro controlador con o sin modo AJAX
--------------------
Pra mantener una compatibilidad con AJAX el dbkm posee un sistema de redireccionamiento hacia otros módulo o controladores, de tal manera que sin importar el modo en que se esté trabajando, pueda redireccionar sin problema alguno.

.. code-block:: php

    <?php

    /**
     * Redirecciona la ejecución a otro controlador en cualquier modo de trabajo (con o sin AJAX)
     *
     * @param string Parámetros que puede recibir: module, controller, action, parameters
     */
    toRoute("module: modulo", "controller: controlador", "action: action", "parameters: para/me/ters") {

    //Ejemplo:
    DwRedirect::toRoute('module: sistema', 'controller: usuario', 'action: listar', 'parameters: todos/page.5');
    //Redireccionará a sistema/usuario/listar/todos/page.5/

    //Redirección al dashboard
    DwRedirect::toRoute('module: dashboard', 'controller: index');


Hacia un método dentro de otro controlador
--------------------
Cuando la aplicación no se encuentra en modo AJAX o necesitamos redireccionar y recargar la página bajo cualquier modo hacia un método de otro controlador o módulo hacemos lo siguiente:

.. code-block:: php

    <?php

    /**
     * Redirecciona la ejecución a otro controlador en un
     * tiempo de ejecución determinado
     *
     * @param string $route ruta a la que será redirigida la petición.
     * @param integer $seconds segundos que se esperarán antes de redirigir
     * @param integer $statusCode código http de la respuesta, por defecto 302
     */
    to($route = null, $seconds = null, $statusCode = 302) {

    //Ejemplo:
    DwRedirect::to('sistema/usuario/agregar/');
