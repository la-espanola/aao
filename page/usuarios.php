<?php
class page_usuarios extends Page {
	function init(){
        parent::init();
        
        $this->add('H1')->set('Usuarios');
        
        $crud=$this->add('CRUD');
        $model=$this->add('Model_Usuarios');
        $crud->setModel($model);
        
        if ($crud->grid) {
	        $crud->grid->addColumn('prompt','password');
	        
	        if ($_GET['password']) {
	        	$auth=$this->add('BasicAuth');
	        	$auth->usePasswordEncryption('sha1');
	        	$auth->setModel('Model_Usuarios');
	        	$auth->model->load($_GET['password']);
		        $auth->model['password']=$_GET['value'];
		        $auth->model->save();
		        $crud->js()->univ()->successMessage('Contraseña actualizada')->execute();
	        }
        }
    }
}