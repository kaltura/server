<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 2)
{
	echo PHP_EOL . ' ---- Update Pending Clipping Entries ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/entries_id_list || entryId_1,entryId_2,.. || entry_id ] [realrun / dryrun]' . PHP_EOL;
	die(' Error: missing entry_ids file, csv or single entry ' . PHP_EOL . PHP_EOL);
}

elseif (is_file($argv[1]))
{
	$entriesIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] .'"' . PHP_EOL);
}
elseif (strpos($argv[1], ','))
{
	$entriesIds = explode(',', $argv[1]);
}
elseif (strpos($argv[1],'_'))
{
	$entriesIds[] = $argv[1];
}
else
{
	die (' Error: invalid input supplied at: "' . $argv[1] . '"' . PHP_EOL);
}

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
	$dryRun = false;
}
KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$totalEntries = count($entriesIds);

$successEntries = array();
$failedEntries = array();
$dryRunEntries = array();

foreach ($entriesIds as $entryId) {
	$entryId = trim($entryId);
	$entry = entryPeer::retrieveByPKNoFilter($entryId);
	/* @var $entry entry */

	if (!$entry) {
		KalturaLog::debug('ERROR: could not find entry id [' . $entry . ']');
		continue;
	}

	if (!$dryRun) {
		try {
			$entry->setStatus(entryStatus::ERROR_CONVERTING);
			$entry->save();
			$successEntries[] = $entry->getId();
		} catch (Exception $e) {
			$failedEntries[] = $entry->getId();
			KalturaLog::warning('Failed to set status for entry Id: ' . $entry->getId());
		}
	} else {
		$dryRunEntries[] = $entry->getId();
	}

	KalturaLog::info('Done');

	/* Display Entries Result */
	echo PHP_EOL . ' ---- Entries Count ---- ' . $totalEntries . PHP_EOL;

	if ($successEntries) {
		echo PHP_EOL . ' ---- Success Entries ---- ' . PHP_EOL;
		foreach ($successEntries as $successEntry) {
			echo $successEntry . PHP_EOL;
		}
		echo PHP_EOL;
	}

	if ($failedEntries) {
		echo PHP_EOL . ' ---- Failed Entries ---- ' . PHP_EOL;
		foreach ($failedEntries as $failedEntry) {
			echo $failedEntry . PHP_EOL;
		}
		echo PHP_EOL;
	}

	if ($dryRunEntries) {
		echo PHP_EOL . ' ---- Note will be deleted from Elastic ---- ' . PHP_EOL;
		foreach ($dryRunEntries as $dryRunEntry) {
			echo $dryRunEntry . PHP_EOL;
		}
		echo PHP_EOL;
	}
}