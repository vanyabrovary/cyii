<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Strategy extends ActiveRecord
{
    public static function tableName() :string
    {
        return 'strategy';

    }


    public function rules() :array
    {
        return [
            ['id',            'integer'],
            ['priority',      'integer'],
            ['name',          'string',   'max' => 255],
            [ ['is_public',   'is_main', 'is_not'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            // #### safe

            [ ['id', 'is_public', 'is_main', 'is_not', 'priority'], 'safe'],
            // #### 'required'

            ['name','required'],
            // #### unique

            [ ['name'], 'unique', 'targetAttribute' => ['name'] ]
        ];
    }


    public function getStrategyItems()
    {
        return $this->hasMany(StrategyItem::className(), ['strategy_id' => 'id']);

    }


    ## don't know for. to del???
    public function getStrategyItemFilters()
    {
        return $this->hasMany(StrategyItemFilter::className(), ['strategy_id' => 'id']);

    }

    public function getStrategyPause()
    {
        return $this->hasMany(StrategyPause::className(), ['strategy_id' => 'id']);

    }


    ## strategy contragents only
    public function getContragents()
    {
        return $this->_filter('1')->asArray()->all();

    }


    ## strategy contragents only
    public function getContragentsAll()
    {
        return $this->_filter('2')->asArray()->all();

    }


    ## strategy filters for internal using
    public function getFilters()
    {
        return $this->_filter()->asArray()->all();

    }


    ## strategy filters OR only
    public function getFiltersOr()
    {
        return $this->_filter()->andWhere(["is_or" => 'true'])->andWhere(['not', ["uid"  => 'strategy_item']])->asArray()->all();

    }


    ## strategy filters AND only
    public function getFiltersAnd()
    {
        return $this->_filter()->andWhere(["is_or" => 'false'])->andWhere(['not', ["uid"  => 'strategy_item']])->asArray()->all();

    }


    ## strategy filters only
    private function _filter($with_contragents = null)
    {
        $m = call_user_func('app\models\Filters::find');

        $m->andWhere(['strategy_id' => $this->id]);

        if ( $with_contragents == 1) $m->andWhere(['uid' => 'strategy']);
        if ( $with_contragents == 1) $m->andWhere(["variable_value"  => 'dp.ContragentID']);

        if ( $with_contragents == 2) $m->andWhere(['uid' => 'strategy', 'uid' => 'strategy_set']);
        if ( $with_contragents == 2) $m->andWhere(["variable_value"  => 'dp.ContragentID']);

        if (!$with_contragents) $m->andWhere(['not', ["variable_value"  => 'dp.ContragentID']]);

        return $m;

    }


    ## strategy_to_set
    public function getStrategyToSets()
    {
        return $this->hasMany(StrategyToSet::className(), ['strategy_id' => 'id']);

    }


    public function getStrategySets()
    {
        #return [1,2,3,4,5];
        return $this->hasMany(StrategySet::className(), ['id' => 'strategy_set_id'])->viaTable('strategy_to_set', ['strategy_id' => 'id']);

    }


}
