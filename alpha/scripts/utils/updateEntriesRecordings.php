<?php
require_once('/opt/kaltura/app/tests/lib/KalturaClient.php');
if ($argc < 6)
{
	die("Usage: php $argv[0] partnerId adminSecret serviceUrl simuliveEntryIdsFile vodEntryIdsFile \n");
}
$partnerId = $argv[1];
$adminSecret = $argv[2];
$url = $argv[3];
$simuliveEntryIdsFile = $argv[4] ;
$vodEntryIdsFile = $argv[5];

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

$simuliveEntryIds = file ($simuliveEntryIdsFile) or die ('Could not read file'."\n");
$vodEntryIds = file ($vodEntryIdsFile) or die ('Could not read file'."\n");

if (count($simuliveEntryIds) != count($vodEntryIds))
{
	print_r("simuliveEntryIdsFile and vodEntryIdsFile should be on the same length \n");
	exit;
}

for ($i = 0; $i < count($simuliveEntryIds); $i++)
{
	$simuliveEntryId = trim($simuliveEntryIds[$i]);
	$vodEntryId = trim($vodEntryIds[$i]);
	try
	{
		$simuliveEntry = $client->baseEntry->get($simuliveEntryId);
	}
	catch(Exception $e)
	{
		print_r($e->getMessage() . ' entryId: ' . $simuliveEntry ."\n");
		continue;
	}
	if ($simuliveEntry)
	{
		$kLive = new KalturaLiveStreamEntry();
		$kLive->recordedEntryId = $vodEntryId;
		$kLive->displayInSearch = KalturaEntryDisplayInSearchType::SYSTEM;
		try
		{
			$client->baseEntry->update($simuliveEntryId, $kLive);
			print_r('EntryId: ' . $simuliveEntryId . ' was updated with recordedEntryId: '. $vodEntryId .
				' displayInSearch: ' . KalturaEntryDisplayInSearchType::SYSTEM . "\n");
		}
		catch(Exception $e)
		{
			print_r($e->getMessage() . ' entryId: ' . $simuliveEntryId ."\n");
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