<?php

if($argc != 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {entry id} {realrun / dryrun}\n";
	exit;
} 
$entryId = $argv[1];
$dryRun = ($argv[2] != 'realrun');

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);

$entry = entryPeer::retrieveByPK($entryId);
if(!$entry)
{
	echo "Entry id [$entryId] not found\n";
	exit(-1);
}

if(!($entry instanceof LiveEntry))
{
	echo "Entry id [$entryId] is not live entry\n";
	exit(-1);
}

/* @var $entry LiveEntry */

$entry->setSyncDCs(true);
$entry->save();

KalturaLog::debug('Done');
