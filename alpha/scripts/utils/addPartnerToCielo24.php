<?php
	if($argc < 4)
		die("Usage: php " . basename(__FILE__) . " [partner id] [username] [password]" . PHP_EOL);

	require_once(__dir__ . '/../bootstrap.php');	
	
	$partnerId = $argv[1];
	$username = $argv[2];
	$password = $argv[3];
	
	$options = new Cielo24Options($username, $password);
	Cielo24Plugin::setPartnerCielo24Options($partnerId, $options);
	
