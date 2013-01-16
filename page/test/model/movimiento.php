<?php
class page_test_model_movimiento extends Page_Tester {
	  public $proper_responses=array(
          'Test_camposOK'=>'kk'  
      );
      
      function Prepare() {
	      $p=$this->add('Model_Movimiento');
	      return array($p); 
      }
      
      function test_camposOK($p) {
	      return 'kk';
      }
}