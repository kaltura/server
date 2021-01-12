<?php
if($argc < 3)
{
	echo "Usage: php $argv[0] [adminKs] [serviceUrl] <dryRunMode> <maxEntries>".PHP_EOL;
	die("Not enough parameters" . "\n");
}

require_once(__DIR__ . '/../../../batch/bootstrap.php');

function getMRProfiles($client)
{
	$filter = new KalturaScheduledTaskProfileFilter();
	$pager = new KalturaFilterPager();
	$pager->pageIndex = 1;
	$pager->pageSize = 500;
	$scheduledTaskClient = KalturaScheduledTaskClientPlugin::get($client);
	$result = $scheduledTaskClient->scheduledTaskProfile->listAction($filter, $pager);
	kalturaLog::info("Found count {$result->totalCount} profiles");
	return $result->objects;
}

function filterProfiles($profiles)
{
	$subTasksProfiles = array();
	foreach ($profiles as $profile)
	{
		if(kString::beginsWith($profile->name, 'MR_'))
		{
			$subTasksProfiles[] = $profile;
		}
	}

	return $subTasksProfiles;
}

function getMetadataOnObject($metadataPlugin, $objectId, $metadataProfileId)
{
	$filter = new KalturaMetadataFilter();
	$filter->metadataProfileIdEqual = $metadataProfileId;
	$filter->objectIdEqual = $objectId;
	$result = $metadataPlugin->metadata->listAction($filter, null);
	if ($result->totalCount > 0)
	{
		return $result->objects[0];
	}

	return null;
}

function getClient($serviceUrl, $adminKs)
{
	$config = new KalturaConfiguration();
	$config->clientTag = 'KScheduledTaskRunner';
	$config->serviceUrl = $serviceUrl;
	$client = new KalturaClient($config);
	$client->setKs($adminKs);

	return $client;
}

function getMrIdAndSubTaskIndexFromProfile($profile)
{
	return explode(',', $profile->objectFilter->advancedSearch->items[0]->items[1]->value);
}

/**
 * @param $client
 * @param $metadataPlugin
 * @param KalturaScheduledTaskProfile $profile
 * @param bool $dryRunMode
 * @param int $maxEntries
 * @param int $entriesHandledCount
 */
function processEntries($client, $metadataPlugin, $profile, $dryRunMode, $maxEntries, &$entriesHandledCount)
{
	$updatedDay = getUpdatedDay($profile);
	$entryList = $client->baseEntry->listAction($profile->objectFilter);
	$entries = $entryList->objects;
	$metadataProfileId = $profile->objectFilter->advancedSearch->items[0]->metadataProfileId;
	$newUpdatedDay = $updatedDay + 1;
	$mrpFilterArr = getMrIdAndSubTaskIndexFromProfile($profile);
	$mrId = $mrpFilterArr[0];
	$subTaskIndex = $mrpFilterArr[1];
	$newXmlVal = "$mrId,$subTaskIndex,$newUpdatedDay";
	foreach ($entries as $entry)
	{
		handleEntry($metadataPlugin, $entry, $metadataProfileId, $updatedDay, $mrId, $newXmlVal, $dryRunMode, $entriesHandledCount);
		if($entriesHandledCount >= $maxEntries)
		{
			kalturaLog::info("Reached max entries limit, {$entriesHandledCount} were handled");
			return;
		}
	}
}

function handleEntry($metadataPlugin, $entry, $metadataProfileId, $updatedDay, $mrId, $newXmlVal, $dryRunMode, &$entriesHandledCount)
{
	$metadata = getMetadataOnObject($metadataPlugin, $entry->id, $metadataProfileId);
	$xml_string = ($metadata && $metadata->xml) ? $metadata->xml : null;
	if($xml_string)
	{
		$xml = simplexml_load_string($xml_string);
		$mrpData = $xml->xpath('/metadata/MRPData');
		$dataRows = count($mrpData);
		for ($i = 0; $i < $dataRows; $i++)
		{
			if (kString::beginsWith($mrpData[$i], $mrId . ','))
			{
				$MRPData = explode(',', $mrpData[$i][0]);
				if(count($MRPData) != 3)
				{
					KalturaLog::err("Illegal MRP data for {$entry->id} {$mrpData[$i][0]}");
					return;
				}

				$day = $MRPData[2];
				if($day < $updatedDay)
				{
					$dateToExecute = $day * 86400;
					$dt = new DateTime("@$dateToExecute");
					$dayInDateFormat = $dt->format('Y-m-d H:i:s');
					kalturaLog::info("{$entry->id} date was suppose to be {$updatedDay} aka {$dayInDateFormat}");
					if($dryRunMode)
					{
						kalturaLog::info("DRY RUN Updated {$entry->id} MRP");
					}
					else
					{
						$mrpData[$i][0] = $newXmlVal;
						$result = $metadataPlugin->metadata->update($metadata->id, $xml->asXML());
						if($result)
						{
							kalturaLog::info("Updated {$entry->id} MRP");
						}
						else
						{
							kalturaLog::err("Failed to update {$entry->id} MRP");
						}
					}

					$entriesHandledCount++;
				}
			}
		}
	}
}

/**
 * @param KalturaScheduledTaskProfile $profile
 * @return int
 */
function getUpdatedDay($profile)
{
	$now = intval(time() / 86400);  // as num of sec in day to get day number
	return $now - $profile->description;
}

function main($serviceUrl, $adminKs, $dryRunMode, $maxEntries)
{
	$client = getClient($serviceUrl, $adminKs);
	$metadataPlugin = KalturaMetadataClientPlugin::get($client);
	$entriesHandledCount = 0;
	$scheduledTaskProfiles = getMRProfiles($client);
	$profiles = filterProfiles($scheduledTaskProfiles);
	foreach($profiles as $profile)
	{
		/** @var KalturaScheduledTaskProfile $profile */
		kalturaLog::info("working on profile {$profile->name} id {$profile->id} with wait days {$profile->description}");
		processEntries($client, $metadataPlugin, $profile, $dryRunMode, $maxEntries, $entriesHandledCount);
		if($entriesHandledCount >= $maxEntries)
		{
			return;
		}
	}

	return $entriesHandledCount;
}

$adminKs = $argv[1];
$serviceUrl = $argv[2];
$dryRunMode = true;
$maxEntries = 1000;

if(isset($argv[3]))
{
	$dryRunMode = $argv[3];
}

if(isset($argv[4]))
{
	$maxEntries = $argv[4];
}
$entriesHandledCount = main($serviceUrl, $adminKs, $dryRunMode, $maxEntries);
kalturaLog::info("Reset old MR script finished and updated {$entriesHandledCount} entries");