<?php
$config = array();
$client = null;
$serviceUrl = null;
/* @var $client KalturaClient */
require_once __DIR__  . '/common.php';

$options = getopt('', array(
	'service-url:',
	'debug',
	'timeout:',
	'conversion-profile-id:',
	'conversion-profile-system-name:',
	'use-single-resource',
	'use-multi-request',
));

if(!isset($options['timeout']))
{
	echo "Argument timeout is required";
	exit(-1);
}
$timeout = $options['timeout'];

if(!isset($options['conversion-profile-id']) && !isset($options['conversion-profile-system-name']))
{
	echo "One of arguments conversion-profile-id or conversion-profile-system-name is required";
	exit(-1);
}

$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
$apiCall = null;
try
{
	$conversionProfileId = null;
	/* @var $entry KalturaMediaEntry */
	if(isset($options['conversion-profile-id']))
	{
		$conversionProfileId = $options['conversion-profile-id'];
	}
	elseif(isset($options['conversion-profile-system-name']))
	{
		$apiCall = 'session.start';
		$ks = $client->session->start($config['monitor-partner']['adminSecret'], 'monitor-user', KalturaSessionType::ADMIN, $config['monitor-partner']['id']);
		$client->setKs($ks);
			
		$conversionProfileFilter = new KalturaConversionProfileFilter();
		$conversionProfileFilter->systemNameEqual = $options['conversion-profile-system-name'];
		
		$apiCall = 'conversionProfile.list';
		$conversionProfileList = $client->conversionProfile->listAction($conversionProfileFilter);
		/* @var $conversionProfileList KalturaConversionProfileListResponse */
		if(!count($conversionProfileList->objects))
			throw new Exception("conversion profile with system name [" . $options['conversion-profile-system-name'] . "] not found");
			
		$conversionProfile = reset($conversionProfileList->objects);
		/* @var $conversionProfile KalturaConversionProfile */
		$conversionProfileId = $conversionProfile->id;
	}

	$apiCall = 'session.start';
	$ks = $client->session->start($config['monitor-partner']['secret'], 'monitor-user', KalturaSessionType::USER, $config['monitor-partner']['id']);
	$client->setKs($ks);
	
	$flavors = array(
		0 => __DIR__ . '/media/source.mp4',
		1 => __DIR__ . '/media/flavor1.3gp',
		2 => __DIR__ . '/media/flavor2.mp4',
		3 => __DIR__ . '/media/flavor3.mp4',
	);
	
	if(isset($options['use-multi-request']))
		$client->startMultiRequest();
		
	 // Creates a new entry
	$entry = new KalturaMediaEntry();
	$entry->name = 'monitor-test';
	$entry->description = 'monitor-test';
	$entry->mediaType = KalturaMediaType::VIDEO;
	
	$apiCall = 'media.add';
	$createdEntry = $client->media->add($entry);
	/* @var $createdEntry KalturaMediaEntry */
	
	$resources = array();
	foreach($flavors as $assetParamsId => $filePath)
	{
		$uploadToken = new KalturaUploadToken();
		$uploadToken->fileName = basename($filePath);
		$uploadToken->fileSize = filesize($filePath);
		
		$createdToken = $client->uploadToken->add($uploadToken);
		/* @var $createdToken KalturaUploadToken */
		$uploadedToken = $client->uploadToken->upload($createdToken->id, $filePath);
		/* @var $uploadedToken KalturaUploadToken */
		
		$contentResource = new KalturaUploadedFileTokenResource();
		$contentResource->token = $uploadedToken->id;
		
		$resources[$assetParamsId] = $contentResource;
	}
	
	if(isset($options['use-single-resource']))
	{
		$resource = new KalturaAssetsParamsResourceContainers();
		$resource->resources = array();
		
		foreach($resources as $assetParamsId => $contentResource)
		{
			$flavorResource = new KalturaAssetParamsResourceContainer();
			$flavorResource->assetParamsId = $assetParamsId;
			$flavorResource->resource = $contentResource;
			
			$resource->resources[] = $flavorResource;
		}
		$client->media->addContent($createdEntry->id, $resource);
	}
	else
	{
		foreach($resources as $flavorParamsId => $contentResource)
		{
			$flavorAsset = new KalturaFlavorAsset();
			$flavorAsset->flavorParamsId = $flavorParamsId;
			$createdAsset = $client->flavorAsset->add($createdEntry->id, $flavorAsset);
			/* @var $createdAsset KalturaFlavorAsset */
			
			$client->flavorAsset->setContent($createdAsset->id, $contentResource);
		}
	}
	// Waits for the entry to start conversion
	$apiCall = 'media.get';
	$createdEntry = $client->media->get($createdEntry->id);
	
	if(isset($options['use-multi-request']))
	{
		$apiCall = 'multirequest';
		$results = $client->doMultiRequest();
		foreach($results as $index => $result)
		{
			if ($client->isError($result))
				throw new KalturaException($result["message"], $result["code"]);
		}
		
		$createdEntry = end($results);
	}
	
	$timeoutTime = time() + $timeout;
	/* @var $createdEntry KalturaMediaEntry */
	while ($createdEntry)
	{
		if(time() > $timeoutTime)
			throw new Exception("timed out, entry id: $createdEntry->id");
			
		if($createdEntry->status == KalturaEntryStatus::PRECONVERT)
		{
			sleep(1);
			$apiCall = 'media.get';
			$createdEntry = $client->media->get($createdEntry->id);
			continue;
		}
		
		$monitorResult->executionTime = microtime(true) - $start;
		$monitorResult->value = $monitorResult->executionTime;
		
		if($createdEntry->status == KalturaEntryStatus::READY)
		{
			$monitorResult->description = "ingestion time: $monitorResult->executionTime seconds";
		}
		elseif($createdEntry->status == KalturaEntryStatus::ERROR_IMPORTING)
		{
			$error = new KalturaMonitorError();
			$error->description = "import failed, entry id: $createdEntry->id";
			$error->level = KalturaMonitorError::CRIT;
			
			$monitorResult->errors[] = $error;
			$monitorResult->description = "import failed, entry id: $createdEntry->id";
		}
		else
		{
			$error = new KalturaMonitorError();
			$error->description = "unexpected entry status: $createdEntry->status, entry id: $createdEntry->id";
			$error->level = KalturaMonitorError::CRIT;
			
			$monitorResult->errors[] = $error;
			$monitorResult->description = "unexpected entry status: $createdEntry->status, entry id: $createdEntry->id";
		}
		
		break;
	}

	try
	{
		$apiCall = 'media.delete';
		$createdEntry = $client->media->delete($createdEntry->id);
	}
	catch(Exception $ex)
	{
		$error = new KalturaMonitorError();
		$error->code = $ex->getCode();
		$error->description = $ex->getMessage();
		$error->level = KalturaMonitorError::WARN;
		
		$monitorResult->errors[] = $error;
	}
}
catch(KalturaException $e)
{
	$monitorResult->executionTime = microtime(true) - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $e->getCode();
	$error->description = $e->getMessage();
	$error->level = KalturaMonitorError::ERR;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = "Exception: " . get_class($e) . ", API: $apiCall, Code: " . $e->getCode() . ", Message: " . $e->getMessage();
}
catch(KalturaClientException $ce)
{
	$monitorResult->executionTime = microtime(true) - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $ce->getCode();
	$error->description = $ce->getMessage();
	$error->level = KalturaMonitorError::CRIT;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = "Exception: " . get_class($ce) . ", API: $apiCall, Code: " . $ce->getCode() . ", Message: " . $ce->getMessage();
}
catch(Exception $ex)
{
	$monitorResult->executionTime = microtime(true) - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $ex->getCode();
	$error->description = $ex->getMessage();
	$error->level = KalturaMonitorError::ERR;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = $ex->getMessage();
}

echo "$monitorResult";
exit(0);
