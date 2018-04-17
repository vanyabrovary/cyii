<?php
namespace app\lib\WReq;

use app\lib\WReq;
use app\lib\WRedis;

class StrategyCases extends WReq
{

    public function __construct($data = NULL ) {
        $this->auth    = 1;
        $this->strict  = 1;
        $this->debug   = 1;
        $this->timeout = 1800;

        $this->url     = 'http://gw.sb.ua:8082/strategy-cases';

        if( $data ) {
            $data = str_replace('"[', '["', $data);
            $data = str_replace(']"', '"]', $data);

    	    $this->data = $data;
        }

    }

// 1. Логи
// БД
// Процесс = log
// Таблица = case
// Дата = YYYY-mm-dd
// Сообщение A | D | !
// Дело:case_id
// Контрагент:contragent_id
// Инструмент:task_type_id
// Cтратегия:strategy_id:strategy_id
// Такитка:strategy_item_id
// Дата создания:todo_at
// Дата отправки:todo_at

}

?>
