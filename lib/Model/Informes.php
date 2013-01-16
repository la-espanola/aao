<?php
class Model_Informes extends Model_Table {
    public $table='informes';
    protected $movim;
    
    function init() {
        parent::init();
        $this->addField('ejercicio')->system(true);
        $this->addField('mes')->system(true);
        $this->addField('tipo')->enum(array('T','E'))->system(true);
        $this->addField('apartado');
        $this->hasOne('Variedades');
        $this->hasOne('Destinos');
        $this->addField('kilos')->type('money');
        $this->movim=$this->add('Model_Movimientos');
    }
    
    public function CargarInforme($tipo, $ejercicio, $mes) { 
    	$this->deleteAll();
    
           
        if ($tipo=='T') return $this->CargarInformeTransformacion($ejercicio, $mes);
        else if ($tipo=='E') return $this->CargarInformeEnvasado($ejercicio, $mes);
        else return false;
    }    
     
    protected function CargarInformeTransformacion($ejercicio, $mes) { 
        $var=$this->add('Model_Variedades');
        $des=$this->add('Model_Destinos');
        foreach ($var as $variedad) {
	        foreach ($des as $destino) {
		        //Entradas Crudas
		        $kilosCruda=$this->movim->CalcularTotal('C', 'E', $ejercicio, $mes, $variedad['id'], $destino['id'],'C');
		        $kilosCruda+=$this->movim->CalcularTotal('T','E', $ejercicio, $mes, $variedad['id'], $destino['id'],'C');
		        
		        //Entradas Transformadas
		        $kilosTrans=$this->movim->CalcularTotal('C', 'E', $ejercicio, $mes, $variedad['id'], $destino['id'],'T');
		        $kilosTrans+=$this->movim->CalcularTotal('T','E', $ejercicio, $mes, $variedad['id'], $destino['id'],'T');
		       		        
		        //Entradas Totales
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosCruda+$kilosTrans;
		        $this['tipo']='T';
		        $this['apartado']='Entradas mes';
		        
		        //Entradas Crudas
		        $this->saveAndUnload();  
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosCruda;
		        $this['tipo']='T';
		        $this['apartado']='---Crudas';
		        $this->saveAndUnload();
		        //Entradas Transformadas
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosTrans;
		        $this['tipo']='T';
		        $this['apartado']='---Transformadas';
		        $this->saveAndUnload();
	        }
	        
        }
        return true;
    }
    
    protected function CargarInformeEnvasado($ejercicio, $mes) { 
        
        return false;    
    }
      
}