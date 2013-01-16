<?php
class Model_Producto extends Model_Table {
	public $table='productos';
	function init() {
		parent::init();
		$this->addField('descripcion');
		$this->addField('variedad');
		$this->addField('cruda_tratada');
		$this->addField('verde_negra');
	}
	
	
}  