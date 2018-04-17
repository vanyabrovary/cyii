#!/usr/bin/perl
#use warnings;
use strict;

use Test::More "no_plan";
use Data::Dumper;
use LWP::UserAgent;
use HTTP::Request::Common 'POST';
use DateTime;
my $date_now     = DateTime->now(); # 1
my $date_now_add = $date_now->add( days => 1 ); # 5


my $STD 	= shift || 0;
my $SENDER 	= shift || 'curl';  # curl || mojo;
my %cfg = ();

#------------------------------------------------------------------------------------------------#
my $host = 'https://sb.ssh.in.ua';
#------------------------------------------------------------------------------------------------#

print "AGENT \n";

%cfg = (
    1 => {
        url    => $host."/v2/StrategySet",
        like   => 'name',
        mess   => '/v2/StrategySet',
        method => 'GET',
        status => 200,
    },
    2 => {
        url    => $host."/v2/StrategyToSet",
        like   => 'strategy_id',
        mess   => '/v2/StrategyToSet',
        method => 'GET',
        status => 200,
    },
    3 => {
        url    => $host."/v2/Strategy",
        like   => 'name',
        mess   => '/v2/Strategy',
        method => 'GET',
        status => 200,
    },
    4 => {
        url    => $host."/v2/StrategyItem",
        like   => 'name',
        mess   => '/v2/StrategyItem',
        method => 'GET',
        status => 200,
    },
    6 => {
        url    => $host."/v2/StrategyItem/30",
        like   => 'name',
        mess   => '/v2/StrategyItem/30',
        method => 'GET',
        status => 200,
    },
    7 => {
        url    => $host."/v2/StrategyItemFilter",
        like   => 'value',
        mess   => '/v2/StrategyItemFilter',
        method => 'GET',
        status => 200,
    },
    8 => {
        url    => $host."/v2/TaskType",
        like   => 'SMS',
        mess   => '/v2/TaskType',
        method => 'GET',
        status => 200,
    },
    9 => {
        url    => $host."/v2/StrategyFilter/where/strategy_id:30,strategy_variable_value:dp.ContragentID",
        like   => 'strategy_variable_value',
        mess   => '/v2/StrategyFilter/where/strategy_id:30,strategy_variable_value:dp.ContragentID',
        method => 'GET',
        status => 200,
    },
    10 => {
        url    => $host."/v2/StrategyFilter/where/strategy_id:30",
        like   => 'strategy_id',
        mess   => '/v2/StrategyFilter/where/strategy_id:30',
        method => 'GET',
        status => 200,
    },
    11 => {
        url    => $host."/v2/StrategyItem/30/child/strategyItemFilters",
        like   => 'strategy_item_id',
        mess   => '/v2/StrategyItem/30/child/strategyItemFilters',
        method => 'GET',
        status => 200,
    },
    12 => {
        url    => $host."/v2/Strategy",
        unlike => '1',
        mess   => 'OPTIONS /v2/Strategy (200)',
        method => 'OPTIONS',
        status => 200,
    },
    13 => {
        url    => $host."/v2/23424",
        like   => '',
        mess   => 'OPTIONS /v2/234 (204)',
        method => 'OPTIONS',
        status => 204,
    },

    ################################################################
    14 => {
        url    => $host."/v2/TaskType",
        json   => '{"id":8,"name":"888"}',
        like   => 'name',
        mess   => 'POST /v2/TaskType {"id":8,"name":"888"} (200)',
        method => 'POST',
        status => 200,
    },

    15 => {
        url    => $host."/v2/TaskType",
        json   => '{"name":"888"}',
        like   => '400',
        mess   => 'POST /v2/TaskType {"name":"888"} (400)',
        method => 'POST',
        status => 400,
    },
    16 => {
        url    => $host."/v2/TaskType/8",
        json   => '{"name":"8899"}',
        like   => '8899',
        mess   => 'PUT /v2/TaskType {"id":8,"name":8899} (200)',
        method => 'PUT',
        status => 400,
    },

    17 => {
        url    => $host."/v2/TaskType/8",
        like   => '8899',
        mess   => 'GET /v2/TaskType/8',
        method => 'GET',
        status => 200,
    },
    18 => {
        url    => $host."/v2/TaskType",
        json   => '{"id":8899}',
        like   => '400',
        mess   => 'DELETE /v2/TaskType {"id":888889} (400)',
        method => 'DELETE',
        status => 400,
    },
    19 => {
        url    => $host."/v2/TaskType",
        json   => '{"id":8}',
        like   => 'deleted',
        mess   => 'DELETE /v2/TaskType {"id":8} (200)',
        method => 'DELETE',
        status => 200,
    },
    20 => {
        url    => $host."/v2/strategy_filter:strategy/unfold/strategy_variable_value:dp.ContragentID,strategy_filter_value:444",
        like   => 'count',
        mess   => 'GET /v2/strategy_filter:strategy/unfold/strategy_variable_value:dp.ContragentID,strategy_filter_value:444(200)',
        method => 'GET',
        status => 200,
    },
    21 => {
        url    => $host."/v2/strategy:strategy_filter/expand",
        like   => 'count',
        mess   => 'GET /v2/strategy:strategy_filter/expand (200)',
        method => 'GET',
        status => 200,
    },
    22 => {
        url    => $host."/v2/StrategyItem/32/child/filters",
        like   => '{',
        mess   => '/v2/StrategyItem/32/child/filters',
        method => 'GET',
        status => 200,
    },
    23 => {
        url    => $host."/v2/StrategySet",
        like   => 'name',
        mess   => '/v2/StrategySet',
        method => 'GET',
        status => 200,
    },
    24 => {
        url    => $host."/v2/StrategyToSet",
        like   => 'strategy_id',
        mess   => '/v2/StrategyToSet',
        method => 'GET',
        status => 200,
    },

);

