<?php

namespace app\models;
use Yii;
use yii\db\{ActiveRecord,Command};
use app\lib\WReq\{StrategyCases,QDate};
use app\lib\{WQuery,WRedis};

class StrategyItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'strategy_item';

    }


    public function rules()
    {
        return [

            // #### fields
            [['id', 'strategy_id', 'task_type_id', 'destination_id', 'template_type_id'], 'integer'],
            ['name', 'string', 'max' => 255],
            ['is_public',  'boolean', 'trueValue' => true, 'falseValue' => false],

            // #### safe
            [ ['id', 'is_public', 'name', 'strategy_id', 'task_type_id', 'destination_id', 'template_type_id'], 'safe'],

            // #### 'required'
            [['name', 'strategy_id', 'task_type_id'], 'required'],

            // #### unique
            [['strategy_id', 'task_type_id', 'name'], 'unique', 'targetAttribute' => ['strategy_id', 'task_type_id', 'name']]

        ];

    }


    public function getStrategy()
    {
        return $this->hasOne(Strategy::className(), ['id' => 'strategy_id']);

    }


    public function getTaskTypes()
    {
        return $this->hasOne(TaskType::className(), ['id' => 'task_type_id']);

    }


    public function getStrategyItemFilters()
    {
        return $this->hasMany(StrategyItemFilter::className(), ['strategy_item_id' => 'id']);

    }


    public function getFilters()
    {
        print passthru("echo '".$this->makeFilters()."' | json_pp -f json -json_opt pretty ");

    }


    ## get filters
    private function _get_fs()
    {
        $m = Filters::find();
        $m->andWhere([ 'not', ["variable_value" => 'dp.ContragentID'] ]);
        $m->andWhere([ 'item_id'                => $this->id ]);

        return $m;

    }


    public function getFiltersOr()
    {
        return $this->_get_fs()->andWhere(["is_or" => 'true' ])->asArray()->all();

    }


    public function getFiltersAnd()
    {
        return $this->_get_fs()->andWhere(["is_or" => 'false' ])->asArray()->all();

    }


    private $_column;
    public  $f_add_day;  # filter add days
    private $todo_last; # redefine protection

    ## For CliController. Add day numbers to current date
    public function todo_at()
    {
        if( !isset($this->f_add_day) ) throw new \yii\web\HttpException(400, 'BAD f_add_day');

        $date_obj = new \DateTime(date('Y-m-d'));
        $date_obj->modify('+'.$this->f_add_day.' day');
        return $date_obj->format('Y-m-d');

    }


    ## For CliController. Holydays for todo_at
    public function todo_last()
    {
        if( !isset($this->todo_last) ){
            $result = (new WQuery())->schedule_working_date( $this->todo_at() );
            $this->todo_last = $result['date'];
        }

        return $this->todo_last;

    }


    ## FIX ME to mixin
    ## For current model. Add day numbers to custom date
    public function _for_date_segment_date($date = null)
    {

        if ($this->f_add_day && $date) {

            $date_obj = new \DateTime($date);
            $date_obj->modify('+'.$this->f_add_day.' day');
            return $date_obj->format('Y-m-d');

        }

        return $date;

    }

    ## FIXME to mixin
    private function _f_item($itm) :array
    {
        $this->_column[]  = $itm["variable_value"];
        # between 5?7
        if ('between' == $itm['filter_name']) {
            ## 5?7
            if ($itm['segment_date']) {
                # $l = 5; $r = 7;
                list($l, $r) = explode("?", $itm['segment_date']);
                # 2017-10-27 = 2017-10-22 + 5;
                $l = $this->_for_date_segment_date($l);
                # 2017-10-29 = 2017-10-22 + 5;
                $r = $this->_for_date_segment_date($r);
            } else {
                list($r, $l) = explode("?", $itm['filter_value']);
            }
            return array(
                '0' => $itm['filter_name'],
                '1' => $itm['variable_value'],
                '2' => $r,
                '3' => $l
            );
        # <>=
        } elseif('in' == $itm['filter_name']) {

            $str = '['.preg_replace('/"/', '', $itm['filter_value']).']';

            return array(
                '0' => $itm['filter_name'],
                '1' => $itm['variable_value'],
                '2' => $str
            );
        } else {
            return array(
                '0' => $itm['filter_name'],
                '1' => $itm['variable_value'],
                '2' => $this->_for_date_segment_date($itm['segment_date']) ?? $itm['filter_value']
            );
        }

    }

    ## FIXME. Implement this using PostgreSQL perlu/PL
    public function makeFilters() :string
    {

        ## default columns
        $this->_column    = Yii::$app->params['def']['filter_col'];

        $f['0']           = 'AND';

        ## ['1'][] for any and
        $f['1']['0']      = 'AND';

        ## default filters
        $f['1']['1']      = Yii::$app->params['def']['filter'] ;

        foreach ( $this->strategy->getFiltersAnd()    as $itm)     { $f['1'][] = $this->_f_item($itm); }
        foreach ( $this->getFiltersAnd()              as $itm)     { $f['1'][] = $this->_f_item($itm); }

        $f['2']['0']      = 'AND';

        ## ['2']['1'][] for contragent
        $f['2']['1']['0'] = 'OR';

        foreach ($this->strategy->getContragents()    as $itm)     { $f['2']['1'][]  = $this->_f_item($itm); }
        foreach ($this->strategy->getContragentsAll() as $itm)     { $f['2']['1'][]  = $this->_f_item($itm); }

        ## ['2']['2'][] for any or
        $f['2']['2']['0'] = 'OR';

        foreach ($this->strategy->getFiltersOr()       as $itm)     { $f['2']['2'][]  = $this->_f_item($itm); }
        foreach ($this->getFiltersOr()                 as $itm)     { $f['2']['2'][]  = $this->_f_item($itm); }

        if (!isset($f['2']['2']['1'])) { unset($f['2']['2']); }
        if (!isset($f['2']['1']['1'])) { unset($f['2']['1']); }
        if (!isset($f['2']['1']))      { unset($f['2']);      }

        $str = json_encode($f, JSON_FORCE_OBJECT);

        ## fix for filter "in".
        $str = str_replace('"[', '[', $str);
        $str = str_replace(']"', ']', $str);
        $str = str_replace('\"', '"', $str);

        return '{"filter":' . $str . ',"columns":'.json_encode(array_unique($this->_column)) . '}';

    }


    public function getCases()
    {
        return ( new StrategyCases($this->makeFilters()) )->do()->result;

    }


    public function getCasesjson()
    {
        print ( new StrategyCases($this->makeFilters()) )->do()->content;

    }

    ## not full implementation !!!
    public function getCasescrosjson()
    {
        $result_exists[] = '';
        $cases = $this->getCases();

        foreach ( $cases as $itm ) {
            #$fresh = Task::find();
            #$result_exists[] = $fresh->where( [ 'case_id' => $itm['ID'], 'todo_at' => date('Y-m-d'), 'strategy_item_id' => $this->id ] )->one();
        }

        $result = [ 'cross_cases' => $result_exists, 'cases' => $cases];

        return $result;

    }

    ## not full implementation !!!
    public function getCasescroscountjson()
    {
        $result_exists[] = '';
        $cases = $this->getCases();

        foreach ( $cases as $itm ) {
            $fresh = Task::find();
            $result_exists[] = $fresh->where([ 'case_id' => $itm['ID'], 'todo_at' => date('Y-m-d') ])->one();
        }

        $result = [ 'cross_cases_count' => count($result_exists) - 1, 'cases_count' => count($cases)];
        return $result;

    }


    public function getRediskey()
    {
        return $this->redis_store_key('*').':*';

    }


    public function getHistorycases()
    {
       $redis = new WRedis( $this->redis_store_key('*').':*' );
       return json_decode(var_dump($redis->keysval()) );

    }


    public function redis_store_key($all_date = null){

        if(!isset($all_date)){
            $all_date = date('Y-m-d');
        }

        return "log:strategy-case:".$all_date.":".$this->task_type_id.":".$this->strategy_id.":".$this->id;

    }

}
