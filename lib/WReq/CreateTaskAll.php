<?php
namespace app\lib\WReq;

use app\lib\WReq;

class CreateTask extends WReq
{

    public function __construct($id = NULL) {

        if(!$id) return 0;

        $this->auth    = 1;
        $this->strict  = 1;
        $this->url     = 'http://gw.sb.ua:8082/create-task';
        $this->data    = '{"TemplateID":20, "CaseID":'.$id.' }';
    }
}

?>
