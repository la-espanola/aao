<?php
class Model_Procesados extends Model_Table {
    public $table='procesados';
    
    function init() {
        parent::init();
        
        $this->addfield('name')->
                mandatory('¡Has olvidado indicar el nombre!')->
                caption('Nombre'); 
        $this->getField('id')->
            system(false)->
            visible(true)->
            editable(true)->
            enum(array('NOP','CLS', 'DES', 'RPA', 'RCI', 'RDJ', 'MCH','ROT','RTP'))->
            mandatory('El código de procesado es obligatorio');               
    }
    
}