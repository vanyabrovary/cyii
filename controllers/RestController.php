<?php
namespace app\controllers;

use Yii;
use yii\web\{Controller,HttpException};
use yii\db\{Query,Expression};
use app\lib\{WQuery,WRedis};

class RestController extends Controller
{

    private $arg;
    private $model;

    public function init()
    {
        $this->arg   = Yii::$app->request->params();
        $this->model = 'app\models\\' . $this->arg["m"]; //' for mc
    }


    public function actionList()
    {
        return call_user_func( $this->model . '::find')->asArray()->all();

    }


    public function actionLoad()
    {
        return call_user_func( $this->model . '::findOne', $this->arg["id"] );

    }


    public function actionChild()
    {
        $func = $this->arg["child"];
        return call_user_func( $this->model . '::findOne', $this->arg["id"] )->$func;

    }


    public function actionSave()
    {
        /* Decides to UPDATE */
        if(isset($this->arg["id"])) {
            $model  = call_user_func( $this->model . '::findOne', $this->arg["id"] );

        }

        /* Decides to INSERT */
        if(!isset($model)) {
            $model  = new $this->model;

        }

        /* FROM json or form */
        $model->attributes = $this->arg;
        $model->save();

        /* model error. */
        if($model->errors) { throw new HttpException(400, implode(' ', $model->getFirstErrors('id') )); }

        /* return model content in body */
        return $model;

    }


    /* save list */
    public function actionSavel()
    {
        /* take json collection */
        $hash = json_decode(Yii::$app->request->params_json(), true);

        /* is this array? */
        if(gettype($hash) != 'array') throw new HttpException(400, 'WANT ARRAY');

        /* for models list. $model[1], $model[2], $model[3] ... */
        $i = 0;

        foreach ($hash as $itm) { $i++;

            /* Decides to UPDATE */
            if( isset( $itm["id"] ) ) {
                $model[$i] = call_user_func( $this->model . '::findOne', $itm["id"] );

            }

            /* Decides to INSERT */
            if( !isset($model[$i]) )  {
                $model[$i] = new $this->model;

            }

            /* set values and validate */
            $model[$i]->attributes = $itm;
            $model[$i]->validate();

            /* if row data is not valid, set error to $invalid[] array */
            foreach($model[$i]->errors as $err){
                $invalid[] = $err;
            }

        }

        /* if errors exists */
        if( isset($invalid) ) return $invalid;

        /* start transaction */
        $connection  = \Yii::$app->db;
        $transaction = $connection->beginTransaction();

        foreach( $model as $mod ) {
            try {
                $mod->save();
                $model_hash[] = $mod;
            }
            catch (\yii\db\Exception $e) {
                $errors = 1;
            }
        }

        if(isset($errors)){
            $transaction->rollBack();

        } else {
            $transaction->commit();

        }

        return $model_hash;

    }


    public function actionDelete()
    {
        $model = call_user_func( $this->model . '::findOne', $this->arg["id"] );

        if($model) {
            $model->delete();

        } else {
            throw new HttpException(400, '0 ROWS DELETED');

        }

        return ['message' => 'deleted'];

    }


    public function actionWorker()
    {
        $class = 'app\lib\WReq\\' . $this->arg["m"]; // ' for mc

        $worker = new $class();

        $worker->data = Yii::$app->request->params_json() ?? '';

        $worker->do('1');

        return $worker->result;

    }


    public function actionUnfold()
    {
        $this->_join('unfold');

    }


    public function actionExpand()
    {
        $this->_join('expand');

    }


    public function actionExpandleft()
    {
        $this->_join('expandleft');

    }


    private function _join($type)
    {
        list($from, $to) = preg_split('/:/', $this->arg["m"] ); // from:to

        $q = (new Query())->select("json_map( (row_to_json($from.*) || json_agg($to.*)::text ) ) AS item")->from("$from");

        if( $type == 'expand' ) {
            $q->innerJoin("$to", "$from.id = $to.$from" . "_id");

        }

        if( $type == 'unfold' ) {
            $q->innerJoin("$to", "$to.id   = $from.$to" . "_id");

        }

        if( $type == 'expandleft' ) {
            $q->leftJoin("$to", "$from.id = $to.$from" . "_id");

        }

        if( $this->_where() ) $q->where( $this->arg["_where"] );

        print '['.implode(',', array_column( $q->groupBy("$from.id")->all(), 'item') ).']';

    }


    private function Group()
    {
        $cols      = explode(',', $this->arg["cols"] );
        $cols['0'] = 'COUNT(' . $cols['0'] . ')';
        $q = (new Query())->select($cols)->from($this->arg['m']);

        if( $this->_where()) $q->where( $this->arg['_where'] );

        array_shift($cols);
        return $q->groupBy($cols)->all();

    }


