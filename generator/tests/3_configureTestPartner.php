<?php

require_once(__DIR__ . '/utils.php');

if ($argc < 2)
	die("Usage:\n\tphp " . basename(__file__) . " <root dir>\n");
	
$rootDir = fixSlashes($argv[1]);

$config = parse_ini_file(dirname(__file__) . '/config.ini', true);

$search = array(
	'@YOUR_PARTNER_ID@', 
	'@YOUR_USER_SECRET@', 
	'@YOUR_ADMIN_SECRET@',
	'@SERVICE_URL@');
	
$replace = array(
	$config['general']['partner_id'],
	$config['general']['user_secret'],
	$config['general']['admin_secret'],
	$config['general']['service_url']);

replaceInFolder($rootDir, null, array('.tar.gz', 'configureTestPartner.php'), $search, $replace, '.template', '');
