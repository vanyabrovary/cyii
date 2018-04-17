<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StrategySetFilter extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_set_filter';
    }


    public function rules()
    {
        return [
            // #### fields
            [['id', 'strategy_set_id'], 'integer'],

            [['strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value'], 'string', 'max' => 255],

            ['is_public', 'boolean', 'trueValue' => true, 'falseValue' => false],

            ['is_or', 'boolean', 'trueValue' => true, 'falseValue' => false],

            // #### safe
            [['id', 'is_public','strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value', 'is_or'], 'safe'],

            // #### 'required'
            [['strategy_set_id', 'strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value'], 'required'],

            // #### unique
            [['strategy_filter_name', 'strategy_filter_value', 'strategy_variable_value', 'strategy_set_id'], 'unique', 'targetAttribute' => ['strategy_filter_name', 'strategy_filter_value', 'strategy_variable_value', 'strategy_set_id']]
        ];
    }
}
