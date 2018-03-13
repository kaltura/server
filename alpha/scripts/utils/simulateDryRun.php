<?php
require_once('/opt/kaltura/web/content/clientlibs/batchClient/KalturaClient.php');
//require_once('/opt/kaltura/web/content/clientlibs/batchClient/KalturaPlugins/KalturaScheduledTaskClientPlugin.php');

function getInstanceByType($type, KalturaClient $client)
{
	switch($type)
	{
		case KalturaObjectFilterEngineType::ENTRY:
			return new KObjectFilterBaseEntryEngine($client);
		default:
			return KalturaPluginManager::loadObject('KObjectFilterEngineBase', $type, array($client));
	}
}

function getFilter($scheduledTaskProfile)
{
	$objectFilterEngineType = $scheduledTaskProfile->objectFilterEngineType;
	if($objectFilterEngineType != KalturaObjectFilterEngineType::ENTRY)
	{
		echo "filter type not supported by script.\n";
		die;
	}

	$filter = $scheduledTaskProfile->objectFilter;
	$filter->orderBy = KalturaBaseEntryOrderBy::CREATED_AT_ASC;
	return $filter;
}

function query(KalturaClient $client, $filter, KalturaFilterPager $pager)
{
	return $client->baseEntry->listAction($filter, $pager);
}

function getIds($entries, $includeCreateTime)
{
	$result = array();
	foreach ($entries as $entry)
	{
		if($entry->createdAt == $includeCreateTime)
			$result[] = $entry->id;
	}


	return implode (", ", $result);
}

if($argc < 5)
{
	echo "Missing arguments.\n";
	echo "php simulateDryRun.php {profileId} {filePath} {admin ks} {url} {max results}.\n";
	die;
}

$profileId = $argv[1];
$filePath = $argv[2];
$ks =  $argv[3];
$url = $argv[4];

if($argc > 5)
{
	$maxResults = $argv[5];
}
else
{
	$maxResults = PHP_INT_MAX;
}


$config = new KalturaConfiguration(-2);
$config->serviceUrl = $url;
$client = new KalturaClient($config);
$client->setKs($ks);
$scheduledTaskClient = KalturaScheduledTaskClientPlugin::get($client);
$scheduledTaskProfile = $scheduledTaskClient->scheduledTaskProfile->get($profileId);
$pager = new KalturaFilterPager();
$pager->pageSize = 500;
$pager->pageIndex = 1;
$resultsCount = 0;
$handle = fopen($filePath, "w");
$client->setPartnerId($scheduledTaskProfile->partnerId);
$filter = getFilter($scheduledTaskProfile);
while(true)
{
	$results = query($client, $filter, $pager);
	if (!count($results->objects))
		break;

	$objects = $results->objects;
	$resultsCount += count($objects);
	foreach ($objects as $object)
	{
		fwrite ($handle, serialize($object).PHP_EOL);
	}

	if ($resultsCount >= $maxResults || $resultsCount < 500)
		break;

	/* @var $filter KalturaBaseEntryFilter */
	$lastResult = end($objects);
	$filter->createdAtGreaterThanOrEqual = $lastResult->createdAt;
	$filter->idNotIn = getIds($objects, $lastResult->createdAt);
	echo($resultsCount.PHP_EOL);
}

fwrite ($handle, "Total results: {$resultsCount}".PHP_EOL);
fclose($handle);