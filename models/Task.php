<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Task extends ActiveRecord
{

  //todo_at closed_at  created_at  updated_at

    public static function tableName()
    {
        return 'task';
    }


    public function rules()
    {
        return [
            // #### fields
            [['id', 'strategy_id', 'strategy_item_id', 'task_type_id', 'case_id', 'contragent_id', 'priority'], 'integer'],
            // #### bool
            [['is_not'], 'boolean', 'trueValue' => true, 'falseValue' => false],

            [['todo_taken'], 'string', 'max' => 255],

            // #### safe
            [['id', 'strategy_id', 'strategy_item_id', 'task_type_id', 'case_id', 'contragent_id', 'priority', 'todo_at', 'todo_last', 'is_not'], 'safe'],

            // #### 'required'
            [['strategy_id', 'strategy_item_id', 'task_type_id', 'case_id', 'contragent_id','is_not'], 'required'],

            // #### unique
            [['case_id', 'contragent_id', 'strategy_item_id', 'strategy_id', 'task_type_id', 'todo_at','is_not'], 'unique', 'targetAttribute' => ['case_id', 'contragent_id', 'strategy_item_id', 'strategy_id', 'task_type_id', 'todo_at', 'is_not']]
        ];
    }


    public function getStrategy()
    {

        return $this->hasOne(Strategy::className(), ['strategy_id' => 'id']);

    }


    public function getStrategyItem()
    {

        return $this->hasOne(StrategyItem::className(), ['strategy_item_id' => 'id']);

    }


}
