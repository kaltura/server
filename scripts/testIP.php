<?php
require_once 'bootstrap.php';

if ($argc < 2)
{
	die('pleas provide IP address as input' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is IP address' . PHP_EOL);
}

$myLocator = new myIPGeocoder();

$country = $myLocator->iptocountry( $argv[1] );

echo "country: $country";

