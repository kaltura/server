<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

const UNIX_LINE_END = PHP_EOL;

if ($argc < 2)
{
	echo UNIX_LINE_END . ' ---- Delete Kuser From Elastic ---- ' . UNIX_LINE_END;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/kuser_id_file (one ID per line) || kuserId_1, kuserId_2, kuserId_3,.. ] [realrun | dryrun]' . UNIX_LINE_END;
	die(' Error: missing kuser_id file or csv list ' . UNIX_LINE_END . UNIX_LINE_END);
}

if (is_file($argv[1]))
{
	$kuserIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] .'"' . UNIX_LINE_END);
}
elseif (strpos($argv[1], ','))
{
	$kuserIds = explode(',', $argv[1]);
}
elseif (is_numeric($argv[1]))
{
	$kuserIds[] = $argv[1];
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
$successKusers = array();
$failedKusers = array();
$skippedKusers = array();
$dryRunKusers = array();

foreach ($kuserIds as $kuserId)
{
	$kuserId = trim($kuserId);
	$kuser = kuserPeer::retrieveByPKNoFilter($kuserId);

	if (!$kuser) {
		KalturaLog::warning('Kuser not found for ID: ' . $kuserId);
		$failedKusers[] = $kuserId;
		continue;
	}

	if ($kuser->shouldDeleteFromElastic())
	{
		if (!$dryRun)
		{
			try
			{
				$elasticSearchManager->deleteFromElastic($kuser);
				$successKusers[] = $kuser->getId();
			} catch (Exception $e)
			{
				$failedKusers[] = $kuser->getId();
				KalturaLog::warning('Failed to execute elastic for kuser Id: ' . $kuser->getId());
			}
		}
		else
		{
			$dryRunKusers[] = $kuser->getId();
		}
	}
	else
	{
		$skippedKusers[] = $kuser->getId();
	}
}
KalturaLog::info('Done');

/* Display Kusers Result */
if($successKusers)
{
	echo UNIX_LINE_END . ' ---- Success Kusers ---- ' . UNIX_LINE_END;
	foreach ($successKusers as $successKuser)
	{
		echo $successKuser . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}

if ($failedKusers)
{
	echo UNIX_LINE_END . ' ---- Failed Kusers ---- ' . UNIX_LINE_END;
	foreach ($failedKusers as $failedKuser)
	{
		echo $failedKuser . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}

if ($skippedKusers)
{
	echo UNIX_LINE_END . ' ---- Skipped Kusers ---- ' . UNIX_LINE_END;
	foreach ($skippedKusers as $skippedKuser)
	{
		echo $skippedKuser . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}

if ($dryRunKusers)
{
	echo UNIX_LINE_END . ' ---- Note will be deleted from Elastic ---- ' . UNIX_LINE_END;
	foreach ($dryRunKusers as $dryRunKuser)
	{
		echo $dryRunKuser . UNIX_LINE_END;
	}
	echo UNIX_LINE_END;
}
?>
