<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$filePath = realpath(dirname(__FILE__) . '/../../../') . '/deployment/base/scripts/init_data/04.flavorParams.ini';

$fileName = basename($filePath);
KalturaLog::info("Handling file [$fileName]");
$objectConfigurations = parse_ini_file($filePath, true);

foreach($objectConfigurations as $ini_item)
{
    $id = $ini_item['id'];

    $c = new Criteria();
    $c->addAnd(assetParamsPeer::ID, $id);
    $assetParams = assetParamsPeer::doSelect($c);

    foreach ($assetParams as $assetParam)
    {
	    /* @var $assetParam BaseassetParams */
	    foreach ($assetParams as $assetParam)
	    {
		    echo "Updating id [{$assetParam->getId()}] of partenr [{$assetParam->getPartnerId()}]";
		    if(isset($ini_item['conversionEnginesExtraParams']))
			    $assetParam->setConversionEnginesExtraParams($ini_item['conversionEnginesExtraParams']);
		    if(isset($ini_item['name']))
			    $assetParam->setName($ini_item['name']);
		    if(isset($ini_item['tags']))
			    $assetParam->setTags($ini_item['tags']);
		    if(isset($ini_item['systemName']))
			    $assetParam->setSystemName($ini_item['systemName']);
		    if(isset($ini_item['description']))
			    $assetParam->setDescription($ini_item['description']);
		    if(isset($ini_item['format']))
			    $assetParam->setFormat($ini_item['format']);
		    if(isset($ini_item['videoCodec']))
			    $assetParam->setVideoCodec($ini_item['videoCodec']);
		    if(isset($ini_item['videoBitrate']))
			    $assetParam->setVideoBitrate($ini_item['videoBitrate']);
		    if(isset($ini_item['audioCodec']))
			    $assetParam->setAudioCodec($ini_item['audioCodec']);
		    if(isset($ini_item['audioBitrate']))
			    $assetParam->setAudioBitrate($ini_item['audioBitrate']);
		    if(isset($ini_item['audioChannels']))
			    $assetParam->setAudioChannels($ini_item['audioChannels']);
		    if(isset($ini_item['audioSampleRate']))
			    $assetParam->setAudioSampleRate($ini_item['audioSampleRate']);
		    if(isset($ini_item['audioResolution']))
			    $assetParam->setAudioResolution($ini_item['audioResolution']);
		    if(isset($ini_item['width']))
			    $assetParam->setWidth($ini_item['width']);
		    if(isset($ini_item['height']))
			    $assetParam->setHeight($ini_item['height']);
		    if(isset($ini_item['frameRate']))
			    $assetParam->setFrameRate($ini_item['frameRate']);
		    if(isset($ini_item['gopSize']))
			    $assetParam->setGopSize($ini_item['gopSize']);
		    if(isset($ini_item['twoPass']))
			    $assetParam->setTwoPass($ini_item['twoPass']);
		    if(isset($ini_item['type']))
			    $assetParam->setType($ini_item['type']);
		    if(isset($ini_item['engineVersion']))
			    $assetParam->setEngineVersion($ini_item['engineVersion']);
		    if(isset($ini_item['conversionEngines']))
			    $assetParam->setConversionEngines($ini_item['conversionEngines']);
		    if(isset($ini_item['deinterlice']))
			    $assetParam->setDeinterlice($ini_item['deinterlice']);
		    if(isset($ini_item['creationMode']))
			    $assetParam->setCreationMode($ini_item['creationMode']);
		    if(isset($ini_item['isDefault']))
			    $assetParam->setIsDefault($ini_item['isDefault']);
		    if(isset($ini_item['operators']))
			    $assetParam->setOperators($ini_item['operators']);
		
		    $assetParam->save();
	    }
    }
}
