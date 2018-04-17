<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class StrategySets extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_sets';
    }
}
