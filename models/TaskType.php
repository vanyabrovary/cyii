<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class TaskType extends ActiveRecord
{
    public static function tableName()
    {
        return 'task_type';
    }


    public function rules()
    {
        return [
            ['id', 'integer'],

            ['name', 'string',  'max' => 255],

            // #### safe
            [['id', 'name'], 'safe'],

            // #### 'required'
            ['name','required'],

            // #### unique
            [ ['name'], 'unique', 'targetAttribute' => ['name'] ],
        ];
    }


    public function getStrategyItems()
    {
        return $this->hasMany(StrategyItem::className(), ['task_type_id' => 'id']);
    }
}
