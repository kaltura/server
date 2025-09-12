<?php

require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 3) {
	die('Error: Missing arguments.' . PHP_EOL .
		'Usage: php ' . $argv[0] . ' <partnerIds - comma separated> <pluginName> [realrun|dryrun]' . PHP_EOL);
}

$partnerIds = explode(',', $argv[1]);
$pluginName = $argv[2];
$dryRun = true;

if (isset($argv[3]) && $argv[3] === 'realrun') {
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::setLogger(new KalturaStdoutLogger());
KalturaLog::info($dryRun ? 'DRY RUN MODE' : 'REAL RUN MODE');

foreach ($partnerIds as $partnerId) {
	$partner = PartnerPeer::retrieveByPK($partnerId);
	if (!$partner) {
		KalturaLog::err("Error: Partner with ID {$partnerId} not found." . PHP_EOL);
		continue;
	}
	if (!$dryRun) {
		PermissionPeer::disablePlugin($pluginName, $partnerId);
		kEventsManager::flushEvents();
		kMemoryManager::clearMemory();
		KalturaLog::info("{$pluginName} plugin successfully disabled for partner {$partnerId}");
	} else {
		KalturaLog::info("DRY RUN: Would disable {$$pluginName} for partner {$partnerId}");
	}
}

KalturaLog::info("Script completed successfully!");
