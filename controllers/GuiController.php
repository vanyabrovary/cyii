<?php
namespace app\controllers;
use Yii;
use yii\web\{Controller,Response};

class GuiController extends Controller
{

    public function actionRun()
    {
        if( $this->actionStatus()){
            return "PROGRESS";

        }

        if( $this->actionStatus() < 1) {
            /* /usr/bin/php /var/www/host/app/yii cli-task/make-all make 5 >> /var/www/sb.ssh.in.ua/app/web/api/cases.log & */

            passthru($this->_cli_cmd().' '.$this->_cli_make_task_cmd().' make '.$this->_cli_fc_days().' >> '.$this->_cli_log_file().' &');
            return "START";

       }

    }


    public function actionRuntask()
    {
        /* /usr/bin/php /var/www/host/app/yii cli-task/send-task-all-at-one >> /var/www/sb.ssh.in.ua/app/web/api/cases.log & */

        passthru($this->_cli_cmd().' '.$this->_cli_send_task_cmd().' >> '.$this->_cli_send_task_log_file().' &');
        return "START";

    }


    public function actionKill()
    {
        /* ps aux |grep cli-task/make-all |awk '{print \$2}' |xargs kill -9 */

        exec("ps aux |grep ".$this->_cli_make_task_cmd()." |awk '{print \$2}' |xargs kill -9");
        return $this->actionStatus();

    }


    public function actionStatus() :int
    {
        /* ps aux |grep cli-task/make-all |wc -l */

        $p_cnt = exec('ps aux |grep '.$this->_cli_make_task_cmd().' |wc -l'); settype( $p_cnt, "integer" ); $p_cnt = $p_cnt - 2;

        if($p_cnt > 0 ){
            return 1;
        } else {
            return 0;
        }

    }


    public function actionClearfc()
    {
        /* truncate table task_fc */

        Yii::$app->db->createCommand()->truncateTable('task_fc')->execute();
        print 'OK';

    }


    public function actionClear()
    {
        Yii::$app->db->createCommand('DELETE from task where todo_at = now()::date')->execute();
        print 'OK';

    }


    private function _cli_cmd(){
        return '/usr/bin/php '.Yii::$app->basePath.'/yii';

    }


    private function _cli_log_file(){
        return Yii::$app->basePath.'/runtime/logs/gui_controller_make_task'. date("Y-m-d") .'.log';

    }


    private function _cli_send_task_log_file(){
        return Yii::$app->basePath.'/runtime/logs/gui_controller_send_task_'. date("Y-m-d") .'.log';

    }



    private function _cli_fc_days(){
        return Yii::$app->params['def']['fc_days_number'];

    }


    private function _cli_make_task_cmd(){
        return Yii::$app->params['def']['cli_make_task_cmd'];

    }


    private function _cli_send_task_cmd(){
        return Yii::$app->params['def']['cli_send_task_cmd'];

    }


}
