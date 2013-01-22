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
        $this->dsql()->order('ejercicio,mes,variedades_id,id,destinos_id');
        
        $this->movim=$this->add('Model_Movimientos');
    }
    
    public function FiltrarDatos($tipo, $ejercicio, $mes, $variedad) {
	    $this->initQuery();
    	$this->addCondition('ejercicio','=',$ejercicio);
    	$this->addCondition('mes','=',$mes);
    	$this->addCondition('tipo','=',$tipo);
    	$this->addCondition('variedades_id','=',$variedad);
    	$this->dsql()->order('destinos_id desc');
    	return $this;

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
		        $kilosCruda=$this->movim
		        	->CalcularTotal('C', 'E', $ejercicio, $mes, $variedad['id'], $destino['id'],'C');
		        $kilosCruda+=$this->movim
		        	->CalcularTotal('T','E', $ejercicio, $mes, $variedad['id'], $destino['id'],'C');
		        
		        //Entradas Transformadas
		        $kilosTrans=$this->movim
		        	->CalcularTotal('C','E', $ejercicio, $mes, $variedad['id'], $destino['id'],'T');
		        $kilosTrans+=$this->movim
		        	->CalcularTotal('T','E', $ejercicio, $mes, $variedad['id'], $destino['id'],'T');
		       		        
		        //Entradas Totales
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosCruda+$kilosTrans;
		        $this['tipo']='T';
		        $this['apartado']='Entradas mes';
		        $this->saveAndUnload(); 
		        //Entradas Crudas 
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
		        
		        //Ventas Granel Crudas
		        $kilosCruda=$this->movim
		        	->CalcularTotal('V','S', $ejercicio, $mes, $variedad['id'], $destino['id'],'C');
		        //Ventas Granel Transformadas
		        $kilosTrans=$this->movim
		        	->CalcularTotal('V','S', $ejercicio, $mes, $variedad['id'], $destino['id'],'T');
		        //Envasadora propia
		        $kilosEnvProp=$this->movim
		        	->CalcularTotal('E','S', $ejercicio, $mes, $variedad['id'], $destino['id'],'T','P');
		        //Envasadora propia
		        $kilosEnvExt=$this->movim
		        	->CalcularTotal('E','S', $ejercicio, $mes, $variedad['id'], $destino['id'],'T','E');
		        //Mermas
		        $kilosMermas=$this->movim
		        	->CalcularTotal('M','S', $ejercicio, $mes, $variedad['id'], $destino['id'],'T');
		        
		        //Salidas Totales
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosCruda+$kilosTrans+$kilosEnvProp+$kilosEnvExt+$kilosMermas;
		        $this['tipo']='T';
		        $this['apartado']='Salidas mes';
		        $this->saveAndUnload(); 
		        //Ventas Crudas 
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosCruda;
		        $this['tipo']='T';
		        $this['apartado']='---Crudas';
		        $this->saveAndUnload();
		        //Ventas Transformadas
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosTrans;
		        $this['tipo']='T';
		        $this['apartado']='---Transf. otras ind.';
		        $this->saveAndUnload();
		        //Salidas Envasado propias
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosEnvProp;
		        $this['tipo']='T';
		        $this['apartado']='---T. envas. propia';
		        $this->saveAndUnload();
		        //Salidas Envasado propias
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosEnvExt;
		        $this['tipo']='T';
		        $this['apartado']='---T. otras. envas.';
		        $this->saveAndUnload();
		        //Salidas Mermas
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosMermas;
		        $this['tipo']='T';
		        $this['apartado']='---Mermas';
		        $this->saveAndUnload();
		        
		        
	        }
	        
        }
        return true;
    }
    
    protected function CargarInformeEnvasado($ejercicio, $mes) { 
        $var=$this->add('Model_Variedades');
        $des=$this->add('Model_Destinos');
        foreach ($var as $variedad) {
	        foreach ($des as $destino) {
		       	//Envasadora propia
		        $kilosEnvProp=$this->movim
		        	->CalcularTotal('E','S', $ejercicio, $mes, $variedad['id'], $destino['id'],'T','P');
		        //Entradas Totales
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosEnvProp;
		        $this['tipo']='E';
		        $this['apartado']='Entradas mes';
		        $this->saveAndUnload();
		        //Entradas Env. Propia
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=$kilosEnvProp;
		        $this['tipo']='E';
		        $this['apartado']='---De entam. propia';
		        $this->saveAndUnload();
		        //Entradas Totales
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=0;
		        $this['tipo']='E';
		        $this['apartado']='---De otras entam/op.';
		        $this->saveAndUnload();
		        //Entradas Totales
		        $this['ejercicio']=$ejercicio;  
		        $this['mes']=$mes;  
		        $this['variedades_id']=$variedad['id'];  
		        $this['destinos_id']=$destino['id'];  
		        $this['kilos']=0;
		        $this['tipo']='E';
		        $this['apartado']='---De otras env.';
		        $this->saveAndUnload();
		        		        
	        }
	        
        }

        return true;    
    }
      
}