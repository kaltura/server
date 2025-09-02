<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Starting update status of category kuser\n";

const UNIX_LINE_END = PHP_EOL;

if ($argc < 2) {
	echo "Usage: php $argv[0] <categoryKuserID_file_or_list> [<realrun | dryrun>]\n";
	die();
}

$categoryKuserIds = [];
if (is_file($argv[1])) {
	$categoryKuserIds = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
} elseif (strpos($argv[1], ',')) {
	$categoryKuserIds = explode(',', $argv[1]);
} elseif (is_numeric($argv[1])) {
	$categoryKuserIds[] = $argv[1];
} else {
	die("Error: invalid input supplied at: '{$argv[1]}'\n");
}

$categoryKuserId = $argv[1];

$dryrun = true;
if ($argc == 3 && $argv[2] == 'realrun') {
	$dryrun = false;
}

KalturaStatement::setDryRun($dryrun);
KalturaLog::debug("dryrun value: [$dryrun]\n");

foreach ($categoryKuserIds as $categoryKuserId) {
	$categoryKuserId = trim($categoryKuserId);
	$categoryKuser = categoryKuserPeer::retrieveByPK($categoryKuserId);

	if (!$categoryKuser) {
		KalturaLog::warning("Category Kuser not found for ID: $categoryKuserId");
		continue;
	}

	$categoryKuser->setStatus(categoryKuserStatus::DELETED);
	if (!$dryrun) {
		$categoryKuser->save();
	}
	kEventsManager::flushEvents();
	KalturaLog::debug("Category Kuser $categoryKuserId status was updated\n");
}

KalturaLog::info("Done");
