<?php
class page_informes extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Informes');
    
        if (!$paso) $paso=1;
        
        $formDatos=$this->add('Form');
        $formDatos->addField('dropdown','ejercicio')
        	->setValueList(array('2012'=>'2012','2013'=>'2013'));
        $formDatos->addField('dropdown','mes')
        	->setValueList(array('1'=>'Enero',
        	'2'=>'Febrero',
        	'3'=>'Marzo',
        	'4'=>'Abril',
        	'5'=>'Mayo',
        	'6'=>'Junio',
        	'7'=>'Julio',
        	'8'=>'Agosto',
        	'9'=>'Septiembre',
        	'10'=>'Octubre',
        	'11'=>'Noviembre',
        	'12'=>'Diciembre'));
        $formDatos->addField('radio','informe')->
        	setValueList(array('T'=>'Transformación','E'=>'Envasado'))->
        	validateNotNULL('Dime el tipo de datos que debo mostrar')->set('T');
        $b=$formDatos->addSubmit('Mostrar');
        $b->js('click',$b->js()->hide());
        
        $m=$this->add('Model_Informes');    
        $grid=$this->add('Grid');
        $grid->setModel($m);
        $grid->addPaginator();    
                 
        if ($formDatos->isSubmitted()) {
            $m=$this->add('Model_Informes');
            $res=$m->CargarInforme($formDatos->get('informe'),$formDatos->get('ejercicio'),$formDatos->get('mes'));
            
            $grid->js(null,$b->js(null,$grid->js()->reload())->show())->univ()->successMessage('Mes Calculado correctamente')->execute();
        }
    }
}