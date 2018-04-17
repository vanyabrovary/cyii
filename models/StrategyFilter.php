<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StrategyFilter extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_filter';
    }


    public function rules()
    {
        return [
            // #### fields
            [['id', 'strategy_id'], 'integer'],
            [['strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value'], 'string', 'max' => 255],

            ['is_public', 'boolean', 'trueValue' => true, 'falseValue' => false],

            ['is_or', 'boolean', 'trueValue' => true, 'falseValue' => false],

            // #### safe
            [['id', 'is_public','strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value', 'is_or'], 'safe'],

            // #### 'required'
            [['strategy_id', 'strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value'], 'required'],

            // #### unique
            [['strategy_filter_name', 'strategy_filter_value', 'strategy_variable_value', 'strategy_id'], 'unique', 'targetAttribute' => ['strategy_filter_name', 'strategy_filter_value', 'strategy_variable_value', 'strategy_id']]
        ];
    }


    public function getStrategy()
    {
        return $this->hasOne(Strategy::className(), ['id' => 'strategy_id']);
    }
}
