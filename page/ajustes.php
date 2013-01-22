<?php
class page_ajustes extends Page {  
    
    function init(){
        parent::init();
        
        $this->add('H1')->set('Ajustes');
        $mesanterior=$this->add('myUtils')->getMesPasado();
	    $mes=$mesanterior['mes'];
	    $ejercicio=$mesanterior['ejercicio'];
	    
        $form=$this->add('Form');
        $form->add('H3')->set('Convertir');
        
        $form->addField('dropdown','ejercicio')
        	->setValueList(array('2012'=>'2012','2013'=>'2013'))->set($ejercicio);
        $form->addField('dropdown','mes')
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
        $form->addField('dropdown','variedades_id')->setCaption('Variedad')->setModel('Model_Variedades');
        $form->addField('dropdown','estados_id')->setCaption('Estado')->setModel('Model_Estados');
        $form->addField('dropdown','destinos_id')->setCaption('Verde/Negra')->setModel('Model_Destinos');
        $form->addField('dropdown','operaciones_id')->setCaption('Operacion')->setModel('Model_Operaciones');
        
        $form->add('H3')->set('en');
        
        $form->addField('dropdown','variedades_id')->setCaption('Variedad')->setModel('Model_Variedades');
        $form->addField('dropdown','estados_id')->setCaption('Estado')->setModel('Model_Estados');
        $form->addField('dropdown','destinos_id')->setCaption('Verde/Negra')->setModel('Model_Destinos');
        $form->addField('dropdown','operaciones_id')->setCaption('Operacion')->setModel('Model_Operaciones');
        
        $b=$form->addSubmit('Convertir');
        $b->js('click',$b->js()->hide());
        if ($form->isSubmitted()) {
        	$m=$this->add('Model_Movimientos');
        	if ($m->convertir($form->get('ejercicio'),
        					  $form->get('mes'),
        					  array('variedad'=>$form->get('variedades_id'),
        					  		'estado'=>$form->get('estados_id'),
        					  		'destino'=>$form->get('destinos_id'),
        					  		'operacion'=>$form->get('operaciones_id')
        					  	   ),
        					  array('variedad'=>$form->get('variedades_id_2'),
        					  		'estado'=>$form->get('estados_id_2'),
        					  		'destino'=>$form->get('destinos_id_2'),
        					  		'operacion'=>$form->get('operaciones_id_2')
        					  		)
        					 )
        	   ) {
	        	$this->js(null,$b->js()->show())->univ()->successMessage('Conversión finalizada')->execute();
	        } else {
		        $this->js(null,$b->js()->show())->univ()->errorMessage('La conversión ha fallado')->execute();
	        }
        }
    }
}