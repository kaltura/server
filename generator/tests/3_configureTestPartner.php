<?php

require_once(__DIR__ . '/utils.php');

if ($argc < 2)
	die("Usage:\n\tphp " . basename(__file__) . " <root dir>\n");
	
$rootDir = fixSlashes($argv[1]);

$config = parse_ini_file(dirname(__file__) . '/config.ini', true);

$search = array(
	'54321', 
	'YOUR_USER_SECRET', 
	'YOUR_ADMIN_SECRET');
	
$replace = array(
	$config['general']['partner_id'],
	$config['general']['user_secret'],
	$config['general']['admin_secret']);

replaceInFolder($rootDir, null, array('.tar.gz', 'configureTestPartner.php'), $search, $replace);
