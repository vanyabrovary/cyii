<?php
namespace app\lib\WReq;

use app\lib\WReq;
use app\lib\WRedis;
use app\lib\WQuery;

class StrategyCase extends WReq
{

    public function __construct($id = NULL) {
        $this->auth    = 1;
        $this->strict  = 0;
        $this->debug   = 1;
        $this->url     = 'http://gw.sb.ua:8082/strategy-cases';
        if( $id ) {
            $this->data ='{"filter":{"0": "=", "1": "dc.ID", "2": '.$id.'},"columns":{"test": "test"}}';
            #.json_encode( (new WQuery())->filters_distinct_variable_value() ) .
        }

    }
}

?>
