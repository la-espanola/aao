<?php
require_once('RestClient.php');
class Model_ClientesProveedores extends Model_Table {
    public $table='clientesproveedores';
    
    function init() {
        parent::init();
        
        $this->addfield('name')->
            mandatory('No podemos tener clientes o proveedores sin nombre')->
            caption('Nombre')->searchable(true);
        $this->addfield('tipo')->
            setValueList(array('C'=>'Cliente','P'=>'Proveedor'))->
            mandatory('Campo requerido')->
            caption('Tipo')->sortable(true);
        $this->addfield('exportacion')->
            setValueList(array('S'=>'Sí','N'=>'No'))->
            mandatory('Campo requerido')->
            caption('Exportación');
        $this->addfield('envasadora')->
            setValueList(array('S'=>'Sí','N'=>'No'))->
            mandatory('Campo requerido')->
            caption('Envasadora');
        $this->addfield('codigo_erp')->
            caption('Código ERP');
    }
    
    public function ImportarDeERP($pagina=1) {
        $restcli=new ESPANOLAserverRestClient();
        $result=$restcli->ExportarClientesProveedores($pagina);
        
        if (sizeof($result)==0) return false;
        
        foreach ($result as $cliprov) {
            //$expr=$this->dsql()->where('codigo_erp',$cliprov->codigo)->where('tipo',$cliprov->tipo);
            $this->tryLoadBy($this->dsql()->expr('codigo_erp='.$cliprov->codigo.' and tipo=\''.$cliprov->tipo.'\''));
            $this['name'] = $cliprov->nombre;
            $this['codigo_erp'] = $cliprov->codigo;
            $this['tipo'] = $cliprov->tipo;
            $this['exportacion'] = $cliprov->exportacion;
            $this->save();
            $this->unload();
        }
    
        return true;
    }
    
}