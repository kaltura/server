<?php

$dryRun = true; //TODO: change for real run
$stopFile = dirname(__FILE__).'/stop_live_migration'; // creating this file will stop the script
$entryLimitEachLoop = 500;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
$c = new Criteria();
$c->add(assetPeer::TYPE, 0);
$c->setLimit($entryLimitEachLoop);

$assets = assetPeer::doSelect($c, $con);
while(count($assets))
{
	foreach($assets as $asset)
	{
		switch($asset->getFormat())
		{
			case assetParams::CONTAINER_FORMAT_PDF:
				$asset->setType(DocumentAssetType::get()->coreValue(DocumentAssetType::PDF));
				break;
				
			case assetParams::CONTAINER_FORMAT_SWF:
				$asset->setType(DocumentAssetType::get()->coreValue(DocumentAssetType::SWF));
				break;
				
			case thumbParams::CONTAINER_FORMAT_JPG:
				$asset->setType(assetType::THUMBNAIL);
				break;
				
			default:
				$asset->setType(assetType::FLAVOR);
				
				$asset->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_BITRATE, $asset->getBitrate());
				$asset->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_FRAME_RATE, $asset->getFrameRate());
				$asset->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_VIDEO_CODEC_ID, $asset->getVideoCodecId());
		}
		$asset->save();
	}
	assetPeer::clearInstancePool();
	$assets = assetPeer::doSelect($c, $con);
}

$c = new Criteria();
$c->add(assetParamsPeer::TYPE, 0);
$c->setLimit($entryLimitEachLoop);

$assetParams = assetParamsPeer::doSelect($c, $con);
while(count($assetParams))
{
	foreach($assetParams as $assetParam)
	{
		switch($assetParam->getFormat())
		{
			case assetParams::CONTAINER_FORMAT_PDF:
				$assetParam->setType(DocumentAssetType::get()->coreValue(DocumentAssetType::PDF));
				break;
				
			case assetParams::CONTAINER_FORMAT_SWF:
				$assetParam->setType(DocumentAssetType::get()->coreValue(DocumentAssetType::SWF));
				break;
				
			case thumbParams::CONTAINER_FORMAT_JPG:
				$assetParam->setType(assetType::THUMBNAIL);
				break;
				
			default:
				$assetParam->setType(assetType::FLAVOR);
				
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_CODEC, $assetParam->getVideoCodec());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_BITRATE, $assetParam->getVideoBitrate());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CODEC, $assetParam->getAudioCodec());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_BITRATE, $assetParam->getAudioBitrate());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CHANNELS, $assetParam->getAudioChannels());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_SAMPLE_RATE, $assetParam->getAudioSampleRate());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_RESOLUTION, $assetParam->getAudioResolution());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_FRAME_RATE, $assetParam->getFrameRate());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_GOP_SIZE, $assetParam->getGopSize());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_TWO_PASS, $assetParam->getTwoPass());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_DEINTERLICE, $assetParam->getDeinterlice());
				$assetParam->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_ROTATE, $assetParam->getRotate());
		}
		$assetParam->save();
	}
	assetParamsPeer::clearInstancePool();
	$assetParams = assetParamsPeer::doSelect($c, $con);
}


$c = new Criteria();
$c->add(assetParamsOutputPeer::TYPE, 0);
$c->setLimit($entryLimitEachLoop);

$assetParamsOutputs = assetParamsOutputPeer::doSelect($c, $con);
while(count($assetParamsOutputs))
{
	foreach($assetParamsOutputs as $assetParamsOutput)
	{
		switch($assetParamsOutput->getFormat())
		{
			case assetParams::CONTAINER_FORMAT_PDF:
				$assetParamsOutput->setType(DocumentAssetType::get()->coreValue(DocumentAssetType::PDF));
				break;
				
			case assetParams::CONTAINER_FORMAT_SWF:
				$assetParamsOutput->setType(DocumentAssetType::get()->coreValue(DocumentAssetType::SWF));
				break;
				
			case thumbParams::CONTAINER_FORMAT_JPG:
				$assetParamsOutput->setType(assetType::THUMBNAIL);
				break;
				
			default:
				$assetParamsOutput->setType(assetType::FLAVOR);
				
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_CODEC, $assetParamsOutput->getVideoCodec());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_BITRATE, $assetParamsOutput->getVideoBitrate());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CODEC, $assetParamsOutput->getAudioCodec());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_BITRATE, $assetParamsOutput->getAudioBitrate());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CHANNELS, $assetParamsOutput->getAudioChannels());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_SAMPLE_RATE, $assetParamsOutput->getAudioSampleRate());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_RESOLUTION, $assetParamsOutput->getAudioResolution());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_FRAME_RATE, $assetParamsOutput->getFrameRate());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_GOP_SIZE, $assetParamsOutput->getGopSize());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_TWO_PASS, $assetParamsOutput->getTwoPass());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_DEINTERLICE, $assetParamsOutput->getDeinterlice());
				$assetParamsOutput->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_ROTATE, $assetParamsOutput->getRotate());
		}
		$assetParamsOutput->save();
	}
	assetParamsOutputPeer::clearInstancePool();
	$assetParamsOutputs = assetParamsOutputPeer::doSelect($c, $con);
}

KalturaLog::log('Done');


