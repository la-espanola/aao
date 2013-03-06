<?php
class Model_Usuarios extends Model_Table {
    public $table='usuarios';
    
    function init() {
        parent::init();
        
        $this->addfield('name')
             ->mandatory('Campo requerido')
             ->caption('Nombre');
        $this->addfield('email')
        	->mandatory('Campo requerido');
        $this->addField('password')->system(true);
        $this->addField('admin')->setValueList(array('S'=>'Sí','N'=>'No'));
    }
}