<?php

namespace app\lib;

class WRedis
{
    private $key;
    private $key_date;

    private $exp;
    private $db;

    public function __construct($key) {
        $this->key = $key;

        $this->exp = ( 60 * 60 * 2 );
        $this->db();

    }


    private function db() {
        if ( !$this->db ) {
            $this->db = new \Redis();
            $this->db->connect( '127.0.0.1', 6379 );
            $this->db->select('2');
        }

        return $this->db;

    }


    public function reset( $val ) {
        if(!$val) {
            print 'NO VALUE SET!';
            return 0;
        }

        $this->db->del( $this->key );

        return $this->set($val);

    }


    public function set( $val ) {
        if(!$val) {
            print 'NO VALUE SET!';
            return 0;
        }

        $this->db->set( $this->key, $val );

        return $val;

    }


    public function ttl() : int {
        return $this->db->ttl($this->key);

    }


    public function set_expire( $val ) {
        if(!$val) {
            print 'NO VALUE SET!';
            return 0;
        }

        $this->db->del($this->key);
        $this->db->set($this->key, $val);

        $this->db->expire($this->key, $this->exp );

        return $this->db->ttl($this->key);

    }


    public function incr() {
        return $this->db->incr($this->key);

    }


    public function  val() {
        return $this->db->get($this->key);

    }

    public function  key() {
        return $this->key;

    }


    public function  key_for_log() {
        return 'log:'.$this->key.':'.date('Y-m-d');

    }

    public function  keys() {
	   return  $this->db->keys($this->key);

    }

    public function  keysval() {

        foreach ( $this->db->keys($this->key) as $key) {
            $item[$key] = $this->db->get( $key );
        }
        if( isset($item) ){
            return $item;
        } else {
            return -0;
        }
    }

    ## todel
    public function  key_date() {
        if(!isset($this->key_date)) { $this->key_date = $this->key().':'.date('Y-m-d'); }
        return $this->key_date;

    }

    ## todel
    public function  key_date_incr() {
        return $this->db->incr($this->key_date());
    }

    ## todel
    public function  add_row_to_log() {
       $key = $this->key_date();

       return  $this->db->keys($this->key);

    }

    public function log( $val ) {

        if(!$val) {
            print 'NO VALUE SET FOR LOG!';
            return 0;
        }

        $this->db->set( $this->key_for_log(), $val );

        $this->db->publish('g', $val);

        return $val;

    }

}

?>