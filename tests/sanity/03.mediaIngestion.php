<?php
$config = null;
$clientConfig = null;
/* @var $clientConfig KalturaConfiguration */
$client = null;
/* @var $client KalturaClient */

require_once __DIR__ . '/lib/init.php';
echo "Test started [" . __FILE__ . "]\n";


/**
 * Start a new session
 */
$partnerId = $config['session']['partnerId'];
$adminSecretForSigning = $config['session']['adminSecret'];
$client->setKs($client->generateSessionV2($adminSecretForSigning, 'sanity-user', KalturaSessionType::USER, $partnerId, 86400, ''));
echo "Session started\n";




/**
 * Creates a new entry
 */
$entry = new KalturaMediaEntry();
$entry->name = 'sanity-test';
$entry->description = 'sanity-test';
$entry->mediaType = KalturaMediaType::VIDEO;

$resource = new KalturaUrlResource();
$resource->url = $clientConfig->serviceUrl . 'content/templates/entry/data/kaltura_logo_animated_blue.flv';

$client->startMultiRequest();
$requestEntry = $client->media->add($entry);
/* @var $requestEntry KalturaMediaEntry */
$client->media->addContent($requestEntry->id, $resource);
$client->media->get($requestEntry->id);

$results = $client->doMultiRequest();
foreach($results as $index => $result)
{
	if ($client->isError($result))
	{
		echo "Executing failed for request #".($index+1)." with error [" . $result['message'] . "]\n";
		throw new KalturaException($result["message"], $result["code"]);
	}
}
echo "Entry ingested\n";





/**
 * Waits for the entry to be ready
 */
$createdEntry = end($results);
/* @var $createdEntry KalturaMediaEntry */
while ($createdEntry)
{
	if($createdEntry->status == KalturaEntryStatus::READY)
		break;

	if($createdEntry->status != KalturaEntryStatus::IMPORT && $createdEntry->status != KalturaEntryStatus::PRECONVERT)
	{
		echo "Entry status failed [$createdEntry->status]\n";
		exit(-1);
	}

	sleep(15);
	$createdEntry = $client->media->get($createdEntry->id);
}
if(!$createdEntry)
{
	echo "Entry not found\n";
	exit(-1);
}
echo "Entry ready\n";




/**
 * Gets the entry context
 */
$contextDataParams = new KalturaEntryContextDataParams();
$entryContextDataResult = $client->baseEntry->getContextData($createdEntry->id, $contextDataParams);
/* @var $entryContextDataResult KalturaEntryContextDataResult */
if(!$entryContextDataResult)
{
	echo "Unable to get entry [$createdEntry->id] context\n";
	exit(-1);
}
if(!$entryContextDataResult->isScheduledNow)
{
	echo "Entry [$createdEntry->id] not scheduled\n";
	exit(-1);
}
echo "Context data is valid\n";




/**
 * Gets the entry play manifest
 */
$manifestLocalPath = tempnam(sys_get_temp_dir(), 'download');
$manifestUrl = "{$clientConfig->serviceUrl}p/{$createdEntry->partnerId}/sp/{$createdEntry->partnerId}00/playManifest/entryId/{$createdEntry->id}/a/a.f4m";
$errCode = cUrl($manifestUrl, $manifestLocalPath, $headers, false);
if($errCode != 200)
{
	echo "Entry [$createdEntry->id] manifest failed\n";
	exit(-1);
}
if(!file_exists($manifestLocalPath) || !filesize($manifestLocalPath))
{
	echo "Entry [$createdEntry->id] no manifest file\n";
	exit(-1);
}
if(isset($headers['x-kaltura']) && strpos($headers['x-kaltura'], 'error-') === 0)
{
	echo "Entry [$createdEntry->id] manifest error: " . $headers['x-kaltura'] . "\n";
	exit(-1);
}
echo "OK\n";



/**
 * Validates the entry play manifest
 */
$xml = new DOMDocument();
$xml->loadXML(file_get_contents($manifestLocalPath));
$context = $xml->documentElement;
$xPath = new DOMXPath($xml);
$xPath->registerNamespace('f4m', $context->namespaceURI);
$elementsList = $xPath->query("//f4m:manifest/f4m:id[string() = '{$createdEntry->id}']");
if($elementsList->length != 1)
{
	echo "Entry [$createdEntry->id] manifest node not found\n";
	exit(-1);
}





/**
 * Gets the entry download URL
 */
$mediaLocalPath = tempnam(sys_get_temp_dir(), 'download');
$downloadUrl = "{$clientConfig->serviceUrl}p/{$createdEntry->partnerId}/sp/{$createdEntry->partnerId}00/download/entry_id/{$createdEntry->id}";
$errCode = cUrl($downloadUrl, $mediaLocalPath, $headers, false);
if($errCode != 302)
{
	echo "Entry [$createdEntry->id] download redirect failed\n";
	exit(-1);
}
if(isset($headers['x-kaltura']) && strpos($headers['x-kaltura'], 'error-') === 0)
{
	echo "Entry [$createdEntry->id] download error: " . $headers['x-kaltura'] . "\n";
	exit(-1);
}
echo "OK\n";

$errCode = cUrl($downloadUrl, $mediaLocalPath, $headers, true);
if($errCode != 200)
{
	echo "Entry [$createdEntry->id] download failed\n";
	exit(-1);
}
if(!file_exists($mediaLocalPath) || !filesize($mediaLocalPath))
{
	echo "Entry [$createdEntry->id] no download file\n";
	exit(-1);
}
echo "OK\n";




/**
 * Gets the entry raw URL
 */
$mediaLocalPath = tempnam(sys_get_temp_dir(), 'raw');
$errCode = cUrl($createdEntry->downloadUrl, $mediaLocalPath, $headers, false);
if($errCode != 302)
{
	echo "Entry [$createdEntry->id] raw redirect failed\n";
	exit(-1);
}
if(isset($headers['x-kaltura']) && strpos($headers['x-kaltura'], 'error-') === 0)
{
	echo "Entry [$createdEntry->id] raw error: " . $headers['x-kaltura'] . "\n";
	exit(-1);
}
echo "OK\n";

$errCode = cUrl($createdEntry->downloadUrl, $mediaLocalPath, $headers, true);
if($errCode != 200)
{
	echo "Entry [$createdEntry->id] raw failed\n";
	exit(-1);
}
if(!file_exists($mediaLocalPath) || !filesize($mediaLocalPath))
{
	echo "Entry [$createdEntry->id] no raw file\n";
	exit(-1);
}
echo "OK\n";




/**
 * Gets the entry thumbnail URL
 */
$mediaLocalPath = tempnam(sys_get_temp_dir(), 'thumb');
$errCode = cUrl($createdEntry->thumbnailUrl, $mediaLocalPath, $headers, false);
if($errCode != 200)
{
	echo "Entry [$createdEntry->id] thumbnail failed\n";
	exit(-1);
}
if(!file_exists($mediaLocalPath) || !filesize($mediaLocalPath))
{
	echo "Entry [$createdEntry->id] no thumbnail file\n";
	exit(-1);
}
if(isset($headers['x-kaltura']) && strpos($headers['x-kaltura'], 'error-') === 0)
{
	echo "Entry [$createdEntry->id] thumbnail error: " . $headers['x-kaltura'] . "\n";
	exit(-1);
}
echo "OK\n";



/**
 * All is SABABA
 */
echo "OK\n";
exit(0);
