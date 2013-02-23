Controladores del Backend
====================
.. contents:: Los controladores utilizados en el backend deben heredar del ``BackendController`` y no del ``AppController``, pues este se encarga de validar los pivilegios y autenticaciones del sistema.  Para el uso del Frontend es necesario extender de la clase ``AppController``

Creando controladores para el Backend
--------------------
Para que un controlador sea usado en el backend lo definimos de la siguiente manera:

.. code-block:: php

    <?php

    class MiController extends BackendController {

    }

Dentro de los controladores existen variables para el nombre del módulo, título de la página y/o tipo de reporte (si se utiliza).  Estas variables son mostradas en la `vista <vistas.rst>`_

.. code-block:: php

    <?php

    class MiController extends BackendController {

        /**
         * Método que se ejecuta antes de cualquier acción
         */
        protected function before_filter() {
            //Se establece el nombre del módulo actual para mostrarlo en la vista
            $this->page_module = 'Sistema';
        }

        /**
         * Método para agregar
         */
        public function agregar() {
            *
            *
            *
            //Se establece el nombre de la página actual, para establecerlo en el atributo <title></title>
            $this->page_title = 'Agregar cliente';
        }

        /**
         * Método para modificar
         */
        public function modificar($codigo) {
            *
            *
            *
            //Se establece el nombre de la página actual, para establecerlo en el atributo <title></title>
            $this->page_title = 'Modificar cliente';
        }
    }

Creando controladores para reportes en el Backend
--------------------
En el backend está definido un módulo para los reportes, de tal manera que sea más organizado nuestro código.  Se definen como cualquier controlador del backend, solo que recibe en los métodos como parámetro el tipo de reporte a mostrar.

.. code-block:: php

    <?php

    class ClienteController extends BackendController {
        /**
         * Método que se ejecuta antes de cualquier acción
         */
        protected function before_filter() {
            //Se establece el nombre del módulo actual para mostrarlo en el reporte
            $this->page_module = 'Sistema';
        }

        /**
         * Método para listar los clientes del sistema
         * @param type $fecha
         * @return type
         */
        public function listar($formato='html') {
            *
            *
            *
            //Defino el formato de salida
            $this->page_format = $formato;
            //Defino el título de la página
            $this->page_title = 'Listado de clientes';
        }
    }

Al definir un formato de salida ``$this->page_format = $formato``, en este caso ``html`` el backend internamente incluirá la vista ``reporte/cliente/listar.html.phtml``.

Si el formato fuera ``xls`` incluirá la vista ``reporte/cliente/listar.xls.phtml``.

**Nota:** El backend soporta solamente los reportes en ``html``.  Si deseas agregar otro tipo de fomato deberás utilizar las librerías adecuadas.