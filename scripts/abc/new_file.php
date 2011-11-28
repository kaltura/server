<?php

if ((count($argv) != 2)/* && (count($argv) != 3)*/)
	throw new Exception("Invalid argument. Usage: php new_file.php <full path>");

$conversionProfileName = null;
if (count($argv) == 3)
	$conversionProfileName = $argv[2];

$ini = parse_ini_file("abc.ini");
require_once ($ini["CLIENT_LIBS"]."/php5/KalturaClient.php");

$incomingFilename = $argv[1];
$path_parts = pathinfo($incomingFilename);

$config = new KalturaConfiguration($ini["ABC_PARTNER_ID"]);
$config->serviceUrl = $ini["SERVICE_URL"];
$client = new KalturaClient($config);
$ks = $client->session->start($ini["ABC_ADMIN_SECRET"], "", KalturaSessionType :: ADMIN);
$client->setKs($ks);

// Check whether entry already exists
$filter = new KalturaMediaEntryFilter();
$filter->referenceIdEqual = $path_parts['basename'];
$filter->statusIn = implode(',', array(
	KalturaEntryStatus::ERROR_CONVERTING,
	KalturaEntryStatus::ERROR_IMPORTING,
	KalturaEntryStatus::IMPORT,
	KalturaEntryStatus::NO_CONTENT,
	KalturaEntryStatus::PENDING,
	KalturaEntryStatus::PRECONVERT,
	KalturaEntryStatus::READY,
));
$listResult = $client->media->listAction($filter);
if ($listResult->totalCount != 0)
	die("Entry already exists. Exiting.\r\n"); // nothing to do - entry already exists

// Get the conversion profile that should be used
if ($conversionProfileName == null) {
	list ($show, $contentType, $sourceType, $hasBug) = explode("_", $path_parts['basename']);
//$conversionProfileName = $show.'_'.$contentType.'_'.$sourceType.'_'.$hasBug;
	$conversionProfileName = $path_parts['basename'];
}
echo "cp sysname: $conversionProfileName\r\n";
$filter = new KalturaConversionProfileFilter;
$filter->systemNameEqual = $conversionProfileName;
$cps = $client->conversionProfile->listAction($filter);
if ($cps->totalCount == 1)
	$cpid = $cps->objects[0]->id;
else {
	throw new Exception("cannot find cpid sysname=$conversionProfileName");
	$cpid = null; // use partner default conversion profile
}

// Create a no-content entry
$entry = new KalturaMediaEntry;
$entry->name = $entry->referenceId = $path_parts['basename'];
$filter->statusIn = implode(',', array(
	KalturaEntryStatus::ERROR_CONVERTING,
	KalturaEntryStatus::ERROR_IMPORTING,
	KalturaEntryStatus::IMPORT,
	KalturaEntryStatus::NO_CONTENT,
	KalturaEntryStatus::PENDING,
	KalturaEntryStatus::PRECONVERT,
	KalturaEntryStatus::READY,
));
$entry->mediaType = KalturaMediaType::VIDEO;
if ($cpid)
	$entry->conversionProfileId = $cpid;
$newEntry = $client->media->add($entry);
echo "media->add called returning entry id $newEntry->id\r\n";

// Add custom metadata to the entry
$meta = "<metadata><ContentType>$contentType</ContentType><SourceType>$sourceType</SourceType><Date>".time()."</Date></metadata>";
$mdprof = $ini["ABC_METADATA_PROFILE_ID"];
$client->metadata->add($mdprof, KalturaMetadataObjectType :: ENTRY, $newEntry->id, $meta);
echo "Setting metadata profile $mdprof entryid $newEntry->id XMl $meta\r\n";

