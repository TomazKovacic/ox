<?php

//https://github.com/nategood/commando


require('vendor/autoload.php');

$cmd = new Commando\Command();

/*
$cmd->option()
	->require()
	->describedAs('A person\'s name');
*/

$cmd->option('r')
	->aka('reload')
	->must( function() {
		print 'Reloaddddd.';
	});

print 'hello ' . $cmd[0];

print "\n----\n" . "oxc: OXC command line tool.\n";

