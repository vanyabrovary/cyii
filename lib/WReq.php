<?php
namespace app\lib;

use Yii;

abstract class WReq
{

    public    $result;           # content body parsed json
    public    $content;          # content body
    public    $data;             # array
    public    $debug;            # array

    protected $strict;           # array
    protected $content_info;     # curl content content_info code
    protected $url;              # string
    protected $data_key;
    protected $auth;             # 1 - on, 0 - off
    protected $timeout;          # 20 as default

    private function _curl_cmd() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,            $this->_url() );
        curl_setopt($ch, CURLOPT_POST,           $this->_is_post() );
        curl_setopt($ch, CURLOPT_TIMEOUT,        $this->_timeout() );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if( $this->_auth() ) curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->_token_header() );
        if( $this->_data() ) curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->_data() );

        #if( $this->_debug()) print "[".date('Y-m-d H:i:s')."] RequestExec " . $this->_url() . "...\n";

        $start = microtime(true);

        $this->content      = curl_exec($ch);
        $this->content_info = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $time_exec = microtime(true) - $start;

        #if( $this->_debug() ) print "RequestExecOK $time_exec\n";

        curl_close($ch);

        $this->_result();

        return $this;

    }


    protected function _url() : string {
       if(!$this->url) throw new \Exception('NO URL SET!');
       return  $this->url;

    }


    private function _data() {
        return $this->data;

    }


    private function _debug() {
        return $this->debug;

    }


    private function _timeout() :int {
        return $this->timeout ?? 360;

    }


    private function _is_post() :bool {
        return $this->data ? 1 : 0;

    }


    private function _is_strict() {
        return $this->strict;

    }


    private function _auth() :bool {
        return $this->auth ? 1 : 0;

    }


    private function _token_header() : array {
        $auth_clas  = 'app\lib\WReq\OauthToken';
        $auth_call  = new $auth_clas();
        $auth_token = $auth_call->token();

        if(!$auth_token) {
            throw new \Exception('BAD AUTH TOKEN! Sorry!');
        }

        return array('Content-Type: application/json', sprintf('Authorization: Bearer %s', $auth_token));

    }


    private function _result() {

        if ( $this->content_info != 200 ) {

            $this->l('CURL ERROR ['.$this->content_info.'] ['.$this->content.']');

        }

        $this->result = json_decode( $this->content, true );

        if ( json_last_error() ) {

            $this->l('JSON ERROR: ['.json_last_error().']');

        }

        //if( $this->_debug() ) print $this->content."\n";

    }


    private function to_redis($key = NULL) {
       $redis = new WRedis( $key );
       $redis->set( $this->content );

    }


    public function do($is_storable = NULL) {
        $this->_curl_cmd();

        if(isset($is_storable)) $this->to_redis( $is_storable );

        return $this;

    }


    private function l($log) {
        if (!$this->_is_strict()) {
            throw new \Exception($log);
        } else {
            print "$log";

        }

    }

}

?>