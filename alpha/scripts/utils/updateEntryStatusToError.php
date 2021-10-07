<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 2)
{
	KalturaLog::info(' ---- Update List Of Pending Clipping Entries ---- ');
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
	if (!$entry)
	{
		$failedEntries[] = $entryId;
		KalturaLog::debug('ERROR: could not find entry id [' . $entryId . ']');
		continue;
	}
	if ($entry -> getStatus() == entryStatus::PENDING)
	{
		try
		{
			$entry->setStatus(entryStatus::ERROR_CONVERTING);
			$entry->save();
			$successEntries[] = $entry->getId();
		}
		catch (Exception $e)
		{
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
	KalturaLog::info(' ---- Entries Counts ---- ');
	KalturaLog::info('Number of total entries ' . $totalEntries);

	if ($successEntries)
	{
		KalturaLog::info(' ---- Success Entries ---- ');
		KalturaLog::info('Number of successful entries ' . count($successEntries));
		foreach ($successEntries as $successEntry)
		{
			KalturaLog::info($successEntry);
		}
	}

	if ($failedEntries)
	{
		KalturaLog::info(' ---- Failed Entries ---- ');
		KalturaLog::info('Number of failed entries ' . count($failedEntries));
		foreach ($failedEntries as $failedEntry)
		{
			KalturaLog::info($failedEntry);
		}
	}
}