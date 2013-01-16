<?php
class Model_Variedades extends Model_Table {
    public $table='variedades';
    
    function init() {
        parent::init();
        
        $this->addfield('name')->
            mandatory('Campo requerido')->
            caption('Nombre');
        $this->getField('id')->
            system(false)->
            visible(true)->
            editable(true)->
            enum(array('MZN', 'GOR', 'CAR', 'CAC', 'HOJ', 'OTR'))->
            mandatory('El código de la variedad es obligatorio'); 
    }
}