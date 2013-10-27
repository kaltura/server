<?php

/**
 * Enable serving live conversion profile to the Wowza servers as XML
 * @service liveConversionProfile
 * @package plugins.wowza
 * @subpackage api.services
 */
class LiveConversionProfileService extends KalturaBaseService
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::initService()
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$this->applyPartnerFilterForClass('conversionProfile2');
		$this->applyPartnerFilterForClass('assetParams');
	}
	
	/**
	 * Allows you to add a new KalturaDropFolderFile object
	 * 
	 * @action serve
	 * @param string $entryId the id of the live entry to be converted
	 * @param string $hostname the media server host name
	 * @return file
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function serveAction($entryId, $hostname = null)
	{
		$entry = null;
		if (!kCurrentContext::$ks)
		{
			$entry = kCurrentContext::initPartnerByEntryId($entryId);
			
			if (!$entry || $entry->getStatus() == entryStatus::DELETED)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
				
			// enforce entitlement
			$this->setPartnerFilters(kCurrentContext::getCurrentPartnerId());
		}
		else 
		{	
			$entry = entryPeer::retrieveByPK($entryId);
		}
			
		if (!$entry || $entry->getType() != KalturaEntryType::LIVE_STREAM || $entry->getSource() != KalturaSourceType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$mediaServer = null;
		if($hostname)
			$mediaServer = MediaServerPeer::retrieveByHostname($hostname);
			
		$conversionProfileId = $entry->getConversionProfileId();
		$liveParams = assetParamsPeer::retrieveByProfile($conversionProfileId);
		
		// translate the $liveParams to XML according to doc: http://www.wowza.com/forums/content.php?304#configTemplate
		
		$root = new SimpleXMLElement('Root');
		
		$transcode = $root->addChild('Transcode');
		
		$encodes = $transcode->addChild('Encodes');
		foreach($liveParams as $liveParamsItem)
			$this->appendLiveParams($entry, $mediaServer, $encodes, $liveParamsItem);
		
		$decode = $transcode->addChild('Decode');
		$video = $decode->addChild('Video');
		$video->addChild('Deinterlace', 'false');
//		$video->addChild('Overlays');
//		$properties = $decode->addChild('Properties');
		
		$streamNameGroups = $transcode->addChild('StreamNameGroups');
		
		$properties = $transcode->addChild('Properties');
		$property = $properties->addChild('Property');
		$property->addChild('Name', 'sourceStreamFrameRate');
		$property->addChild('Value', 30);
		$property->addChild('Type', 'Double');
		
		return new kRendererString($root->asXML(), 'text/xml');
	}
	
	protected function appendLiveParams(LiveStreamEntry $entry, MediaServer $mediaServer = null, SimpleXMLElement $encodes, flavorParams $liveParams)
	{
		$streamName = $entry->getId() . '_' . $liveParams->getId();
		$videoCodec = 'PassThru';
		$audioCodec = 'PassThru';
		$profile = 'main';
		
		switch ($liveParams->getVideoCodec())
		{
			case flavorParams::VIDEO_CODEC_COPY:
				$videoCodec = 'PassThru';
				break;
				
			case flavorParams::VIDEO_CODEC_FLV:
			case flavorParams::VIDEO_CODEC_VP6:
			case flavorParams::VIDEO_CODEC_H263:
				$profile = 'baseline';
				$videoCodec = 'H.263';
				break;
				
			case flavorParams::VIDEO_CODEC_H264:
			case flavorParams::VIDEO_CODEC_H264B:
				$profile = 'baseline';
				// don't break
				
			case flavorParams::VIDEO_CODEC_H264H:
			case flavorParams::VIDEO_CODEC_H264M:
				$streamName = "mp4:$streamName";
				$videoCodec = 'H.264';
				break;
				
			default:
				KalturaLog::err("Live params video codec id [" . $liveParams->getVideoCodec() . "] is not expected");
				break;
		}
		
		if(!$liveParams->getWidth() && !$liveParams->getHeight() && !$liveParams->getFrameRate())
			$videoCodec = 'Disable';
		
		switch ($liveParams->getAudioCodec())
		{
			case flavorParams::AUDIO_CODEC_AAC:
			case flavorParams::AUDIO_CODEC_AACHE:
				$audioCodec = 'AAC';
				break;
			
			default:
				KalturaLog::err("Live params audio codec id [" . $liveParams->getAudioCodec() . "] is not expected");
				break;
		}
		
		if(!$liveParams->getAudioSampleRate() && !$liveParams->getAudioChannels())
			$audioCodec = 'Disable';
		
		$encode = $encodes->addChild('Encode');
		$encode->addChild('Enable', 'true');
		$encode->addChild('Name', $liveParams->getSystemName());
		$encode->addChild('StreamName', $streamName);
		$video = $encode->addChild('Video');
		$video->addChild('Codec', $videoCodec);
		$video->addChild('Transcoder', $mediaServer ? $mediaServer->getTranscoder() : MediaServer::DEFAULT_TRANSCODER);
		$video->addChild('GPUID', $mediaServer ? $mediaServer->getGPUID() : MediaServer::DEFAULT_GPUID);
		$frameSize = $video->addChild('FrameSize');
	
		if($liveParams->getWidth() && $liveParams->getHeight())
		{
			$frameSize->addChild('FitMode', 'fit-height');
			$frameSize->addChild('Width', $liveParams->getWidth());
			$frameSize->addChild('Height', $liveParams->getHeight());
		}
		elseif($liveParams->getWidth())
		{
			$frameSize->addChild('FitMode', 'fit-width');
			$frameSize->addChild('Width', $liveParams->getWidth());
			$frameSize->addChild('Height', 0);
		}
		elseif($liveParams->getHeight())
		{
			$frameSize->addChild('FitMode', 'fit-height');
			$frameSize->addChild('Width', 0);
			$frameSize->addChild('Height', $liveParams->getHeight());
		}
		else
		{
			$frameSize->addChild('FitMode', 'match-source');
			$frameSize->addChild('Width', 0);
			$frameSize->addChild('Height', 0);
		}
		
		$video->addChild('Profile', $profile);
		$video->addChild('Bitrate', $liveParams->getVideoBitrate() * 1024);
		$keyFrameInterval = $video->addChild('KeyFrameInterval');
		$keyFrameInterval->addChild('FollowSource', 'true');
		$keyFrameInterval->addChild('Interval', 60);
		
//		$video->addChild('Overlays');
		
//		$parameters = $video->addChild('Parameters');
		
		$audio = $encode->addChild('Audio');
		$audio->addChild('Codec', $audioCodec);
		$audio->addChild('Bitrate', $liveParams->getAudioBitrate() * 1024);
		
//		$parameters = $audio->addChild('Parameters');
	}
}
