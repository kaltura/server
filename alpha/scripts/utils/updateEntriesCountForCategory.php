<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Starting script to update entriesCount for category ";

if ($argc < 3 )
	die("Usage: php $argv[0] update entriesCount for category <realrun | dryrun>" . "\n");

$categoryId = $argv[1];
$newEntriesCount =  $argv[2];

$dryrun = true;
if ($argc == 4 && $argv[3] == 'realrun') {
	$dryrun = false;
}

KalturaStatement::setDryRun($dryrun);
KalturaLog::debug('dryrun value: [' . $dryrun . ']');

$category = categoryPeer::retrieveByPK($categoryId);
$currentEntriesCount = $category->getEntriesCount();
KalturaLog::debug("Current entries count for category with ID $categoryId is $currentEntriesCount\n");

if ($currentEntriesCount == $newEntriesCount) {
	KalturaLog::debug("No update needed, current entries count is already $currentEntriesCount\n");
	exit(0);
}

$category->setEntriesCount($newEntriesCount);
$category->save();

KalturaLog::debug("Updated entriesCount to $newEntriesCount for category with ID $categoryId\n");

