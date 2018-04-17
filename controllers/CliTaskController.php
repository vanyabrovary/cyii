<?php
namespace app\controllers;

use Yii;
use yii\console\Controller;

use app\lib\{WQuery,WRedis};
use app\lib\WReq\{StrategyCases,CreateTask,TaskForDate};
use app\models\{Task,StrategyItem};

class CliTaskController extends Controller
{

    /*
       Make all tasks
       Take two parameters
       action (str)
       make - make tasks
       show - show active strategy_item ids
       fc (int) - forecase days number
    */

    public function actionMakeAll( $action = 'make',  $fc = 0 )
    {
        if( $action == 'make' ) ( new WQuery() )->task_fc_truncate();
        if( $action == 'make' ) ( new WQuery() )->update_strategy_pause();

        foreach ( (new WQuery())->filters_strategy_item() as $item) {

            if ( $action == 'make' ) {
                for ($day = 0; $day <= $fc; $day++) {
                    $this->actionMake($item['item_id'], $day);
                }

            }

            if ( $action == 'show' ) {
                print $item['item_id']."\n";

            }

        }

    }

    /*
        Make tasks for strategy_item
        Take two parameters
        strategy_item_id (int)
        fc (int) - forecase day number
    */*


    public function actionMake($strategy_item_id, $day = 0)
    {
        print "\n START FOR $strategy_item_id \n";

        $s_item = StrategyItem::findOne( $strategy_item_id );

        if(!isset($s_item->id)) return 'Bad strategy_item_id';

        $s_item->f_add_day = $day;

        $res = (new StrategyCases( $s_item->makeFilters() ) )->do( $s_item->redis_store_key() )->result;

        if ( gettype($res) != 'array' ){
            $this->l('"ERROR":"wantarray from worker!"');
            return 0;

        }

        foreach ( $res as $task ) {
            if (!isset($task['ID']))           { $this->l("BAD CaseID:       ".var_dump($task)); continue; }
            if (!isset($task['ContragentID'])) { $this->l("BAD ContragentID: ID:". $task["ID"]); continue; }
            $item = [
                "case_id"          => $task["ID"],
                "contragent_id"    => $task['ContragentID'],
                "priority"         => $s_item->strategy->priority,
                "task_type_id"     => $s_item->task_type_id,
                "strategy_id"      => $s_item->strategy_id,
                "strategy_item_id" => $s_item->id,
                "todo_at"          => $s_item->todo_at(),
                "todo_last"        => $s_item->todo_last(),
                "is_not"           => $s_item->strategy->is_not
            ];
            if ( $s_item->f_add_day == 0 ) { $this->actionMakeTask($item,'Task');   }
            if ( $s_item->f_add_day  > 0 ) { $this->actionMakeTask($item,'TaskFc'); }
        }

        print "\n FINISH FOR $strategy_item_id \n";

    }


    private function actionMakeTask( $item, $type = 'Task' )
    {
        $type  = 'app\models\\'.$type;
        $model = new $type();
        $fresh = call_user_func( $type . '::find');
        $flag;

        if ( $fresh->where($item)->one() ) {
            $flag = '!';

        } else {
            $model->attributes  = $item;
            $model->save();
            $flag  = '+';

        }

        $this->l("'exists':'$flag','result':".json_encode($item, JSON_FORCE_OBJECT));

        return $flag;

    }


    // Send tasks to workwr

    public function actionSendTaskAllAtOne()
    {
        foreach ( ( new TaskForDate( date("Y-m-d") ) )->do()->result as $task ) {
            $result[] = [ "CaseID" => $task["case_id"], "TemplateID" => 20 ];
        }

        foreach ( ( new CreateTask( json_encode($result) ) )->do()->result  as $itm ) {
            Yii::$app->db->createCommand('UPDATE task SET todo_taken = now()::date WHERE id = '.$task["id"])->execute();
            $this->l("'worker':'send_task','case_id':".$task['case_id']);
        }

    }

    // Log to Redis and stderr

    private function l($text)
    {
        $log   = '"date":"' . date("Y-m-d H:i:s").'",'.$text."\n";
        $redis = new WRedis('task');
        $redis->log($log);
        print $log;

    }

}
