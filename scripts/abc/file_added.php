<?php

if ($argc < 2)
	throw new Exception("Invalid argument. Usage: php new_file.php <full path>");

$conversionProfileName = null;
if ($argc == 3)
	$conversionProfileName = $argv[2];

$ini = parse_ini_file("abc.ini");
require_once ($ini["CLIENT_LIBS"]."/php5/KalturaClient.php");

$incomingFilename = $argv[1];
$path_parts = pathinfo($incomingFilename);

list ($show, $contentType, $sourceType, $hasBug) = explode("_", $path_parts['basename'], 6);

if ( $contentType == 'segment' )
{
    $contentType = 'Short Form';
}
elseif ( $contentType == 'full' )
{
    $contentType = 'Long Form';
}

$config = new KalturaConfiguration($ini["ABC_PARTNER_ID"]);
$config->serviceUrl = $ini["SERVICE_URL"];
$client = new KalturaClient($config);
$partnerId = $ini["ABC_PARTNER_ID"];
$ks = $client->session->start($ini["ABC_ADMIN_SECRET"], "", KalturaSessionType::ADMIN, $partnerId);
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
{
    echo "Entry already exists. Replacing ". $listResult[0]->id;
	$existingEntry = $listResult[0];
}

// Get the conversion profile that should be used
if ($conversionProfileName == null) {
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

$entry->mediaType = KalturaMediaType::VIDEO;
if ($cpid)
	$entry->conversionProfileId = $cpid;
	
// Create custom metadata for the entry
$mdprof = $ini["ABC_METADATA_PROFILE_ID"];
if (!$existingEntry)
{
	$adEntry = $client->media->add($entry);
    $meta = "<metadata><Title>$show</Title><FormatType>$contentType</FormatType><SourceType>$sourceType</SourceType><SourceVideoFile>".$path_parts['basename']."</SourceVideoFile></metadata>";
    $client->metadata->add($mdprof, KalturaMetadataObjectType::ENTRY, $adEntry->id, $meta); 
    echo "Setting metadata profile $mdprof entryid $adEntry->id XMl $meta\r\n";
}
else 
{
    $adEntry =  $client->media->update($existingEntry->id, $entry);
}


