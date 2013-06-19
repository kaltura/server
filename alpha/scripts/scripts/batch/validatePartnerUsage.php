<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$partnerId = null;
if($argc > 1 && is_numeric($argv[1]))
	$partnerId = $argv[1];
	
if(in_array('dryRun', $argv))
	KalturaStatement::setDryRun(true);
	
$batchClient = new myBatchPartnerUsage($partnerId);

KalturaLog::debug('Done.');
