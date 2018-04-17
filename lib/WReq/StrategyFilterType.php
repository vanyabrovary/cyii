<?php
namespace app\lib\WReq;

use app\lib\WReq;

class StrategyFilterType extends WReq
{

    public function __construct($data = NULL) {
        $this->auth = 1;

        $this->url  =  'http://gw.sb.ua:8082/strategy-filter-type';

    }

}

?>