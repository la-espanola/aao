<?php
require_once('RestClient.php');
class Model_Movimientos extends Model_Table {
    public $table='movimientos';
    
    function init() {
        parent::init();
        $this->addField('ejercicio')->required(true);
        $this->addField('mes')->required(true);
        $this->addField('fecha')->datatype('date')->required(true);
        $this->addField('kilos_originales')->required(true);
        $this->addField('factor')->editable(false);
        $this->addField('kilos_convertidos')->editable(false);
        $this->addField('codigo_erp')->sortable(true)->defaultValue(-1)->editable(false);
        $this->addField('entrada_salida')
        	 ->setValueList(array('E'=>'Entrada','S'=>'Salida'))
        	 ->required(true)
        	 //->display('radio')
        	 ->defaultValue('E')
        	 ->emptyText(null);
        $this->hasOne('Operaciones')->required(true);
        $this->hasOne('Productos')->required(true);
        $this->hasOne('ClientesProveedores')->required(true);
        
        $this->addHook('beforeSave',function($m) {
	        $fecha=strtotime($this['fecha']);
        	if ($this['mes']!=date('n',$fecha) 
        		|| $this['ejercicio']!=date('Y',$fecha))
    	throw $m->exception('¡La fecha que has elegido no pertenece al mes y año en el que estamos trabajando!','ValidityCheck')->setField('fecha');
    	});
    }
  
    /**
    Devuelve el total de kilos_convertidos para los parámetros pasados. 
    **/
    public function CalcularTotal ($operacion, $entrada_salida, $ejercicio, $mes, $variedad, $destino, $estado,$tipo_envasadora=null,$exportacion=null) {
    	$this->initQuery();
    	$this->addCondition('operaciones_id','=',$operacion);
    	$this->addCondition('ejercicio','=',$ejercicio);
    	$this->addCondition('mes','=',$mes);
    	$this->addCondition('entrada_salida','=',$entrada_salida);
    	
    	$expr=$this->api->db->dsql()->table('productos')->field('productos.id')
    		->where('variedades_id',$variedad)
    		->where('destinos_id',$destino)
    		->where('estados_id',$estado);
    	$this->addCondition('productos_id','in',$expr);
    	
    	if (!empty($tipo_envasadora)) {
    		$expr=$this->api->db->dsql()->table('clientesproveedores')->field('clientesproveedores.id')->where('envasadora',$tipo_envasadora);
    		$this->addCondition('clientesproveedores_id','in',$expr);
	    	
    	}
    	if (!empty($exportacion)) {
    		$expr=$this->api->db->dsql()->table('clientesproveedores')->field('clientesproveedores.id')->where('exportacion',$exportacion);
    		$this->addCondition('clientesproveedores_id','in',$expr);
	    	
    	}
	    return $this->dsql()
	    	->field($this->dsql()->expr('sum(kilos_convertidos)'),'total_kilos')
	    	->getOne();
    }
    
    public function filtrarPorMesyTipo($ejercicio, $mes,$operacion) {
	    $this->initQuery();
    	$this->addCondition('ejercicio','=',$ejercicio);
    	$this->addCondition('mes','=',$mes);
    	$this->addCondition('operaciones_id','=',$operacion);
    	return $this;
    }
    
