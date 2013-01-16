<?php
class page_productos extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Productos');
        $this->api->forget();
        $this->add('CRUD')->setModel('Model_Productos');
    }
}