<?php

require_once('utils.php');

$config = parse_ini_file(dirname(__file__) . '/config.ini', true);

$search = array(
	'54321', 
	'YOUR_USER_SECRET', 
	'YOUR_ADMIN_SECRET');
	
$replace = array(
	$config['general']['partner_id'],
	$config['general']['user_secret'],
	$config['general']['admin_secret']);

replaceInFolder(dirname(__file__), null, array('.tar.gz', 'configureTestPartner.php'), $search, $replace);
