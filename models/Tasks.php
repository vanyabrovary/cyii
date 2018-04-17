<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Tasks extends ActiveRecord
{
        public static function tableName()
    {
        return 'tasks';
    }

    public function rules()
    {
    return [
            [['strategy_set_id'], 'each', 'rule' => ['integer']],
            [['strategy_set_id','strategy_id','strategy_item_id','task_type_id','case_id','contragent_id','priority','todo_at','created_at','updated_at','todo_last','todo_taken','is_not'], 'safe'],
    ];
}

}


