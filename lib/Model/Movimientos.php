<?php
require_once('RestClient.php');
class Model_Movimientos extends Model_Table {
    public $table='movimientos';
    
    function init() {
        parent::init();
        $this->addField('ejercicio');
        $this->addField('mes');
        $this->addField('fecha');
        $this->addField('kilos_originales');
        $this->addField('factor');
        $this->addField('kilos_convertidos');
        $this->addField('codigo_erp')->sortable(true);
        $this->addField('entrada_salida')->enum(array('E','S'));
        $this->hasOne('Operaciones');
        $this->hasOne('Variedades');
        $this->hasOne('Estados');
        $this->hasOne('Destinos');
        $this->hasOne('ClientesProveedores');
    }
    
    public function CalcularTotal ($operacion, $entrada_salida, $ejercicio, $mes, $variedad, $destino, $estado) {
    	$this->initQuery();
    	$this->addCondition('operaciones_id','=',$operacion);
    	$this->addCondition('ejercicio','=',$ejercicio);
    	$this->addCondition('mes','=',$mes);
    	$this->addCondition('variedades_id','=',$variedad);
    	$this->addCondition('destinos_id','=',$destino);
    	$this->addCondition('estados_id','=',$estado);
    	$this->addCondition('entrada_salida','=',$entrada_salida);
    	
	    return $this->dsql()
	    	->field($this->dsql()->expr('sum(kilos_originales)'),'total_kilos')
	    	->getOne();
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
        
        foreach ($result as $compra) {
            $this['ejercicio'] = $ejercicio;
            $this['mes'] = $mes;
            $this['fecha'] = $compra->fecha;
            $this['kilos_originales'] = $compra->kilos;
            $this['entrada_salida'] = $compra->entrada_salida;
            $this['factor'] = 0;
            $this['kilos_convertidos'] = 0;
            $this['estados_id'] = $compra->estado;
            $this['variedades_id'] = $compra->variedad;
            $this['destinos_id'] = $compra->destino;
            $this['operaciones_id'] = $compra->operacion;
            $reg=$proveedor->tryLoadBy('codigo_erp','=',$compra->clienteproveedor);
            $proveedor->tryLoadBy($this->dsql()->expr('codigo_erp='.$compra->clienteproveedor.' and tipo=\'P\''));
            if (!empty($reg)) $this['clientesproveedores_id']=$reg['id'];
            else throw new Excepcion('Proveedor no encontrado');
            $this['codigo_erp'] = $compra->codigo_erp;
            $this['fecha'] = $compra->fecha->date;
            $this->save();
            $this->unload();
        }
    
        return true;
    }
      
}