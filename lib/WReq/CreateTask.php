<?php
namespace app\lib\WReq;

use app\lib\WReq;

class CreateTask extends WReq
{
    public function __construct($data = NULL) {
        if(!$data) return 0;
        $this->auth     = 1;
        $this->strict   = 1;
        $this->url      = 'http://gw.sb.ua:8082/create-task';
        if( $this->_is_json($data) ) {
            $this->data = $data;

        } else {
            $this->data = '{"TemplateID":20, "CaseID":'.$data.' }';

        }
    }

    private function _is_json($str) {
        return json_decode($str) != null;

    }

}

?>
