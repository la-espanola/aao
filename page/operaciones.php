<?php
class page_operaciones extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Operaciones');
        
        $this->add('CRUD')->setModel('Model_Operaciones');
    }
}