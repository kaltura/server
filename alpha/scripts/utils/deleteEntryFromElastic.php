<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

const UNIX_LINE_END = "\n";

if ($argc < 2)
{
	echo UNIX_LINE_END . ' ---- Delete Entry From Elastic ---- ' . UNIX_LINE_END;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/entries_id.log || entryId_1,entryId_2,entryId_3,.. ] [realrun / dryrun]' . UNIX_LINE_END;
	die(' Error: missing entry_id file or csv list ' . UNIX_LINE_END . UNIX_LINE_END);
}

if (is_file($argv[1]))
{
	$entriesIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] .'"' . UNIX_LINE_END);
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
	die (' Error: invalid input supplied at: "' . $argv[1] . '"' . UNIX_LINE_END);
}

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
	$dryRun = false;
}
KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$elasticSearchManager = new kElasticSearchManager();
$successEntries = array();
$failedEntries = array();
$skippedEntries = array();
$dryRunEntries = array();

foreach ($entriesIds as $entryId)
{
	$entryId = trim($entryId);
	$entry = entryPeer::retrieveByPKNoFilter($entryId);


	if ($entry->shouldDeleteFromElastic())
	{
		if (!$dryRun)
		{
			try
			{
				$elasticSearchManager->deleteFromElastic($entry);
				$successEntries[] = $entry->getId();
			} catch (Exception $e)
			{
				$failedEntries[] = $entry->getId();
				KalturaLog::warning('Failed to execute elastic for entry Id: ' . $entry->getId());
			}
		}
		else
		{
			$dryRunEntries[] = $entry->getId();
		}
	}
	else
	{
		$skippedEntries[] = $entry->getId();
	}
}
KalturaLog::info('Done');

/* Display Entries Result */
if($successEntries)
{
	echo UNIX_LINE_END . ' ---- Success Entries ---- ' . UNIX_LINE_END;
	foreach ($successEntries as $successEntry)
	{
		echo $successEntry . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}

if ($failedEntries)
{
	echo UNIX_LINE_END . ' ---- Failed Entries ---- ' . UNIX_LINE_END;
	foreach ($failedEntries as $failedEntry)
	{
		echo $failedEntry . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}

if ($skippedEntries)
{
	echo UNIX_LINE_END . ' ---- Skipped Entries ---- ' . UNIX_LINE_END;
	foreach ($skippedEntries as $skippedEntry)
	{
		echo $skippedEntry . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}

if ($dryRunEntries)
{
	echo UNIX_LINE_END . ' ---- Note will be deleted from Elastic ---- ' . UNIX_LINE_END;
	foreach ($dryRunEntries as $dryRunEntry)
	{
		echo $dryRunEntry . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}
?>
