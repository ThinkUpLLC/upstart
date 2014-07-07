#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Getopt::Long;

my $engine = {};
my $users = {};
my $options = {'users' => 0,'engine' => 'bad'};
my $output = 0;

GetOptions(
 'users'    => \($options->{'users'}),
 'engine=s' => \($options->{'engine'}),
);
if (!$options->{'users'} and !$options->{'engine'}) {
	print "Usage: $0 [--users] [--engine={dump|fix}]\n";
	print "\t--users     Checks active users against thinkupstart_* databases\n";
	print "\t--engine    Checks that InnoDB is used for all thinkupstart_* tables\n";
	print "\n";
	exit();
}
exit() if ($options->{'engine'} ne 'dump' && $options->{'engine'} ne 'fix');


my $dbh = DBI->connect('DBI:mysql:;host=db.x.thinkup.com','root','uKRUtJ3lxrDUBPeD');
my $sth;

if ($options->{'users'}) {
	$dbh->do('USE thinkupllc_upstart');
	$sth = $dbh->prepare("SELECT thinkup_username FROM subscribers WHERE membership_level != 'Waitlist' AND thinkup_username IS NOT NULL");
	$sth->execute();
	while (my $user = $sth->fetchrow_array()) {
		$users->{$user} = 1;
	}
	$sth->finish();
}

$sth = $dbh->prepare("SHOW DATABASES LIKE 'thinkupstart_%'");
$sth->execute();
while (my $db = $sth->fetchrow_array()) {
	$engine->{$db} = {};
}
$sth->finish();

if ($options->{'engine'}) {
	foreach my $db (keys %{$engine}) {
		$dbh->do(sprintf('USE %s',$db));
		$sth = $dbh->prepare('SHOW TABLES');
		$sth->execute();
		while (my $table = $sth->fetchrow_array()) {
			$engine->{$db}->{$table} = '';
		}
	}
	$sth->finish();
	
	foreach my $db (keys %{$engine}) {
		$dbh->do(sprintf('USE %s',$db));
		foreach my $table (keys %{$engine->{$db}}) {
			my (undef,$desc) = $dbh->selectrow_array(sprintf('SHOW CREATE TABLE %s',$table));
	
			$engine->{$db}->{$table} = $1 if ($desc =~ m{ENGINE=(.*?) });
		}
	}
	$sth->finish();
}

if ($options->{'users'}) {
	my %temp_users = %{$users};
	my %temp_engine = %{$engine};

	foreach (map { my $a = $_; $a =~ s{thinkupstart_}{}; $a; } (keys %{$engine})) {
		delete $temp_users{$_};
	}
	foreach (map { 'thinkupstart_'.$_; } (keys %{$users})) {
		delete $temp_engine{$_};
	}

	if (keys %temp_users) {
		print "ACTIVE USERS WITHOUT A thinkupstart_* DATABASE:\n";
		foreach (sort keys %temp_users) {
			print "\t$_\n";
		}
		$output = 1;
	}
	if (keys %temp_engine) {
		print "\n" if $output;
		print "thinkupstart_* DATABASES WITHOUT AN ACTIVE USER:\n";
		foreach (sort keys %temp_engine) {
			print "\t$_\n";
		}
		$output = 1;
	}
}

my $first = 1;
if ($options->{'engine'}) {
	foreach my $db (keys %{$engine}) {
	
		my @cmds = ();
		foreach my $table (keys %{$engine->{$db}}) {
			if ($engine->{$db}->{$table} ne 'InnoDB') {
				push @cmds,"\tALTER TABLE $table ENGINE = InnoDB;\n";
			}
		}

		if (int(@cmds)) {
			unshift @cmds,"\tUSE $db;\n";
			if ($options->{'engine'} eq 'dump') {
				print "\n" if $output;
				print "SQL COMMANDS TO CONVERT NON-INNODB DATABASES:\n" if $first;
				print join('',@cmds);
			}
			else {
				print "\n" if $output && $first;
				print "CONVERTING NON-INNODB TABLES FOR:\n" if $first;
				print "\t$db\n";
				foreach (@cmds) {
					$dbh->do($_);
				}
			}
			$output = 1;
			$first = 0;
		}
	}
}