    public function ImportarDatosDeERP($operacion, $ejercicio, $mes, $centro) {
        if (!is_numeric($mes) || 
            !is_numeric($ejercicio) || 
            !is_numeric($centro)) return false;
        
        //obtenemos el último movimiento importado
        $q=$this->api->db->dsql();
        $maxCodigoErp=$q->table('movimientos')->
        	field($q->expr('max(codigo_erp)'),'max_codigo_erp')->
        	where('ejercicio',$ejercicio)->
        	where('mes',$mes)->
        	where('operaciones_id',$operacion)->
        	getOne();
        
        $restcli=new ESPANOLAserverRestClient();
        $result=$restcli->ExportarMovimientosAAO($operacion,$ejercicio, $mes, $centro, $maxCodigoErp);
        
        if (sizeof($result)==0) return false;
        
        $proveedor=$this->add('Model_ClientesProveedores');
        $producto=$this->add('Model_Productos');
        
        foreach ($result as $compra) {
            $this['ejercicio'] = $ejercicio;
            $this['mes'] = $mes;
            $this['fecha'] = $compra->fecha;
            $this['kilos_originales'] = $compra->kilos;
            $this['entrada_salida'] = $compra->entrada_salida;
            $this['factor'] = 0;
            $this['kilos_convertidos'] = 0;
            $this['operaciones_id'] = $compra->operacion;
           
            switch($operacion) {
	            case 'V': $tipoclicprov='C'; break;
	            case 'E': $tipoclicprov='C'; break;
	            case 'M': $tipoclicprov='C'; break;
	            case 'F': $tipoclicprov='C'; break;
	            default: $tipoclicprov='P';
            }
           
            $reg=$proveedor->tryLoadBy($this->dsql()->expr('codigo_erp='.$compra->clienteproveedor.' and tipo=\''.$tipoclicprov.'\''));
            if (!empty($reg)) $this['clientesproveedores_id']=$reg['id'];
            else throw new Excepcion('Proveedor no encontrado');
            
            $reg=$producto->tryLoadBy($this->dsql()->expr('variedades_id=\''.$compra->variedad.'\' and destinos_id=\''.$compra->destino.'\' and
            estados_id=\''.$compra->estado.'\' and procesados_id=\''.$compra->procesado.'\''));
            if (!$reg) $this['productos_id']=$reg['id'];
            else {
            	$producto['variedades_id']=$compra->variedad;
            	$producto['destinos_id']=$compra->destino;
            	$producto['estados_id']=$compra->estado;
            	$producto['procesados_id']=$compra->procesado;
            	$producto->save();
            	$this['productos_id']=$producto['id'];
            	$producto->unload();
            }      
            $this['codigo_erp'] = $compra->codigo_erp;
            $this['fecha'] = $compra->fecha->date;
            $this->save();
            $this->unload();
        }
    
        return true;
    }
    
    public function aplicarFactoresActualesaMes($ejercicio, $mes) {
    	$this->initQuery();
    	$this->addCondition('ejercicio','=',$ejercicio);
    	$this->addCondition('mes','=',$mes);
    	$expr=$this->api->db->dsql()
    		->table('productos','p')->field('factor_actual')
    		->where('p.id',$this->api->db->dsql()->expr('productos_id'));
    	$this->dsql()
    		->set('factor',$expr)
    		->set('kilos_convertidos',$this->dsql()->expr('kilos_originales*factor'))
    		->update();
    		
    		
	    return true;
    }    
    
    public function convertir($ejercicio, $mes, $origen, $destino) {
    	$movim=$this->add('Model_Movimientos');
    	$movim->addCondition('ejercicio','=',$ejercicio);
    	$movim->addCondition('mes','=',$mes);
    	$movim->addCondition('productos_id',$this->api->db->dsql()
    			->table('productos','p')->field('p.id')
    			->where('variedades_id',$origen['variedad'])
    			->where('estados_id',$origen['estado'])
    			->where('destinos_id',$origen['destino']));
    	$movim->debug();
    	$productos=$this->add('Model_Productos');
    	foreach ($movim as $registro) {
	    	$productoOriginal=$productos->tryLoad($movim['productos_id']);
	    	if ($productoOriginal) {
		    	$procesado=$productoOriginal['procesados_id'];
		    	$productoDestino=$productos->tryLoadBy($this->dsql()
		    	->expr('variedades_id=\''.$destino['variedad'].'\' and 
		    			destinos_id=\''.$destino['destino'].'\' and
		    			estados_id=\''.$destino['estado'].'\' and 
		    			procesados_id=\''.$procesado.'\''));
		    	if ($productoDestino) {		
		    		$movim['productos_id']=$productoDestino['id'];
		    		$movim->saveAndUnload();
		    	}
	    	}
    	}
	    return true;
    }  
}