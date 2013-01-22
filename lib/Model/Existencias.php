<?php
class Model_Existencias extends Model_Table {
    public $table='existencias';
    
    function init() {
        parent::init();
        $this->addField('ejercicio')->mandatory('Falta indicar el ejercicio');
        $this->addField('mes')->mandatory('Falta indicar el mes');
        $this->addField('informe')->enum(array('E','T'))->mandatory('Falta indicar el tipo de informe');
        $this->hasOne('Variedades')->caption('Variedad')->sortable(true)->mandatory('Falta indicar la variedad');
        $this->hasOne('Destinos')->caption('Destino')->sortable(true)->mandatory('Falta indicar el destino'); 
        $this->addfield('kilos_convertidos')->mandatory('¡No has indicado los kilos iniciales!'); 
         
    }
    
    function getTotal($ejercicio, $mes, $informe, $variedad, $destino) {
	    $this->initQuery();
    	$this->addCondition('ejercicio','=',$ejercicio);
    	$this->addCondition('mes','=',$mes);
    	$this->addCondition('informe','=',$informe);
    	$this->addCondition('variedades_id','=',$variedad);
    	$this->addCondition('destinos_id','=',$destino);
    	return $this->dsql()
	    	->field($this->dsql()->expr('sum(kilos_convertidos)'),'total_kilos')
	    	->getOne();
    }
    
    function setTotal($ejercicio, $mes, $informe, $variedad, $destino, $kilos) {
    	$this->initQuery();
    	$this->addCondition('ejercicio','=',$ejercicio);
    	$this->addCondition('mes','=',$mes);
    	$this->addCondition('informe','=',$informe);
    	$this->addCondition('variedades_id','=',$variedad);
    	$this->addCondition('destinos_id','=',$destino);
    	$this->tryLoadAny();
    	$this['kilos_convertidos']=$kilos;
    	$this->save();
    }
}