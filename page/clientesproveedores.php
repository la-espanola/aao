<?php
class page_clientesproveedores extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Clientes/Proveedores');
    
        if (!$paso) $paso=1;
    
        $b=$this->add('Button')->set('Importar desde ERP');
        $b->js('click', $b->js()->hide());
        
        $info=$this->add('P')->set('Importación inactiva');
        
        $grid=$this->add('Grid');
        $grid->setModel('Model_ClientesProveedores');
        $grid->addPaginator();
        $grid->addQuickSearch(array('name'));
            
        if ($b->isClicked()) {
            $paso=$this->recall('PasoImport');
            $info->set('Importando');
            $m=$this->add('Model_ClientesProveedores');
            $res=$m->ImportarDeERP($paso);
            $this->memorize('PasoImport', $paso+1);
            if (!$res) $grid->js(null,$b->js()->show())->reload()->execute();   
            else {     
                $b->js(null, $info->js()->text('Importando paso '.($paso+1)))->click()->execute();
            } 
        }
        else {
            $paso=1;
            $this->memorize('PasoImport',$paso); 
        }
    }
}