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
	$entryList = $client->baseEntry->listAction($profile->objectFilter);
	$entries = $entryList->objects;
	$metadataProfileId = $profile->objectFilter->advancedSearch->items[0]->metadataProfileId;
	foreach ($entries as $entry)
	{
		$entriesHandledCount++;
		$metadata = getMetadataOnObject($metadataPlugin, $entry->id, $metadataProfileId);
		$xml_string = ($metadata && $metadata->xml) ? $metadata->xml : null;
		if ($xml_string)
		{
			$xml = simplexml_load_string($xml_string);
			$mrpData = $xml->xpath('/metadata/MRPData');
			$mrpsOnEntry = $xml->xpath('/metadata/MRPsOnEntry');
			$dataRows = count($mrpData);
			if ($dataRows != count($mrpsOnEntry))
			{
				kalturaLog::err("{$entry->id} have bugged MR data");
				return;
			}

			$uniqueMrpsOnEntry = array();
			$newXml = new SimpleXMLElement("<metadata/>");
			$newXml->addChild('Status', 'Enabled');
			$shouldUpdate = false;
			for ($i = 0; $i < $dataRows; $i++)
			{
				$mrpsOnEntryString = $mrpsOnEntry[$i][0]->__toString();
				if (isset($uniqueMrpsOnEntry[$mrpsOnEntryString]))
				{
					$shouldUpdate = true;
				}
				else
				{
					$uniqueMrpsOnEntry[$mrpsOnEntryString] = true;
					$newXml->addChild('MRPsOnEntry', $mrpsOnEntryString);
					$newXml->addChild('MRPData', $mrpData[$i][0]->__toString());
				}
			}

			if ($shouldUpdate)
			{
				$newXmlString = $newXml->asXML();
				kalturaLog::info("{$entry->id} needs MR updated");
				kalturaLog::info("old data {$xml_string}");
				kalturaLog::info("new data {$newXmlString}");
				if ($dryRunMode)
				{
					kalturaLog::info("DRY RUN Updated {$entry->id} MRP");
				}
				else
				{
					$result = $metadataPlugin->metadata->update($metadata->id, $newXmlString);
					if ($result)
					{
						kalturaLog::info("Updated {$entry->id} MRP");
					}
					else
					{
						kalturaLog::err("Failed to update {$entry->id} MRP");
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

function main($serviceUrl, $adminKs, $dryRunMode, $maxEntries)
{
	$client = getClient($serviceUrl, $adminKs);
	$entriesHandledCount = 0;
	$scheduledTaskProfiles = getMRProfiles($client);
	$profiles = filterProfiles($scheduledTaskProfiles);
	foreach($profiles as $profile)
	{
		/** @var KalturaScheduledTaskProfile $profile */
		kalturaLog::info("working on profile {$profile->name} id {$profile->id}");
		updateEntries($client, $profile, $dryRunMode, $maxEntries, $entriesHandledCount);
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
kalturaLog::info("Remove duplicate old MR script finished and updated {$entriesHandledCount} entries");