    private function Groupview()
    {
        $cols      = explode(',', $this->arg["cols"] );
        $cols['0'] = 'COUNT(' . $cols['0'] . ')';
        $q = (new Query())->select($cols)->from($this->arg['m']);

        if( $this->_whereview() ) $q->where( $this->arg['_whereview'] );

        array_shift($cols);
        return $q->groupBy($cols)->all();

    }


    public function actionGroupview()
    {
        return $this->Groupview();

    }


    public function actionGroup()
    {
        return $this->Group();

    }


    public function actionWhere()
    {
        return call_user_func( $this->model . '::find' )->where($this->_where())->asArray()->all();

    }


    public function actionWhereview()
    {
        return call_user_func( $this->model . '::find' )->where($this->_whereview())->asArray()->all();

    }


    private function _where()
    {
        if(!isset($this->arg["vars"])) return 0;

        $kv = explode(',', $this->arg["vars"] );

        for($i=0; $i < count($kv); $i++) {

            $a = explode( ':', $kv[$i] );
            if(!isset($a[1]) ) continue;

            if( preg_match("/;/", $a[1]) ) {
                $where[$a[0]] = explode(';', $a[1]);

            }else{
                $where[$a[0]] = $a[1];

            }

        }

        $this->arg["_where"] = $where;
        return $where;

    }


    private function _whereview()
    {
        if(!isset($this->arg["vars"])) return 0;

        $kv = explode(',', $this->arg["vars"] );

        for($i=0; $i < count($kv); $i++) {

            $a = explode( ':', $kv[$i] );

            if(!isset($a[1]) ) continue;

            $raw[] = $this->__oper($a[0], $a[1]);
        }

        $this->arg["_whereview"] =  implode(' AND ', $raw );
        return $this->arg["_whereview"];

    }


    private function __oper($col, $val)
    {
        list($operator, $column, $column_ext ) = preg_split('/\./', $col ); // from:to

        if(isset($column_ext)) { $column = $column.'.'.$column_ext; }

        if( $operator == 'lt'   )    { return $column."  < '".$val."'"; }
        if( $operator == 'gt'   )    { return $column."  < '".$val."'"; }
        if( $operator == 'eq'   )    { return $column."  = '".$val."'"; }
        if( $operator == 'neq'  )    { return $column." != '".$val."'"; }
        if( $operator == 'gte'  )    { return $column." >= '".$val."'"; }
        if( $operator == 'lte'  )    { return $column." <= '".$val."'"; }
        if( $operator == 'is'   )    { return $column." IS ".$val."";   }
        if( $operator == 'isnt' )    { return $column." IS NOT ".$val."";    }
        if( $operator == 'ov'   )    { return $column.' && ARRAY['.$val.']'; }
        if( $operator == 'cs'   )    { return $column.' @> ARRAY['.$val.']'; }
        if( $operator == 'cd'   )    { return $column.' <@ ARRAY['.$val.']'; }
        if( $operator == 'in'   )    { return $column.' IN (\''.implode( "','", preg_split('/;/', $val ) ).'\')'; }
        if( $operator == 'between')  { return $column.' BETWEEN \''.implode( "' AND '", preg_split('/;/', $val ) ).'\''; }

        throw new HttpException(400, 'BAD operator:'.$operator.'. ALLOW: lt, gt, eq, neq, gte, lte, ov, cs, cd, in, between, is, isnt');

    }


    public function actionWquery()
    {
        $type   = $this->arg["m"];
        $value  = $this->arg["id"] ?? null;

        if($value) {
                $query  = ( new WQuery() )->$type($value);
                print $query['res'];

        } else {
            print "[";

            foreach ( ( new WQuery() )->$type($value) as $item) {
                $a[] = $item['res'];
            }

            print join(',', $a);
            print "]";

        }
    }


    public function actionWqueryview()
    {
        $type              = $this->arg["m"];
        $this->arg["vars"] = $this->arg["id"] ?? null;

        print "[";

        foreach ( ( new WQuery() )->$type( $this->_whereview(), '1' ) as $item) {
            $a[] = $item['res'];
        }

        print join(',', $a);
        print "]";

    }


    public function actionGetredis()
    {
        if(isset($this->arg["m"])){
            $redis = new WRedis( $this->arg["m"] );

        }

        if(!isset($this->arg["m"])){
            $redis = new WRedis();

        }

        return $redis->keys();

    }


    public function actionGetredisval()
    {
       $redis = new WRedis( $this->arg["m"] );
       print $redis->val();

    }

}
