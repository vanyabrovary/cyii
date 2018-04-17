#!/usr/bin/env perl
use Mojolicious::Lite;
use Mojo::Redis;
use Protocol::Redis::XS;
use common::sense;

##------------------------------------------------------------------------------------------------
## Install (Unix)
##------------------------------------------------------------------------------------------------

## Install redis-server and some modules from cpan
## ~ cpan install Mojolicious::Lite common::sense Mojo::Redis Protocol::Redis::XS

##------------------------------------------------------------------------------------------------
## Usage
##------------------------------------------------------------------------------------------------

## Start on https://127.0.0.1:7473
## ~ nohup perl ws.pl &

## Stop
## ~ ps aux |grep ws.pl |awk '{print $2;}' |xargs kill -9

## Status
## ~ ps aux |grep ws.pl

##------------------------------------------------------------------------------------------------
## Info
##------------------------------------------------------------------------------------------------

## This script subscribe to Redis pub/sub channel "g".
## And show any messages in browser using WebSocket.

## ~ redis-cli> PUBLISH g Hello

##------------------------------------------------------------------------------------------------
## Powered by Mojolicious. Perl.
##------------------------------------------------------------------------------------------------


## Route for https://127.0.0.1:7473/ws
get '/ws' => sub{
    my $self = shift;

    ## Render template ( Look at line 85 )
    $self->render( template=> 'ws_page' );
};


## Route for wss://127.0.0.1:7473/stream
websocket '/stream' => sub {
    my $self = shift;

    ## WebSocket instance
    my $ws   = $self->tx;

    ## Send hello on connect
    $ws->send('hello');

    ## Redis pub/sub instance
    my $pubsub = Mojo::Redis->new;
    $pubsub->protocol_redis("Protocol::Redis::XS");

    ## Redis pub/sub close connection after 180 sec
    $pubsub->timeout(180);
    $self->stash(pubsub_redis => $pubsub);

    ## Subscribe to channel "g". Send ws messages on event
    $pubsub->subscribe('g' => sub{ my ($redis, $event) = @_;  $ws->send( $event->[2] ); } );

    ## Message from ws
    $self->on(message           => sub { my ($ws, $msg) = @_; });

    ## On clese connection
    $self->on(finish            => sub { my $ws = shift; say 'WS closed.'; });

    ## Send ws message "." every second
    Mojo::IOLoop->recurring(1  => sub { $ws->send('.'); });
};

app->start('daemon', '--listen' => "https://127.0.0.1:7473");

__DATA__

@@ws_page.html.ep

<!DOCTYPE html>
<html>
<head><script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script></head>
<body>
  <div id='dnow'></div>
  <br>
  <div id='msgs'></div>
  <script type="text/javascript">
    var conn;

    $(document).ready(function(){

      conn 		= new WebSocket('wss://sb.ssh.in.ua/stream');

      conn.onmessage 	= function  (event) {

      if(event.data != '.' && event.data != 1){
        $('#msgs').append(event.data+"<br/>");
      }

      if(event.data == '.') {
        var d = new Date;
        $('#dnow').html(d.toString());
      }
    };

    conn.onopen 	= function () { var d = new Date; $('#dnow').html(d.toString());  };
    conn.onclose 	= function () { $('#msgs').append("closed.<br>");  };

  });
  </script>
</body>
</html>
