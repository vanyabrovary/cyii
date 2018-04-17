<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class TaskFc extends ActiveRecord
{

  //todo_at closed_at  created_at  updated_at

    public static function tableName()
    {
        return 'task_fc';
    }


    public function rules()
    {
        return [
            // #### fields
            [['id', 'strategy_id', 'strategy_item_id', 'task_type_id', 'case_id', 'contragent_id', 'priority'], 'integer'],
            // #### bool
            [['is_not'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            // #### safe
            [['id', 'strategy_id', 'strategy_item_id', 'task_type_id', 'case_id', 'contragent_id', 'priority', 'todo_at', 'todo_last','is_not'], 'safe'],

            // #### 'required'
            [['strategy_id', 'strategy_item_id', 'task_type_id', 'case_id', 'contragent_id','is_not'], 'required'],

            // #### unique
            [['case_id', 'contragent_id', 'strategy_item_id', 'strategy_id', 'task_type_id', 'todo_at','is_not'], 'unique', 'targetAttribute' => ['case_id', 'contragent_id', 'strategy_item_id', 'strategy_id', 'task_type_id', 'todo_at','is_not']]
        ];
    }
}
