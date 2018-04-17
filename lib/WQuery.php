<?php
namespace app\lib;

use Yii;
use yii\db\Query;

class WQuery
{

    public function strategy_set_task($strategy_set_id = null) {
        return  (new Query())->select("SELECT * from strategy_sets JOIN task ON task.strategy_id = strategy_sets.strategy_id  WHERE strategy_sets.strategy_set_id = $strategy_set_id")->all();

    }


    public function filters_distinct_variable_value(){
        return (new Query())->select("DISTINCT ON (variable_value) variable_value")->from("filters")->all();

    }


    public function schedule_working_date($date){
        return (new Query())->select("date")->from("schedule")->where("date >= '$date' AND is_working = 'true'")->orderBy("date")->limit('1')->one();

    }


    public function task(){
        return (new Query())->select("id, todo_at")->from("task")->all();

    }


    public function task_fc_truncate(){
        return Yii::$app->db->createCommand()->truncateTable('task_fc')->execute();

    }

    public function task_count(){

        return  (new Query())->select("count(id) as cnt from task")->one();

    }

    public function taskfc_count(){

        return  (new Query())->select("count(id) as cnt from task_fc")->one();

    }



    public function update_strategy_pause(){
        Yii::$app->db->createCommand("UPDATE strategy_pause SET updated_at = now()");
    }



    public function filters_strategy_item($is_not = null){
        return Yii::$app->db->createCommand('SELECT DISTINCT item_id, is_main, priority FROM filters WHERE item_id IS NOT NULL ORDER BY is_main, priority, item_id')->queryAll();

    }

}

?>