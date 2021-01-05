<?php
if($argc < 4)
{
	echo "Usage: php $argv[0] [serviceUrl] [partnerId] [adminSecret] <dryRunMode> <maxEntries>".PHP_EOL;
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

function getClient($serviceUrl, $partnerId, $secret)
{
	$config = new KalturaConfiguration($partnerId);
	$config->clientTag = 'KScheduledTaskRunner';
	$config->serviceUrl = $serviceUrl;
	$client = new KalturaClient($config);
	$result = $client->session->start($secret, null, KalturaSessionType::ADMIN, $partnerId, null, null);
	$client->setKs($result);

	return $client;
}

function getMrIdAndSubTaskIndexFromProfile($profile)
{
	return explode(',', $profile->objectFilter->advancedSearch->items[0]->items[1]->value);
}

/**
 * @param $client
 * @param KalturaScheduledTaskProfile $profile
 * @param bool $dryRunMode
 * @param int $maxEntries
 * @param int $entriesHandledCount
 */
function updateEntries($client, $profile, $dryRunMode, $maxEntries, &$entriesHandledCount)
{
	$metadataPlugin = KalturaMetadataClientPlugin::get($client);
	$updatedDay = getUpdatedDay($profile);
	$entryList = $client->baseEntry->listAction($profile->objectFilter);
	$entries = $entryList->objects;
	foreach ($entries as $entry)
	{
		$metadataProfileId = $profile->objectFilter->advancedSearch->items[0]->metadataProfileId;
		$metadata = getMetadataOnObject($metadataPlugin, $entry->id, $metadataProfileId);
		$xml_string = ($metadata && $metadata->xml) ? $metadata->xml : null;
		if($xml_string)
		{
			$xml = simplexml_load_string($xml_string);
			$mprsData = $xml->xpath('/metadata/MRPData');
			$newUpdatedDay = $updatedDay + 1;
			$mrpFilterArr = getMrIdAndSubTaskIndexFromProfile($profile);
			$mrId = $mrpFilterArr[0];
			$subTaskIndex = $mrpFilterArr[1];
			$newXmlVal = "$mrId,$subTaskIndex,$newUpdatedDay";
			for ($i = 0; $i < count($mprsData); $i++)
			{
				if (kString::beginsWith($mprsData[$i], $mrId . ','))
				{
					$MRPData = explode(',', $mprsData[$i][0]);
					if(count($MRPData) != 3)
					{
						KalturaLog::ERR("Illegal MRP data for {$entry->id} {$mprsData[$i][0]}");
						continue;
					}

					$day = $MRPData[2];
					if($day < $updatedDay)
					{
						if($dryRunMode)
						{
							kalturaLog::info("DRY RUN Updated {$entry->id} MRP");
						}
						else
						{
							$mprsData[$i][0] = $newXmlVal;
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

		if($entriesHandledCount >= $maxEntries)
		{
			kalturaLog::info("Reached max entries limit, {$entriesHandledCount} were handled");
			return;
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

function main($serviceUrl, $partnerId, $adminSecret, $dryRunMode, $maxEntries)
{
	$client = getClient($serviceUrl, $partnerId, $adminSecret);
	$entriesHandledCount = 0;
	$scheduledTaskProfiles = getMRProfiles($client);
	$profiles = filterProfiles($scheduledTaskProfiles);
	foreach($profiles as $profile)
	{
		/** @var KalturaScheduledTaskProfile $profile */
		kalturaLog::info("working on profile {$profile->name}");
		updateEntries($client, $profile, $dryRunMode, $maxEntries, $entriesHandledCount);
		if($entriesHandledCount >= $maxEntries)
		{
			return;
		}
	}

	return $entriesHandledCount;
}

$serviceUrl = $argv[1];
$partnerId = $argv[2];
$adminSecret = $argv[3];
$dryRunMode = true;
$maxEntries = 1000;

if(isset($argv[4]))
{
	$dryRunMode = $argv[4];
}

if(isset($argv[5]))
{
	$maxEntries = $argv[5];
}
$entriesHandledCount = main($serviceUrl, $partnerId, $adminSecret, $dryRunMode, $maxEntries);
kalturaLog::info("finished updated {$entriesHandledCount} entry");