&_request($_) for ( sort { $a <=> $b } keys %cfg );

#------------------------------------------------------------------------------------------------#
print "WORKER \n";

%cfg = (

    23 => {
        url    => $host."/w/v2/Contragent",
        like   => 'ShortName',
        mess   => '/w/v2/Contragent http://gw.sb.ua:8082/contragent',
        method => 'GET',
        status => 200,
    },

    24 => {
        url    => $host."/w/v2/StrategyFilter",
        like   => 'FilterTypeID',
        mess   => '/w/v2/StrategyFilter http://gw.sb.ua:8082/strategy-filter',
        method => 'GET',
        status => 200,
    },

    25 => {
        url    => $host."/w/v2/StrategyFilterType",
        like   => 'Name',
        mess   => '/w/v2/StrategyFilterType http://gw.sb.ua:8082/strategy-filter-type',
        method => 'GET',
        status => 200,
    },

    26 => {
        url    => $host."/w/v2/StrategyVariables",
        like   => 'Name',
        mess   => '/w/v2/StrategyVariables http://gw.sb.ua:8082/strategy-variables',
        method => 'GET',
        status => 200,
    },

    27 => {
        url    => $host."/w/v2/TaskTemplates",
        like   => '{',
        mess   => '/w/v2/TaskTemplates http://gw.sb.ua:8082/task-templates',
        method => 'GET',
        status => 200,
    },

    28 => {
        url    => $host."/w/v2/StrategyCases",
        like   => 'ID',
        mess   => '/w/v2/StrategyCases http://gw.sb.ua:8082/strategy-cases',
        method => 'POST',
        status => 200,
        json   => '{"filter": {"0": "AND", "1": {"0": "AND", "1": {"0": "=", "1": "dc.CaseProcess", "2": 1 }, "2": {"0": ">", "1": "fca.ActualSum", "2": "500"}, "3": {"0": "=", "1": "dptps.Name", "2": "Активное"}, "4": {"0": "=", "1": "dhr.Name", "2": "Обещание об оплате"} }, "2": {"0": "AND", "1": {"0": "OR", "1": {"0": "=", "1": "dp.ContragentID", "2": "196"}, "2": {"0": "=", "1": "dp.ContragentID", "2": "443"} } } }, "columns": ["dc.ID", "dc.CaseProcess", "fca.ActualSum", "dptps.Name", "dhr.Name", "dp.ContragentID"] }'
    },


    29 => {
        url    => $host."/v2/Strategy/26/child/strategySets",
        like   => 'name":"',
        mess   => 'GET /v2/Strategy/26/child/strategySets',
        method => 'GET',
        status => 200,
    },

    30 => {
        url    => $host."/v2/StrategyToSet/savel",
        like   => 'strategy_to_id',
        mess   => '/w/v2/StrategyToSet add list StrategyToSet',
        method => 'POST',
        status => 200,
        debug   => 0,
        json   => '[{"strategy_id":"8","strategy_set_id":"62"}]'
    },

    31 => {
        url    => $host."/v2/StrategyPause",
        like   => 'ОШИБКА:',
        mess   => '/w/v2/StrategyPause add bad',
        method => 'POST',
        status => 500,
#        debug  => 0,
        json   => '{"strategy_id":"50","pause_from":"'.$date_now->ymd().'","pause_to":"'.$date_now->ymd().'","comment":"Bad","set_is_public_to":"f"}'
    },

    32 => {
        url    => $host."/v2/StrategyPause",
        like   => '"strategy_id":',
        mess   => '/w/v2/StrategyPause add good',
        method => 'POST',
        status => 200,
        debug  => 0,
        json   => '{"id":"27","strategy_id":"50","pause_from":"'.$date_now->ymd().'","pause_to":"'.$date_now_add->ymd().'","comment":"Good","set_is_public_to":"f"}'
    },


    32 => {
        url    => $host."/v2/StrategyPause",
        like   => '"strategy_id":',
        mess   => '/w/v2/StrategyPause add good',
        method => 'POST',
        status => 200,
        debug  => 0,
        json   => '{"id":"27","strategy_id":"50","pause_from":"'.$date_now->ymd().'","pause_to":"'.$date_now_add->ymd().'","comment":"Good","set_is_public_to":"f"}'
    }



);
&_request($_) for ( sort { $a <=> $b } keys %cfg );

