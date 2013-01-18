<?php
class MyUtils extends AbstractObject {

	public function getMesPasado() {
		$mes=date('m');
        $ejer=date('Y');
        
        if ($mes==1) {
	        $ejer--;
	        $mes=12;
        }
        else $mes--;
        return array('ejercicio'=>$ejer, 'mes'=>$mes);
	} 

}