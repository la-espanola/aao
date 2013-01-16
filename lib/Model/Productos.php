<?php
class Model_Productos extends Model_Table {
    public $table='productos';
    
    function init() {
        parent::init();
        
        $this->addfield('name')->
                mandatory('Cada producto necesita tener un nombre, no olvides indicarlo')->
                sortable(true)->
                caption('Nombre');
        $this->hasOne('Variedades')->caption('Variedad')->sortable(true);
        $this->hasOne('Estados')->caption('Estado')->sortable(true);
        $this->hasOne('Destinos')->caption('Destino')->sortable(true); 
        $this->addfield('factor_actual')->caption('Factor Actual')->mandatory('El factor de conversión es un dato obligatorio'); 
         
    }
    
}