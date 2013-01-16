<?php
class page_movimientos extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Movimientos');
    
        if (!$paso) $paso=1;
        
        $formDatos=$this->add('Form');
        $radio=$formDatos->addField('radio','importar')->
        	setValueList(array('C'=>'Compras','T'=>'Traspasos'))->
        	validateNotNULL('Dime el tipo de datos que debo importar')->set('C');
    
    
        $b=$formDatos->addSubmit('Importar desde ERP');
        $b->js('click', $b->js()->hide());
        
        $info=$this->add('P')->set('Importación inactiva');
        
        $m=$this->add('Model_Movimientos');
        $grid=$this->add('Grid');
        $grid->setModel($m);
        $grid->addPaginator();
          
        if ($formDatos->isSubmitted()) {
            $paso=$this->recall('PasoImport');
            $info->set('Importando');
            $datos=$formDatos->get('importar');
            if ($datos=='C' || $datos=='T') $res=$m->ImportarDatosDeERP($datos,2012,12,1);
            else $this->js(null,$b->js()->show())->univ()->errorMessage('Selecciona un tipo de datos antes de importar')->execute();
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