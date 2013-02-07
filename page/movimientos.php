<?php
class page_movimientos extends Page {  
    
    function init(){
        parent::init();
        
        $this->api->stickyGET('ejercicio');
        $this->api->stickyGET('mes');
        $this->api->stickyGET('operaciones_id');
        
        $this->add('H1')->set('Movimientos');
    
        if (!$paso) $paso=1;
<<<<<<< HEAD
        
        $mesanterior=$this->add('MyUtils')->getMesPasado();
        $mes=$mesanterior['mes'];
        $ejer=$mesanterior['ejercicio'];
=======
        if ($_GET['mes']) {
	        $mes=$_GET['mes'];
	        $ejer=$_GET['ejercicio'];
	        $operaciones_id=$_GET['operaciones_id'];
        }
        else {
	        $mesanterior=$this->add('myUtils')->getMesPasado();
	        $mes=$mesanterior['mes'];
	        $ejer=$mesanterior['ejercicio'];
	        $operaciones_id='C';
        }
>>>>>>> Corregida edición de movimientos
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
        $radiotipo=$formDatos->addField('radio','operaciones_id')->
        	setValueList(array('C'=>'Compras','T'=>'Traspasos', 'V'=>'Ventas Granel', 'E'=>'Envasado','M'=>'Mermas','F'=>'Ventas PT'))->
        	validateNotNULL('Dime el tipo de datos que debo importar')->set($operaciones_id);
    
    
        $botonImportar=$formDatos->addButton('Importar mes desde ERP')
        	->js('click',$formDatos->js(null,$formDatos->js()->hide())->atk4_form('submitForm','botonImportar'));
        $botonRefrescar=$formDatos->addButton('Refrescar')
        	->js('click',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        
        
        $comboejer->js('change',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        $combomes->js('change',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        $radiotipo->js('change',$formDatos->js()->atk4_form('submitForm','botonRefrescar'));
        
        $info=$this->add('P')->set('Importación inactiva');
        
        $m=$this->add('Model_Movimientos');
        if ($_GET['mes']) {
        	$m->FiltrarPorMesyTipo($_GET['ejercicio'],$_GET['mes'],$_GET['operaciones_id']);
        }
        else if ($formDatos->get('ejercicio')) {
	        $m->FiltrarPorMesyTipo($formDatos->get('ejercicio') ,$formDatos->get('mes'),$formDatos->get('operaciones_id') );
        }
        else $m->FiltrarPorMesyTipo($ejer,$mes,'X');
        
        $grid=$this->add('CRUD');
        $grid->setModel($m);
        if ($grid->grid) {
        	$grid->grid->addPaginator();
        }
        if ($grid->form) {
	        $grid->form->addField('hidden','ejercicio')->set($ejer); 
	        $grid->form->addField('hidden','mes')->set($mes); 
	        $grid->form->addField('hidden','operaciones_id')->set($operaciones_id); 
        }
        
        if ($formDatos->isSubmitted())
        {
	        if ($formDatos->isClicked('botonImportar')) {
	            $paso=$this->recall('PasoImport');
	            $info->set('Importando');
	            $datos=$formDatos->get('operaciones_id');
	            if ($datos) {
	            	$m2=$this->add('Model_Movimientos');
	            	$res=$m2->ImportarDatosDeERP($datos,$formDatos->get('ejercicio'),$formDatos->get('mes'),1);
	            } else $this->js()->univ()->errorMessage('Selecciona un tipo de datos antes de importar')->execute();
	            $this->memorize('PasoImport', $paso+1);
	            if (!$res) {
	            	$grid->js(null,$formDatos->js(null,$info->js()->text('IMPORTACION FINALIZADA'))->show())->reload(array(	'mes'=>$formDatos->get('mes'),
	        						'ejercicio'=>$formDatos->get('ejercicio'),
	        						'operaciones_id'=>$formDatos->get('operaciones_id')))->execute();   
	            }
	            else {  
	            	$formDatos->js(null, $info->js()->text('Importando paso '.($paso+1)))->atk4_form('submitForm','botonImportar')->execute();
	            } 
	        } else  {
	        	//Filtrar datos
		        $grid->js()
	        		->reload(array(	'mes'=>$formDatos->get('mes'),
	        						'ejercicio'=>$formDatos->get('ejercicio'),
	        						'operaciones_id'=>$formDatos->get('operaciones_id')))->execute();
	        }
        }
        else {
            $paso=1;
            $this->memorize('PasoImport',$paso); 
        }
    }
}