<?php
namespace app\lib\WReq;

use app\lib\WReq;

class TaskForDate extends WReq
{

    public function __construct($date = NULL) {
        $this->auth    = 0;
        $this->url     = 'https://sb.ssh.in.ua/v2/Task/whereview/eq.todo_last:'.$date;
    }
}

?>
