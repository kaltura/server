<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$partnerId = null;
if($argc > 1 && is_numeric($argv[1]))
	$partnerId = $argv[1];
	
if(in_array('dryRun', $argv))
	KalturaStatement::setDryRun(true);
	
kCurrentContext::$master_partner_id = -2;
kCurrentContext::$uid = "PARTNER USAGE DAEMON";

// Make sure that events will be performed immediately (e.g. creating a new kuser for the given puser)
kEventsManager::enableDeferredEvents(false);

$batchClient = new myBatchPartnerUsage($partnerId);

KalturaLog::debug('Done.');
