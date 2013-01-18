<?php
class page_informes extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Informes');
    
        if (!$paso) $paso=1;
        if ($_GET['mes']) {
        	$mes=$_GET['mes'];
        	$ejer=$_GET['ejer'];	
        	$informe=$_GET['informe'];
        } else { 
        	$mesanterior=$this->add('myUtils')->getMesPasado();
	        $mes=$mesanterior['mes'];
	        $ejer=$mesanterior['ejercicio'];
	        $informe='T';
	    }
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
        	validateNotNULL('Dime el tipo de datos que debo mostrar')->set($informe);
        $b=$formDatos->addSubmit('Mostrar');
        $b->js('click',$b->js()->hide());
        
        $m=$this->add('Model_Informes');    
        $m->CargarInforme($informe,$ejer,$mes,$variedad['id']);
        
        $tabla=$this->add('HtmlElement')->setElement('table')->set('.');
        //$linea=$tabla->add('HtmlElement')->setElement('tr');    
        //$linea->add('HtmlElement')->setElement('td')->set('VARIEDADES');
        $var=$this->add('Model_Variedades');
        
        $primeraLinea=true;
        foreach ($var as $variedad) {
	    	$m=$m->FiltrarDatos($informe, $ejer, $mes, $variedad['id']);
	    	$linea=$tabla->add('HtmlElement')->setElement('tr'); 
	    	if ($primeraLinea) {
	    		$linea->add('HtmlElement')->setElement('td')->set(' ');
		    	foreach ($m as $datos) {
		    		$linea->add('HtmlElement')->setElement('td')->set($datos['apartado']);
		    	}
		    	$linea=$tabla->add('HtmlElement')->setElement('tr'); 
		    	$primeraLinea=false;	
	    	}
	    	$linea->add('HtmlElement')->setElement('td')->set($variedad['name']);
	    	foreach ($m as $datos) {
		    	$linea->add('HtmlElement')->setElement('td')->set($datos['kilos']);
	    	}	
        }
                         
        if ($formDatos->isSubmitted()) {
            $grid->js(null,$b->js(null,$grid->js()
            ->reload(array(	'informe'=>$formDatos->get('informe'),
            				'mes'=>$formDatos->get('mes'),
		        			'ejercicio'=>$formDatos->get('ejercicio'))))->show())->univ()
		        				->successMessage('Mes Calculado correctamente')->execute();
        }
    }
}