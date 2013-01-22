<?php
class page_procesados extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Procesados');
        
        $this->add('CRUD')->setModel('Model_Procesados');
    }
}