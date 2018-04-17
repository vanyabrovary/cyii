<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StrategySet extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_set';
    }


    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string', 'min' => 1,  'max' => 255],

            // #### safe
            [['id','name'], 'safe'],

            // #### 'required'
            ['name','required'],

            // #### unique
            [['name'], 'unique', 'targetAttribute' => ['name']]

        ];
    }


    public function getStrategyToSet()
    {
        return $this->hasMany(StrategyToSet::className(), ['strategy_set_id' => 'id']);
    }

    public function getStrategy()
    {
        #return [1,2,3,4,5];
        return $this->hasMany(Strategy::className(), ['id' => 'strategy_id'])->viaTable('strategy_to_set', ['strategy_set_id' => 'id']);
    }



    public function getContragents()
    {
        $m = call_user_func('app\models\Filters::find');
        return $m->andWhere([ 'strategy_set_id' => $this->id, 'uid' => 'strategy_set', 'variable_value' => 'dp.ContragentID' ])->asArray()->all();
    }

}
