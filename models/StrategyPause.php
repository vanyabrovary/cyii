<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StrategyPause extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_pause';
    }
    public function rules()
    {
     return [

    //     //     // #### fields
            [['id', 'strategy_id'], 'integer'],
            [['comment', 'pause_from', 'pause_to'], 'string', 'max' => 255],
        ['set_is_public_to',  'boolean', 'trueValue' => true, 'falseValue' => false],
    //     //     // #### safe
        [['id', 'strategy_id', 'comment', 'pause_from', 'pause_to', 'set_is_public_to'], 'safe'],
            #### 'required'
        [['comment', 'strategy_id', 'pause_from', 'pause_to' ], 'required'],
    #### unique
        [['strategy_id', 'pause_from', 'pause_to'], 'unique', 'targetAttribute' => ['strategy_id', 'pause_from', 'pause_to']]

    ];
    }


}
