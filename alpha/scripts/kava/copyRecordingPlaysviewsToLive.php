<?php

require_once (dirname(__FILE__).'/../bootstrap.php');

ini_set( "memory_limit","1024M" );

define('ENTRIES_CHUNK', 500);
define('TMP_FILE_PATH', '/tmp/playsviewsDump.txt');

$f = fopen("php://stdin", "r");
KalturaLog::log('Copy Script Started');
$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();
$connection = Propel::getConnection();

function handleChunk($currIdsMap)
{
	global $map;

	myPartnerUtils::resetAllFilters();
	myPartnerUtils::resetPartnerFilter('entry');
	$entries = entryPeer::retrieveByPKS(array_keys($currIdsMap));
	foreach ($entries as $entry)
	{
		if ($entry->getIsRecordedEntry())
		{
			$liveEntryId = $entry->getRootEntryId();
			$map[$liveEntryId][] = $currIdsMap[$entry->getId()];
		}
	}
}

$map = array();
$currIdsMap = array();

while ($s = trim(fgets($f)))
{
	$sep = strpos($s, "\t") ? "\t" : " ";
	$curEntryArr = explode($sep, $s);
	$entryId = reset($curEntryArr);
	$currIdsMap[$entryId] = $s;
	$map[$entryId][] = $s;

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
$fp = fopen(TMP_FILE_PATH, 'w');
if (!$fp)
{
	die('Failed to open tmp file');
}

foreach ($map as $entryId => $assocValues)
{
	//write row as is
	if (count($assocValues) == 1)
	{
		fwrite($fp, reset($assocValues) . "\n");
		continue;
	}

	$entryValues = array(
		0 => $entryId
	);

	foreach ($assocValues as $entryStr)
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
		for ($i = 2; $i < 10; $i++)
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
