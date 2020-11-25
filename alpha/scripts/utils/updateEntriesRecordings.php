<?php
require_once('/opt/kaltura/app/tests/lib/KalturaClient.php');
if ($argc < 5)
{
	die("Usage: php $argv[0] partnerId adminSecret serviceUrl simuliveVodFile \n");
}
$partnerId = $argv[1];
$adminSecret = $argv[2];
$url = $argv[3];
$simuliveVodFile = $argv[4] ;

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
		$kLive = new KalturaLiveStreamEntry();
		$kLive->recordedEntryId = $vodEntryId;
		try
		{
			$client->baseEntry->update($simuliveEntryId, $kLive);
			print_r('EntryId: ' . $simuliveEntryId . ' was updated with recordedEntryId: '. $vodEntryId . "\n");
		}
		catch(Exception $e)
		{
			print_r($e->getMessage() . ' entryId: ' . $simuliveEntryId ."\n");
			continue;
		}

		$kLive = new KalturaLiveStreamEntry();
		$kLive->displayInSearch = KalturaEntryDisplayInSearchType::SYSTEM;
		try
		{
			$client->baseEntry->update($vodEntryId, $kLive);
			print_r('EntryId: ' . $vodEntryId . ' was updated with displayInSearch: ' . KalturaEntryDisplayInSearchType::SYSTEM . "\n");
		}
		catch(Exception $e)
		{
			print_r($e->getMessage() . ' entryId: ' . $vodEntryId ."\n");
			continue;
		}
		addCategoryEntryIds($client, $simuliveEntryId, $vodEntryId);
	}
}
print_r("Done! \n");


/*
 * adding the category entries of simuliveEntryId to vodEntryId
 */
function addCategoryEntryIds($client, $simuliveEntryId, $vodEntryId)
{
	$categoryEntryFilter = new KalturaCategoryEntryFilter();
	$categoryEntryFilter->entryIdEqual = $simuliveEntryId;
	$categoryEntryFilter->statusEqual = KalturaCategoryEntryStatus::ACTIVE;

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