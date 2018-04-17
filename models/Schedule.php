<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Schedule extends ActiveRecord
{

    public static function tableName() :string
    {
        return 'schedule';
    }

    public function rules() :array
    {
        return [
            ['id',         'integer'],
            ['date',       'string',  'min'       =>10,    'max'        => 10],
            ['is_working', 'boolean', 'trueValue' => true, 'falseValue' => false],

            // #### safe
            [ ['id', 'date', 'is_working'], 'safe'],

            // #### 'required'
            ['date', 'required'],

            // #### unique
            [ ['date'], 'unique', 'targetAttribute' => ['date'] ]
        ];
    }

}
