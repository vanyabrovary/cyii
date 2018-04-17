<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StrategyItemFilter extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_item_filter';
    }


    public function rules()
    {
        return [
            // #### fields
            [['id', 'strategy_item_id'], 'integer'],

            [['strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value'], 'string', 'max' => 255],

            ['is_public',   'boolean', 'trueValue' => true, 'falseValue' => false],
            ['is_segment',  'boolean', 'trueValue' => true, 'falseValue' => false],
            ['is_or',       'boolean', 'trueValue' => true, 'falseValue' => false],

            // #### safe
            [['id', 'is_public','is_segment','strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value', 'is_or'], 'safe'],

            // #### 'required'
            [['strategy_item_id', 'strategy_variable_value', 'strategy_filter_name', 'strategy_filter_value'], 'required'],

            // #### unique
            [['strategy_filter_name', 'strategy_filter_value', 'strategy_variable_value', 'strategy_item_id'], 'unique', 'targetAttribute' => ['strategy_filter_name', 'strategy_filter_value', 'strategy_variable_value', 'strategy_item_id']]
        ];
    }


    public function getStrategyItem()
    {
        return $this->hasOne(StrategyItem::className(), ['id' => 'strategy_item_id']);
    }
}
