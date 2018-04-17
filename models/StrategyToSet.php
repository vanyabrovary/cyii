<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StrategyToSet extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_to_set';
    }


    public function rules()
    {
        return [
            [ ['id','strategy_id','strategy_set_id'], 'integer'],

            // #### safe
            [['id','strategy_id','strategy_set_id'], 'safe'],

            // #### 'required'
            [['strategy_id', 'strategy_set_id'], 'required'],

            // #### unique
            [['strategy_id', 'strategy_set_id'], 'unique', 'targetAttribute' => ['strategy_id', 'strategy_set_id']]

        ];
    }


    public function getStrategy()
    {
        return $this->hasMany(Strategy::className(), ['id' => 'strategy_id']);
    }


    public function getStrategySet()
    {
        return $this->hasMany(Strategy::className(), ['strategy_set_id' => 'id']);
    }
}
