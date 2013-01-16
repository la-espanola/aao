<?php
class Model_Destinos extends Model_Table {
    public $table='destinos';
    
    function init() {
        parent::init();
        
        $this->addfield('name')->
            mandatory('Necesitamos un nombre para este destino')->
            caption('Nombre');
        $this->getField('id')->
            system(false)->
            visible(true)->
            editable(true)->
            enum(array('V', 'N'))->
            mandatory('El código de este destino es obligatorio');    
    }
    
}