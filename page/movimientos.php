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
        $formDatos->addField('radio','importar')->
        	setValueList(array('C'=>'Compras','T'=>'Traspasos', 'V'=>'Ventas Granel'))->
        	validateNotNULL('Dime el tipo de datos que debo importar')->set('C');
    
    
        $botonImportar=$formDatos->addSubmit('Importar mes desde ERP');
        $botonRefrescar=$formDatos->addSubmit('Refrescar');
        $botonImportar->js('click', $botonImportar->js()->hide());
        
        $comboejer->js('change',$botonRefrescar->js()->click());
        $combomes->js('change',$botonRefrescar->js()->click());
        
        $info=$this->add('P')->set('Importación inactiva');
        $m=$this->add('Model_Movimientos');
        
        if ($_GET['mes']) {
        	$m->FiltrarPorMes($_GET['ejercicio'],$_GET['mes']);
        }
        else $m->FiltrarPorMes($ejer,$mes);
        $grid=$this->add('Grid');
        $grid->setModel($m);
        $grid->addPaginator();
        
        
        if ($botonRefrescar->isClicked()) {
    		echo 'kkk';
            $paso=$this->recall('PasoImport');
            $info->set('Importando');
            $datos=$formDatos->get('importar');
            if ($datos=='C' || $datos=='T' || $datos=='V') 
            	$res=$m->ImportarDatosDeERP($datos,$formDatos->get('ejercicio'),$formDatos->get('mes'),1);
            else $this->js(null,$botonImportar->js()->show())->univ()->errorMessage('Selecciona un tipo de datos antes de importar')->execute();
            $this->memorize('PasoImport', $paso+1);
            if (!$res) {
            	$grid->js(null,$botonImportar->js()->show())->reload()->execute();   
            }
            else {     
                $botonImportar->js(null, $info->js()->text('Importando paso '.($paso+1)))->click()->execute();
            } 
        } else if ($formDatos->isSubmitted()) {
        	//Filtrar datos
        	echo 'lll';
	        $grid->js(null,$botonImportar->js()->show())
	        	->reload(array(	'mes'=>$formDatos->get('mes'),
	        					'ejercicio'=>$formDatos->get('ejercicio')))->execute();
        }
        else {
            $paso=1;
            $this->memorize('PasoImport',$paso); 
        }
    }
}