<?php
/**
 * @service ESPANOLAserverRestClient
 */
 
class ESPANOLAserverRestClient{
    /**
     * The WSDL URI
     *
     * @var string
     */
    public static $_WsdlUri='http://192.168.44.13:1111/servicios/server.php?WSDL';
    /**
     * The PHP SoapClient object
     *
     * @var object
     */
    public static $_Server=null;
    /**
     * The endpoint URI
     *
     * @var string
     */
    public static $_EndPoint='http://192.168.44.13:1111/servicios/server.php';

    /**
     * Send a SOAP request to the server
     *
     * @param string $method The method name
     * @param array $param The parameters
     * @return mixed The server response
     */
    public static function _Call($method,$param){
        $keys=array_keys($param);
        $i=-1;
        $len=sizeof($keys);
        while(++$i<$len)
            $method=str_replace(" ".$keys[$i]."/",urlencode($param[$keys[$i]])."/",$method);
        $context = stream_context_create(array(
        	'http' => array(
        	'header'  => "Authorization: Basic " . base64_encode("test:test")
        	)
        ));
        return file_get_contents(self::$_EndPoint.$method, false, $context);
    }

    /**
     * @param int $pagina
     * @return ClienteProveedor
     */
    public function ExportarClientesProveedores($pagina){
        return json_decode(self::_Call('/ExportarClientesProveedores/ pagina/',Array(
            'pagina'=>$pagina
        )));
    }

    public function ExportarArticulos($pagina){
        return json_decode(self::_Call('/ExportarArticulos/ pagina/',Array(
            'pagina'=>$pagina
        )));
    }

    public function ExportarArticulosPT($pagina){
        return json_decode(self::_Call('/ExportarArticulosPT/ pagina/',Array(
            'pagina'=>$pagina
        )));
    }

    public function ExportarMovimientosAAO($operacion, $ejercicio, $mes, $centro, $pagina){
        return json_decode(self::_Call('/ExportarMovimientosAAO/ operacion/ ejercicio/ mes/ centro/ pagina/',Array(
        	'operacion'=>$operacion,
            'ejercicio'=>$ejercicio,
            'mes'=>$mes,
            'centro'=>$centro,
            'pagina'=>$pagina
        )));
    }

    /**
     * @param int $mes
     * @param int $anyo
     * @param int $factoria
     * @return string
     */
    public function ExportarMes($mes,$anyo,$factoria){
        return self::_Call('/ExportarMes/ mes/ anyo/ factoria/',Array(
            'mes'=>$mes,
            'anyo'=>$anyo,
            'factoria'=>$factoria
        ));
    }
}

class ClienteProveedor{
    /**
     * @var int
     */
    public $tipo;
    /**
     * @var int
     */
    public $codigo;
    /**
     * @var int
     */
    public $nombre;
    /**
     * @var int
     */
    public $exportacion;
}