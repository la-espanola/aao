<?php
class page_estados extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Estados');
        
        $this->add('CRUD')->setModel('Model_Estados');
    }
}