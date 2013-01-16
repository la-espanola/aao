<?php
class Frontend extends ApiFrontend {
    function init(){
        parent::init();
        //Si se necesita conexi—n a bb.dd. permanente descomentar
        //la siguiente l’nea.
        //Si no es as’ copiar la siguente l’na a las p‡ginas
        //que necesiten conexion a bb.dd.
        $this->dbConnect();
 
        //incluimos atk e inidicamos quŽ versi—n
        $this->requires('atk','4.2.0');
 
        // Indicamos la localizacion de los add-on.
        $this->addLocation('atk4-addons',array(
                    'php'=>array(
                        'mvc',
                        'misc/lib',
                        )
                    ))
            ->setParent($this->pathfinder->base_location);
 
        // jUI es necesario para las funciones ajax y javascript
        $this->add('jUI');
 
        // Inicializamos las librer’as javascript que estar‡n
        // disponible para toda la aplicaci—n
        // Si tienes c—digo javascript propio puedes ponerlo en,
        // templates/js/atk4_univ_ext.js e incluirlo aqu’
        
        $this->api->template->set('page_title','AAO');
        
        $this->js()
            ->_load('atk4_univ')
            ->_load('ui.atk4_notify');
        
        $this->add('Menu',null,'Menu')
            ->addMenuItem('index','Inicio')
            ->addMenuItem('variedades','Variedades')
            ->addMenuItem('estados','Estados')
            ->addMenuItem('destinos','Destinos')
            ->addMenuItem('operaciones','Operaciones')
            ->addMenuItem('productos','Productos')
            ->addMenuItem('clientesproveedores','Clientes/Proveedores')
            ->addMenuItem('movimientos','Movimientos')
            ->addMenuItem('informes','Informes');
            
            
    }
}