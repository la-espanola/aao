<?php
class page_variedades extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Variedades');
        
        $this->add('CRUD')->setModel('Model_Variedades');
    }
}