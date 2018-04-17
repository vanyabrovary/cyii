<?php
namespace app\lib\WReq;

use app\lib\WReq;
use app\lib\WRedis;

class OauthToken extends WReq
{
    private $redis;

    public function __construct() {
        $this->auth = 0;

        $this->url  = 'http://gw.sb.ua:8081/oauth/token';

        $this->data =  [
            "client_id"     => "2",
            "grant_type"    => "password",
            "password"      => "12Da",
            "username"      => "SB_mod",
            "client_secret" => "KaahB7z43ACONVC345345cvsdfgsdg"
        ];

        $this->redis = new WRedis($this->url);

    }

    public function token() {

        if( $this->redis->ttl() >  0 ) { return $this->redis->val(); }

        if( $this->redis->ttl() <= 0 ) {

            $result = $this->do();

            if( $this->result['access_token'] ){
                $this->redis->set_expire( $this->result['access_token'] );
            }

            return $this->redis->val();


        }

    }

}

?>