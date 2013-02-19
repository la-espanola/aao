<?php
class page_productos extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Productos');
        
        $b=$this->add('Button')
        	->set('Importar Aceituna de ERP');
        $b2=$this->add('Button')
        	->set('Importar Productos Terminados de ERP');
        $b->js('click', $b->js(null, $b2->js()->hide())->hide());
        $b2->js('click', $b->js(null,$b2->js()->hide())->hide());
        
        $info=$this->add('P')->set('Importación inactiva');
        
        $this->add('H3')->set('Aplicar factores a movimientos de');
        if ($_GET['mes']) {
        	$mes=$_GET['mes'];
        	$ejercicio=$_GET['ejercicio'];	
        	$informe=$_GET['informe'];
        } else { 
        	$mesanterior=$this->add('MyUtils')->getMesPasado();
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
        $formDatos->addSubmit('Aplicar');
        $this->add('HR');
        $crud=$this->add('CRUD')->setModel('Model_Productos');
        
        if ($b->isClicked()) {
            $paso=$this->recall('PasoImport');
            $info->set('Importando');
            $m=$this->add('Model_Productos');
            $res=$m->ImportarDeERP($paso);
            $this->memorize('PasoImport', $paso+1);
            if (!$res) {
            	if ($crud->grid) $crud->grid->js(null,$b->js()->show())->reload()->execute();   
            }
            else {     
                $b->js(null, $info->js()->text('Importando paso '.($paso+1)))->click()->execute();
            } 
        }
        else {
            $paso=1;
            $this->memorize('PasoImport',$paso); 
        }
        
        if ($b2->isClicked()) {
            $paso=$this->recall('PasoImport');
            $info->set('Importando');
            $m=$this->add('Model_Productos');
            $res=$m->ImportarPTDeERP($paso);
            $this->memorize('PasoImport', $paso+1);
            if (!$res) {
            	if ($crud->grid) $crud->grid->js(null,$b->js()->show())->reload()->execute();   
            }
            else {     
                $b->js(null, $info->js()->text('Importando paso '.($paso+1)))->click()->execute();
            } 
        }
        else {
            $paso=1;
            $this->memorize('PasoImport',$paso); 
        }
        
        
        if ($formDatos->isSubmitted()) {
	        if ($this->add('Model_Movimientos')->aplicarFactoresActualesaMes($formDatos->get('ejercicio'), $formDatos->get('mes')))
	        	$this->js()->univ()->successMessage('Factores aplicados correctamente')->execute();
	        else $this->js()->univ()->errorMessage('Error aplicando factores')->execute();	
        }
    }
}