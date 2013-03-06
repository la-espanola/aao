<?php
class Model_Productos extends Model_Table {
    public $table='productos';
    
    function init() {
        parent::init();
        $this->hasOne('Variedades')->caption('Variedad')->sortable(true)->editable(false);
        $this->hasOne('Estados')->caption('Estado')->sortable(true)->editable(false);
        $this->hasOne('Destinos')->caption('Destino')->sortable(true)->editable(false); 
        $this->hasOne('Procesados')->caption('Procesado')->sortable(true)->editable(false); 
        $this->addField('factor_actual')->caption('Factor Actual')->mandatory('El factor de conversión es un dato obligatorio'); 
        $this->addField('name')->system(true);
        $this->addHook('beforeSave',$this);
    }
   
    function beforeSave() {
	    $this['name']=$this['variedades'].' '.$this['estados'].' '.$this['destinos'].' '.$this['procesados'];
    }
   
    public function ImportarDeERP($pagina=1) {
        $restcli=new ESPANOLAserverRestClient();
        $result=$restcli->ExportarArticulos($pagina);
        
        if (sizeof($result)==0) return false;
        
        foreach ($result as $articulo) {
            //$expr=$this->dsql()->where('codigo_erp',$cliprov->codigo)->where('tipo',$cliprov->tipo);
            $this->tryLoadBy($this->dsql()->expr('variedades_id=\''.$articulo->variedad.'\' and estados_id=\''.$articulo->estado.'\'
            and destinos_id=\''.$articulo->destino.'\' and procesados_id=\''.$articulo->procesado.'\''));
            $this['variedades_id'] = $articulo->variedad;
            $this['estados_id'] = $articulo->estado;
            $this['destinos_id'] = $articulo->destino;
            $this['procesados_id'] = $articulo->procesado;
            $this->save();
            $this->unload();
        }
    
        return true;
    }
    
    public function ImportarPTDeERP($pagina=1) {
        $restcli=new ESPANOLAserverRestClient();
        $result=$restcli->ExportarArticulosPT($pagina);
        
        if (sizeof($result)==0) return false;
        
        foreach ($result as $articulo) {
            //$expr=$this->dsql()->where('codigo_erp',$cliprov->codigo)->where('tipo',$cliprov->tipo);
            $this->tryLoadBy($this->dsql()->expr('variedades_id=\''.$articulo->variedad.'\' and estados_id=\''.$articulo->estado.'\'
            and destinos_id=\''.$articulo->destino.'\' and procesados_id=\''.$articulo->procesado.'\''));
            $this['variedades_id'] = $articulo->variedad;
            $this['estados_id'] = $articulo->estado;
            $this['destinos_id'] = $articulo->destino;
            $this['procesados_id'] = $articulo->procesado;
            $this->save();
            $this->unload();
        }
    
        return true;
    }

    
}