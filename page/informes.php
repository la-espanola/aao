<?php
class page_informes extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Informes');
    
        if (!$paso) $paso=1;
        if ($_GET['mes']) {
        	$mes=$_GET['mes'];
        	$ejercicio=$_GET['ejercicio'];	
        	$informe=$_GET['informe'];
        } else { 
        	$mesanterior=$this->add('myUtils')->getMesPasado();
	        $mes=$mesanterior['mes'];
	        $ejercicio=$mesanterior['ejercicio'];
	        $informe='T';
	    }
        $formDatos=$this->add('Form');
        $formDatos->addField('dropdown','ejercicio')
        	->setValueList(array('2012'=>'2012','2013'=>'2013'))->set($ejercicio);
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
        $m->CargarInforme($informe,$ejercicio,$mes,$variedad['id']);
        
        $tabla=$this->add('HtmlElement')->setElement('table')->setAttr('id','tabla');
        $var=$this->add('Model_Variedades');
        
        $primeraLinea=true;
        foreach ($var as $variedad) {
	    	$m=$m->FiltrarDatos($informe, $ejercicio, $mes, $variedad['id']);
	    	$linea=$tabla->add('HtmlElement')->setElement('tr'); 
	    	if ($primeraLinea) {
	    		$linea->add('HtmlElement')->setElement('td')->addClass('titulo')->set($informe.' '.$ejercicio.' '.$mes);
	    		$estado_anterior='';
		    	foreach ($m as $datos) {
		    		if ($estado_anterior=='') $linea->add('HtmlElement')->setElement('td')->addClass('titulo')->set('VERDE');
		    		else if ($estado_anterior=='V' && $datos['destinos_id']!='V') $linea->add('HtmlElement')->setElement('td')->addClass('titulo')->set('NEGRA');
		    		$linea->add('HtmlElement')->setElement('td')->addClass('titulo')->set($datos['apartado']);
		    		$estado_anterior=$datos['destinos_id'];
		    	}
		    	$linea=$tabla->add('HtmlElement')->setElement('tr'); 
		    	$primeraLinea=false;	
	    	}
	    	$linea->add('HtmlElement')->setElement('td')->addClass('titulo')->set($variedad['name']);
	    	$estado_anterior='';
	    	foreach ($m as $datos) {
	    		if ($estado_anterior=='') $linea->add('HtmlElement')->setElement('td')->addClass('titulo')->set('VERDE');
	    		else if ($estado_anterior=='V' && $datos['destinos_id']!='V') $linea->add('HtmlElement')->setElement('td')->addClass('titulo')->set('NEGRA');
		    	$linea->add('HtmlElement')->setElement('td')->addClass('numero')->set(number_format($datos['kilos']));
		    	$estado_anterior=$datos['destinos_id'];
	    	}	
        }
        
        $tabla->js(true)->_load('transpose_table')->univ()->transposeTable('aao_informes_htmlelement');
                         
        if ($formDatos->isSubmitted()) {
            $tabla->js(null,$tabla->js(null, $b->js()->show()))->reload(array('informe'=>$formDatos->get('informe'),
            				'mes'=>$formDatos->get('mes'),
		        			'ejercicio'=>$formDatos->get('ejercicio')))->execute();
        }
    }
}