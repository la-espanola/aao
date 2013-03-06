<?php
class Page_logout extends Page {
	function init() {
		parent::init();
		
		$this->api->auth->logout();
		$this->add('P')->set('Hasta Pronto');
	}
}