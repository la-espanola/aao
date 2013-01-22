<?php
class Model_Operaciones extends Model_Table {
    public $table='operaciones';
    
    function init() {
        parent::init();
        
        $this->addfield('name')->
            mandatory('Campo requerido')->
            caption('Nombre');
        $this->getField('id')->
            system(false)->
            visible(true)->
            editable(true)->
            enum(array('C','V','E','M','T','F'))->
            mandatory('El código de la operación es obligatorio');
    }
    
}