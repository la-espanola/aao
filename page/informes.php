<?php
class page_informes extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Informes');
    
        if (!$paso) $paso=1;
        
        $mesanterior=$this->add('myUtils')->getMesPasado();
        $mes=$mesanterior['mes'];
        $ejer=$mesanterior['ejercicio'];
        
        $formDatos=$this->add('Form');
        $formDatos->addField('dropdown','ejercicio')
        	->setValueList(array('2012'=>'2012','2013'=>'2013'))->set($ejer);
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
        	'12'=>'Diciembre'))->set($mes);
        $formDatos->addField('radio','informe')->
        	setValueList(array('T'=>'Transformación','E'=>'Envasado'))->
        	validateNotNULL('Dime el tipo de datos que debo mostrar')->set('T');
        $b=$formDatos->addSubmit('Mostrar');
        $b->js('click',$b->js()->hide());
        
        $m=$this->add('Model_Informes');    
        if ($_GET['mes']) {
        	$m->CargarInforme($_GET['informe'],$_GET['ejercicio'],$_GET['mes']);
        }
        else $m->CargarInforme('T',$ejer,$mes);
        $grid=$this->add('Grid');
        $grid->setModel($m);
        $grid->addPaginator();    
                 
        if ($formDatos->isSubmitted()) {
            $grid->js(null,$b->js(null,$grid->js()
            ->reload(array(	'informe'=>$formDatos->get('informe'),
            				'mes'=>$formDatos->get('mes'),
		        			'ejercicio'=>$formDatos->get('ejercicio'))))->show())->univ()
		        				->successMessage('Mes Calculado correctamente')->execute();
        }
    }
}