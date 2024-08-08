<?php

require_once (dirname(__FILE__).'/../bootstrap.php');
ini_set("memory_limit", "1024M");
define('ENTRIES_CHUNK', 500);

$lockTimeout = 5;
$lockDuration = 1 * 60 * 60; // one hour
$lockName = 'kava_plays_views_sync';
$lock = kLock::create($lockName);
if ($lock && !$lock->lock($lockTimeout, $lockDuration)) 
{
	KalturaLog::err('Failed to acquire script lock, aborting script.');
	die('Failed to acquire script lock, aborting script.' . PHP_EOL);
}

function handleChunk($currIdsMap)
{
	global $fp, $map;

	$entries = entryPeer::retrieveByPKsNoFilter(array_keys($currIdsMap));
	foreach ($entries as $entry)
	{
		$entryId = $entry->getId();
		if ($entry->getIsRecordedEntry())
		{
			$liveEntryId = $entry->getRootEntryId();
			$map[$liveEntryId][] = $currIdsMap[$entryId];
		}

		if ($entry->getType() != entryType::LIVE_STREAM)
		{
			// if entry is not live no need to keep it in map
			fwrite($fp, $currIdsMap[$entryId] . "\n");
		}
		else
		{
			$map[$entryId][] = $currIdsMap[$entryId];
		}
	}
}

// parse the command line
if ($argc < 2)
{
	echo "Usage:\n\t" . basename(__file__) . " <output file name>\n";
	echo "For example, php " . basename(__file__) . " /tmp/playsviewsDump.txt\n";
	exit(1);
}

$fp = fopen($argv[1], 'w');
if (!$fp)
{
	die('Failed to open tmp file');
}

KalturaLog::log('Copy Script Started');
$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();
$connection = Propel::getConnection();

$map = array();
$currIdsMap = array();
$f = fopen('php://stdin', 'r');

while ($s = trim(fgets($f)))
{
	$sep = strpos($s, "\t") ? "\t" : " ";
	$curEntryArr = explode($sep, $s);
	$entryId = reset($curEntryArr);
	$currIdsMap[$entryId] = $s;

	if (count($currIdsMap) == ENTRIES_CHUNK)
	{
		handleChunk($currIdsMap);
		$currIdsMap = array(); //reset
	}
}

if (count($currIdsMap) > 0)
{
	handleChunk($currIdsMap);
	$currIdsMap = array(); //reset
}

//write output
foreach ($map as $entryId => $values)
{
	//write row as is
	if (count($values) == 1)
	{
		fwrite($fp, reset($values) . "\n");
		continue;
	}

	$entryValues = array(
		0 => $entryId
	);

	foreach ($values as $entryStr)
	{
		$sep = strpos($entryStr, "\t") ? "\t" : " ";
		$currEntryValues = explode($sep, $entryStr);

		// update last played at
		if (isset($entryValues[1]))
		{
			$entryValues[1] = max($entryValues[1], $currEntryValues[1]);
		}
		else
		{
			$entryValues[1] = $currEntryValues[1];
		}

		// update the rest of the metrics
		for ($i = 2; $i < count($currEntryValues); $i++)
		{
			if (isset($entryValues[$i]))
			{
				$entryValues[$i] += $currEntryValues[$i];
				continue;
			}
			$entryValues[$i] = $currEntryValues[$i];
		}
	}

	fwrite($fp, implode("\t", $entryValues) . "\n");
}
fclose($fp);

if ($lock)
{
	$lock->unlock();
}