print `date`;

#------------------------------------------------------------------------------------------------#

#------------------------------------------------------------------------------------------------#

sub _request {
    my $n = shift;

    ## show arguments
    if ( $STD ) {
    	if ( $STD eq 'show' ) {

            print Dumper( %cfg{$n} );
            return 1;
        }
        if ( $STD ne $n ) {
        	return 1;
        }
    }

    return &__curl($n);
}
#------------------------------------------------------------------------------------------------#

#------------------------------------------------------------------------------------------------#
sub __curl {
    my $n   = shift;

    ## post file if 'file' is set for current test
    my $file = $cfg{$n}{file}  ?  "-F 'file=\@" . $cfg{$n}{file} . "'" : '';

    my $json = $cfg{$n}{json}  ?  "-d '" . $cfg{$n}{json} . "'" : '';

    ## set request methos POST if any ather method not set for current test
    my $method = $cfg{$n}{method} || 'GET';

    ## prepare cli command
    my $curl = "curl -X$method --silent -H 'Accept: application/json' -H 'Content-Type: application/json'  $file $json " . $cfg{$n}{url};

    my $res;
    ## make request

#   if($xml){my $res = __lwp_post($cfg{$n}{url}, $cfg{$n}{file} ); }else{
	my $res = `$curl`;
	print $res if ( exists $cfg{$n}{debug} );
#   }

    like( $res, qr/\Q$cfg{$n}{like}/,   $cfg{$n}{mess} ) if $cfg{$n}{like};
    unlike( $res, qr/\Q$cfg{$n}{unlike}/, $cfg{$n}{mess} ) if $cfg{$n}{unlike};

    ## print curl cli command is debug => 1 for current test

    print "\n $n > $curl \n -- \n $res \n -- \n"  if ( exists $cfg{$n}{debug} );
}

sub __lwp_post {
	my ($url, $file) = @_;
	my $ua  = LWP::UserAgent->new;
	my $req = POST $url, Content => [ file  => [ $file ] ];
	$req->content_type('application/xml; charset=utf-8');
	my $res = $ua->request( $req );
	return $res->content
}
#------------------------------------------------------------------------------------------------#

1;

=head1 NAME

 testrest.pl

=head1 DESCRIPTION

 Tests for CE REST using curl and Test::More

=head1 EXAMPLES

  For add new request, specify it as new hash element

  my %cfg = (
      1 => {
          url    => $cgf->{mojo_uri_rest} . 'hardware/cme/capturers',
          like   => 'capa',
          mess   => 'RestHardwareCme OK',
          method => 'GET',
      },
  )
  &_curl($_) for (sort { $a <=> $b} keys %cfg);

=head2 EXPLAIN

  1     - 'test number' for cli. Numbers use only for identification
  url   - 'request url'
  like  - 'like or unlike response content',
  mess  - 'test message ',
  file  - 'file_path for POST request (not required)',
  method- 'request method GET/POST/PUT (not required, default=POST})',
  debug - 'show curl cli command after request (not required)'

=head2 CLI USAGE

  testrest.pl
    make all tests

  testrest.pl show
    show all tests with numbers

  testrest.pl 17
    make test with number 17

  testrest.pl 17 mojo
    make test with number 17 using Mojo::Test

  testrest.pl 17 curl
    make test with number 17 using curl

=head1 DEBUGING

=head1 TODO

=head1 Nanotick
