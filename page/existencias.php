<?php
class page_existencias extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Existencias');
        
        $this->add('CRUD')->setModel('Model_Existencias');
    }
}