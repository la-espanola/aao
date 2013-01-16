<?php
class page_test_model_producto extends Page_Tester {
	  public $proper_responses=array(
          'Test_OK'=>'kk'  
      );
      
      function Prepare() {
	      $p=$this->add('Model_Producto');
	      return array($p); 
      }
      
      function test_OK($p) {
      	  $p['descripcion']='kk';
	      $de=$p['descripcion'];
	      return $de;
      }
      
}