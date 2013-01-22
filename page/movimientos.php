<?php
class page_movimientos extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Movimientos');
    
        if (!$paso) $paso=1;
        
        $mesanterior=$this->add('myUtils')->getMesPasado();
        $mes=$mesanterior['mes'];
        $ejer=$mesanterior['ejercicio'];
        $formDatos=$this->add('Form');
        $formDatos->add('H2')->set('Importar movimientos');
        $comboejer=$formDatos->addField('dropdown','ejercicio')
        	->setValueList(array('2012'=>'2012','2013'=>'2013'))
        	->set($ejer);
        $combomes=$formDatos->addField('dropdown','mes')
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
        	'12'=>'Diciembre'))
        	->set($mes);
        $radiotipo=$formDatos->addField('radio','importar')->
        	setValueList(array('C'=>'Compras','T'=>'Traspasos', 'V'=>'Ventas Granel', 'E'=>'Envasado','M'=>'Mermas','F'=>'Ventas PT'))->
        	validateNotNULL('Dime el tipo de datos que debo importar')->set('C');
    
    
        $botonImportar=$formDatos->addButton('Importar mes desde ERP')
        	->js('click',$formDatos->js()->atk4_form('submitForm','botonImportar'));
        $botonRefrescar=$formDatos->addButton('Refrescar')
        	->js('click',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        
        
        $comboejer->js('change',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        $combomes->js('change',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        $radiotipo->js('change',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        
        $info=$this->add('P')->set('Importación inactiva');
        $m=$this->add('Model_Movimientos');
        
        if ($_GET['mes']) {
        	$m->FiltrarPorMesyTipo($_GET['ejercicio'],$_GET['mes'],$_GET['importar']);
        }
        else if ($formDatos->get('ejercicio')) {
	        $m->FiltrarPorMesyTipo($formDatos->get('ejercicio') ,$formDatos->get('mes'),$formDatos->get('importar') );
        }
        else $m->FiltrarPorMesyTipo($ejer,$mes);
        $grid=$this->add('Grid');
        $grid->setModel($m);
        $grid->addPaginator();
        $this->api->stickyGET('ejercicio');
        $this->api->stickyGET('mes');
        $this->api->stickyGET('importar');
        
        if ($formDatos->isSubmitted())
        {
	        if ($formDatos->isClicked('botonImportar')) {
	            $paso=$this->recall('PasoImport');
	            $info->set('Importando');
	            $datos=$formDatos->get('importar');
	            if ($datos) {
	            	$m2=$this->add('Model_Movimientos');
	            	$res=$m2->ImportarDatosDeERP($datos,$formDatos->get('ejercicio'),$formDatos->get('mes'),1);
	            } else $this->js()->univ()->errorMessage('Selecciona un tipo de datos antes de importar')->execute();
	            $this->memorize('PasoImport', $paso+1);
	            if (!$res) {
	            	$grid->js()->reload()->execute();   
	            }
	            else {  
	            	$formDatos->js(null, $info->js()->text('Importando paso '.($paso+1)))->atk4_form('submitForm','botonImportar')->execute();
	            } 
	        } else  {
	        	//Filtrar datos
		        $grid->js()
	        		->reload(array(	'mes'=>$formDatos->get('mes'),
	        						'ejercicio'=>$formDatos->get('ejercicio'),
	        						'importar'=>$formDatos->get('importar')))->execute();
	        }
        }
        else {
            $paso=1;
            $this->memorize('PasoImport',$paso); 
        }
    }
}