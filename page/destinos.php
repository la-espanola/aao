<?php
class page_destinos extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Destinos');
        
        $this->add('CRUD')->setModel('Model_Destinos');
    }
}