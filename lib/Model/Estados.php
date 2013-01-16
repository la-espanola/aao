<?php
class Model_Estados extends Model_Table {
    public $table='estados';
    
    function init() {
        parent::init();
        
        $this->addfield('name')->
                mandatory('¡Has olvidado indicar el nombre!')->
                caption('Nombre');
        $this->getField('id')->
            system(false)->
            visible(true)->
            editable(true)->
            enum(array('C', 'T'))->
            mandatory('El código de estado es obligatorio');         
    }
    
}