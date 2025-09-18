<?php

if($argc < 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php " . __FILE__ . " {xmlFilePath} {partnerId} <realrun / dryrun>" . PHP_EOL;
	exit;
}

require_once(__DIR__ . '/../bootstrap.php');
$xmlFilePath = $argv[1];
kCurrentContext::$partner_id = $argv[2];
$dryRun = ($argv[3] === "dryrun");

KalturaStatement::setDryRun($dryRun);

if (!file_exists($xmlFilePath) || !is_readable($xmlFilePath))
{
	echo "---- Error: xml file not found or not readable." . PHP_EOL;
	exit;
}

$xmlString = file_get_contents($xmlFilePath);
$xmlContent = simplexml_load_string($xmlString);
if ($xmlContent === false) {
	echo "Failed to load XML from string.\n";
	print_r(libxml_get_errors());
}

foreach ($xmlContent->DATA_RECORD as $item)
{
	echo "---- Creating Flavor Params [" . (string)$item->name . "]" . PHP_EOL;
	if (searchForFlavorParamsByNameAndPid((string)$item->name, kCurrentContext::$partner_id))
	{
		continue;
	}

	$flavorParams = new flavorParams();
	$flavorParams->setName((string)$item->name);
	$flavorParams->setSystemName((string)$item->systemName);
	$flavorParams->setDescription((string)$item->description);
	$flavorParams->setIsDefault((string)$item->isSystemDefault);
	$flavorParams->setTags((string)$item->tags);
	$flavorParams->setSourceRemoteStorageProfileId((string)$item->sourceRemoteStorageProfileId);
	$flavorParams->setMediaParserType((string)$item->mediaParserType);
	$flavorParams->setVideoBitrate((string)$item->videoBitrate);
	$flavorParams->setAudioCodec((string)$item->audioCodec);
	$flavorParams->setAudioBitrate((string)$item->audioBitrate);
    $flavorParams->setAudioChannels((string)$item->audioChannels);
    $flavorParams->setAudioSampleRate((string)$item->audioSampleRate);
    $flavorParams->setWidth((string)$item->width);
    $flavorParams->setHeight((string)$item->height);
    $flavorParams->setFrameRate((string)$item->frameRate);
    $flavorParams->setGopSize((string)$item->gopSize);
    $flavorParams->setConversionEngines((string)$item->conversionEngines);
    $flavorParams->setTwoPass((string)$item->twoPass);
    $flavorParams->setDeinterlice((string)$item->deinterlice);
    $flavorParams->setRotate((string)$item->rotate);
    $flavorParams->setEngineVersion((string)$item->engineVersion);
    $flavorParams->setFormat((string)$item->format);
    $flavorParams->setAspectRatioProcessingMode((string)$item->aspectRatioProcessingMode);
    $flavorParams->setForceFrameToMultiplication16((string)$item->forceFrameToMultiplication16);
    $flavorParams->setIsGopInSec((string)$item->isGopInSec);
    $flavorParams->setIsAvoidVideoShrinkFramesizeToSource((string)$item->isAvoidVideoShrinkFramesizeToSource);
    $flavorParams->setIsAvoidVideoShrinkBitrateToSource((string)$item->isAvoidVideoShrinkBitrateToSource);
    $flavorParams->setIsVideoFrameRateForLowBrAppleHls((string)$item->isVideoFrameRateForLowBrAppleHls);
    $flavorParams->setMultiStream((string)$item->multiStream);
    $flavorParams->setAnamorphicPixels((string)$item->anamorphicPixels);
    $flavorParams->setIsAvoidForcedKeyFrames((string)$item->isAvoidForcedKeyFrames);
    $flavorParams->setForcedKeyFramesMode((string)$item->forcedKeyFramesMode);
    $flavorParams->setIsCropIMX((string)$item->isCropIMX);
    $flavorParams->setOptimizationPolicy((string)$item->optimizationPolicy);
    $flavorParams->setMaxFrameRate((string)$item->maxFrameRate);
    $flavorParams->setVideoConstantBitrate((string)$item->videoConstantBitrate);
    $flavorParams->setVideoBitrateTolerance((string)$item->videoBitrateTolerance);
    $flavorParams->setIsEncrypted((string)$item->isEncrypted);
    $flavorParams->setContentAwareness((string)$item->contentAwareness);
    $flavorParams->setChunkedEncodeMode((string)$item->chunkedEncodeMode);

	$flavorParams->save();

	echo "---- Flavor Params [(string)$item->name] created" . PHP_EOL;

	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
}

echo "Done" . PHP_EOL;

function searchForFlavorParamsByNameAndPid($name, $partnerId)
{
	$criteria = new Criteria();
	$criteria->addAnd ( assetParamsPeer::PARTNER_ID , $partnerId , Criteria::EQUAL );
	$criteria->addAnd ( assetParamsPeer::NAME , $name , Criteria::EQUAL );

	return assetParamsPeer::doSelect( $criteria );
}
