use threads;
use strict;

print "START!";

my $bin = "/usr/bin/php yii";  ## php bin
my @b   = ();                  ## for cmd list
my $t   = {};                  ## for threads
my $d   = '';                  ## for day number

for( 0..5 ) {
    $d = $_;
    foreach ( `$bin cli-task/make-all show` ) {
        chomp;
        push @b, "$bin cli-task/make $_" if $_ > 1;
    }
}

$t->{$_} = threads->new( \&thrsub, $b[$_] ) foreach (0..$#b);
$t->{$_}->join() foreach( sort keys $t );

sub thrsub { my ($cmd) = @_; system($cmd); }

print "FINISHED!";

1;

=head1 NAME

 threads.pl

=head1 DESCRIPTION

 Make faster

=head2 CLI USAGE

 cd app/
 perl threads.pl

=head1 CE
