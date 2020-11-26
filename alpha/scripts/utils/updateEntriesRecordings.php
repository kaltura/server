<?php
require_once('/opt/kaltura/app/tests/lib/KalturaClient.php');
if ($argc < 5)
{
	die("Usage: php $argv[0] partnerId adminSecret serviceUrl simuliveVodFile optional:rootCategoryFullId\n");
}
$partnerId = $argv[1];
$adminSecret = $argv[2];
$url = $argv[3];
$simuliveVodFile = $argv[4] ;
$rootCategoryFullId = null;
if($argc == 6)
{
	$rootCategoryFullId = $argv[5];
}

$config = new KalturaConfiguration();
$config->serviceUrl = $url;
$client = new KalturaClient($config);
$ks = $client->session->start(
	$adminSecret,
	'',
	KalturaSessionType::ADMIN,
	$partnerId,
	86400,
	'disableentitlement');
$client->setKS($ks);

$simuliveVodEntryIds = file ($simuliveVodFile) or die ('Could not read file'."\n");

foreach($simuliveVodEntryIds as $simuliveEntryIdVodEntryId)
{
	$simuliveEntryIdVodEntryId = trim($simuliveEntryIdVodEntryId);
	list($simuliveEntryId, $vodEntryId) = explode(',', $simuliveEntryIdVodEntryId);
	try
	{
		$simuliveEntry = $client->baseEntry->get($simuliveEntryId);
	}
	catch(Exception $e)
	{
		print_r($e->getMessage() . ' entryId: ' . $simuliveEntryId ."\n");
		continue;
	}
	if ($simuliveEntry)
	{
		if ($simuliveEntry->recordedEntryId || $simuliveEntry->redirectEntryId)
		{
			print_r('recordedEntryId/redirectEntryId on entryId: '. $simuliveEntryId . ' already existed' ."\n");
			continue;
		}
		$kLive = new KalturaLiveStreamEntry();
		$kLive->recordedEntryId = $vodEntryId;
		$kLive->redirectEntryId = $vodEntryId;
		try
		{
			$client->baseEntry->update($simuliveEntryId, $kLive);
			print_r('EntryId: ' . $simuliveEntryId . ' was updated with recordedEntryId: '. $vodEntryId .
				', redirectEntryId: ' . $vodEntryId ."\n");
		}
		catch(Exception $e)
		{
			print_r($e->getMessage() . ' entryId: ' . $simuliveEntryId ."\n");
			continue;
		}

		$kBase = new KalturaBaseEntry();
		$kBase->displayInSearch = KalturaEntryDisplayInSearchType::SYSTEM;
		try
		{
			$client->baseEntry->update($vodEntryId, $kBase);
			print_r('EntryId: ' . $vodEntryId . ' was updated with displayInSearch: ' . KalturaEntryDisplayInSearchType::SYSTEM . "\n");
		}
		catch(Exception $e)
		{
			print_r($e->getMessage() . ' entryId: ' . $vodEntryId ."\n");
			continue;
		}
		addCategoryEntryIds($client, $simuliveEntryId, $vodEntryId, $rootCategoryFullId);
	}
}
print_r("Done! \n");


/*
 * adding the category entries of simuliveEntryId to vodEntryId
 */
function addCategoryEntryIds($client, $simuliveEntryId, $vodEntryId, $rootCategoryFullId)
{
	$categoryEntryFilter = new KalturaCategoryEntryFilter();
	$categoryEntryFilter->entryIdEqual = $simuliveEntryId;
	$categoryEntryFilter->statusEqual = KalturaCategoryEntryStatus::ACTIVE;
	if ($rootCategoryFullId)
	{
		$categoryEntryFilter->categoryFullIdsStartsWith = $rootCategoryFullId;
	}
	$pager = new KalturaFilterPager();
	$pager->pageIndex = 1;
	$pager->pageSize = 500;
	try
	{
		$categoryEntryResult = $client->categoryEntry->listAction($categoryEntryFilter, $pager);
		print_r('Found ' . count($categoryEntryResult->objects) . ' category entries on entryId: '. $simuliveEntryId . "\n");
	}
	catch(Exception $e)
	{
		print_r($e->getMessage() . ' entryId: ' . $simuliveEntryId ."\n");
		return;
	}
	foreach ($categoryEntryResult->objects as $categoryEntry)
	{
		$kcategoryEntry = new KalturaCategoryEntry();
		$kcategoryEntry->entryId = $vodEntryId;
		$kcategoryEntry->categoryId = $categoryEntry->categoryId;
		try
		{
			$client->categoryEntry->add($kcategoryEntry);
			print_r('New category entry was added, entryId: ' . $vodEntryId . ' categoryId: '. $categoryEntry->categoryId . "\n");
		}
		catch(Exception $e)
		{
			print_r($e->getMessage() . ' entryId: ' . $vodEntryId . ' categoryId: ' . $categoryEntry->categoryId . "\n");
		}
	}
}