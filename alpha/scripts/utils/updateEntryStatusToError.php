<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 2)
{
	echo PHP_EOL . ' ---- Update List Of Pending Clipping Entries ---- ' . PHP_EOL;
	die (' Error execute script, Usage: php updateEntryStatusToError.php < /path/to/entries_id_list || entryId_1,entryId_2,.. || entry_id > < realrun / dryrun >' . PHP_EOL);
}

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$entriesIds = isValidInput($argv[1]);
$totalEntries = count($entriesIds);
$successEntries = array();
$failedEntries = array();

foreach ($entriesIds as $entryId)
{
	$entryId = trim($entryId);
	$entry = entryPeer::retrieveByPKNoFilter($entryId);
	/* @var $entry entry */
	if (!$entry) {
		$failedEntries[] = $entryId;
		KalturaLog::debug('ERROR: could not find entry id [' . $entryId . ']');
		continue;
	}
	if ($entry -> getStatus() == entryStatus::PENDING)
	{
		try {
			$entry->setStatus(entryStatus::ERROR_CONVERTING);
			$entry->save();
			$successEntries[] = $entry->getId();
		} catch (Exception $e) {
			$failedEntries[] = $entry->getId();
			KalturaLog::warning('Failed to set status for entry Id: ' . $entry->getId());
		}
	}
	else
	{
		$failedEntries[] = $entry->getId();
		KalturaLog::debug('ERROR: status is not pending for entry id [' . $entryId . ']');
		continue;
	}
}

echoResults($totalEntries, $successEntries, $failedEntries);


function isValidInput($entryInput)
{
	if (is_file($entryInput))
	{
		$entries = file($entryInput) or die (' Error: cannot open file at: "' . $entryInput .'"' . PHP_EOL);;
		return $entries;
	}
	elseif (strpos($entryInput, ','))
	{
		return explode(',', $entryInput);
	}
	elseif (strpos($entryInput,'_'))
	{
		return array($entryInput);
	}
	else
	{
		die (' Error: invalid input supplied at: "' . $entryInput . '"' . PHP_EOL);
	}
}

function echoResults($totalEntries, $successEntries, $failedEntries)
{
	KalturaLog::info('Done');

	/* Display Entries Result */
	echo PHP_EOL . ' ---- Entries Count ---- ' . PHP_EOL . $totalEntries . PHP_EOL;

	if ($successEntries) {
		echo PHP_EOL . ' ---- Success Entries ---- ' . PHP_EOL. count($successEntries) . PHP_EOL;
		foreach ($successEntries as $successEntry) {
			echo $successEntry . PHP_EOL;
		}
		echo PHP_EOL;
	}

	if ($failedEntries) {
		echo PHP_EOL . ' ---- Failed Entries ---- ' . PHP_EOL . count($failedEntries) . PHP_EOL;
		foreach ($failedEntries as $failedEntry) {
			echo $failedEntry . PHP_EOL;
		}
		echo PHP_EOL;
	}
}