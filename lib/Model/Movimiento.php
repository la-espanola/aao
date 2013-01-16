<?php
class Model_Movimiento extends Model_Table {
	public $table='movimiento';
	
	function init() {
		parent::init();	
		
		$this->addField('clave');
		$this->addField('kilos');
		$this->addField('fecha');
		$this->addField('referencia_erp');
		$this->hasOne('Producto');
	}
		
	function getSumPorClave($mes) {
		return 0;
	}
}