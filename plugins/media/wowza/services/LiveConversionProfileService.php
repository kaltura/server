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
	 * Serve XML rendition of the Kaltura Live Transcoding Profile usable by the Wowza transcoding add-on
	 * 
	 * @action serve
	 * @param string $streamName the id of the live entry with it's stream suffix
	 * @param string $hostname the media server host name
	 * @return file
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws WowzaErrors::INVALID_STREAM_NAME
	 */
	public function serveAction($streamName, $hostname = null)
	{
		$matches = null;
		if(!preg_match('/^(\d_.{8})_(\d+)$/', $streamName, $matches))
			throw new KalturaAPIException(WowzaErrors::INVALID_STREAM_NAME, $streamName);
			
		$entryId = $matches[1];
		$suffix = $matches[2];
		
		$entry = null;
		if (!kCurrentContext::$ks)
		{
			kEntitlementUtils::initEntitlementEnforcement(null, false);
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
			
		if (!$entry || $entry->getType() != KalturaEntryType::LIVE_STREAM || !in_array($entry->getSource(), array(KalturaSourceType::LIVE_STREAM, KalturaSourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS)))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$mediaServer = null;
		if($hostname)
			$mediaServer = ServerNodePeer::retrieveActiveMediaServerNode($hostname);
			
		$conversionProfileId = $entry->getConversionProfileId();
		$liveParams = assetParamsPeer::retrieveByProfile($conversionProfileId);
		
		$liveParamsInput = null;
		$disableIngested = true;
		foreach($liveParams as $liveParamsItem)
		{
			/* @var $liveParamsItem liveParams */
			if($liveParamsItem->getStreamSuffix() == $suffix)
			{
				$liveParamsInput = $liveParamsItem;
				if(!$liveParamsInput->hasTag(assetParams::TAG_SOURCE))
				{
					$liveParams = array($liveParamsInput);
					$disableIngested = false;
				}
				break;
			}
		}
		
		$ignoreLiveParamsIds = array();
		if($disableIngested)
		{
			$conversionProfileAssetParams = flavorParamsConversionProfilePeer::retrieveByConversionProfile($conversionProfileId);
			foreach($conversionProfileAssetParams as $conversionProfileAssetParamsItem)
			{
				/* @var $conversionProfileAssetParamsItem flavorParamsConversionProfile */
				if($conversionProfileAssetParamsItem->getOrigin() == assetParamsOrigin::INGEST)
					$ignoreLiveParamsIds[] = $conversionProfileAssetParamsItem->getFlavorParamsId();
			}
		}
		
		// translate the $liveParams to XML according to doc: http://www.wowza.com/forums/content.php?304#configTemplate
		
		$root = new SimpleXMLElement('<Root/>');
		
		$transcode = $root->addChild('Transcode');
		
		$encodes = $transcode->addChild('Encodes');
		$groups = array();
		foreach($liveParams as $liveParamsItem)
		{
			/* @var $liveParamsItem liveParams */
			if(!$liveParamsItem->hasTag(assetParams::TAG_SOURCE) && in_array($liveParamsItem->getId(), $ignoreLiveParamsIds))
				continue;
				
			$this->appendLiveParams($entry, $mediaServer, $encodes, $liveParamsItem);
			$tags = $liveParamsItem->getTagsArray();
			$tags[] = 'all';
			foreach($tags as $tag)
			{
				if(!isset($groups[$tag]))
					$groups[$tag] = array();
					
				$systemName = $liveParamsItem->getSystemName() ? $liveParamsItem->getSystemName() : $liveParamsItem->getId();
				$groups[$tag][] = $systemName;
			}
		}
		
		$decode = $transcode->addChild('Decode');
		$video = $decode->addChild('Video');
		$video->addChild('Deinterlace', 'false');
		
		$streamNameGroups = $transcode->addChild('StreamNameGroups');
		
		foreach($groups as $groupName => $groupMembers)
		{
			$streamNameGroup = $streamNameGroups->addChild('StreamNameGroup');
			$streamNameGroup->addChild('Name', $groupName);
			$streamNameGroup->addChild('StreamName', '${SourceStreamName}_' . $groupName);
			$members = $streamNameGroup->addChild('Members');
			
			foreach($groupMembers as $groupMember)
			{
				$member = $members->addChild('Member');
				$member->addChild('EncodeName', $groupMember);
			}
		}
		
		$properties = $transcode->addChild('Properties');
		
		$dom = new DOMDocument("1.0");
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($root->asXML());
		
		return new kRendererString($dom->saveXML(), 'text/xml');
	}
	
	protected function appendLiveParams(LiveStreamEntry $entry, WowzaMediaServerNode $mediaServer = null, SimpleXMLElement $encodes, liveParams $liveParams)
	{
		$streamName = $entry->getId() . '_' . $liveParams->getId();
		$videoCodec = 'PassThru';
		$audioCodec = 'AAC';
		$profile = 'main';
		$systemName = $liveParams->getSystemName() ? $liveParams->getSystemName() : $liveParams->getId();
		
		$encode = $encodes->addChild('Encode');
		$encode->addChild('Enable', 'true');
		$encode->addChild('Name', $systemName);
		$encode->addChild('StreamName', $streamName);
		$video = $encode->addChild('Video');
		$audio = $encode->addChild('Audio');
		
		if ($liveParams->hasTag(assetParams::TAG_AUDIO_ONLY))
		{
			$videoCodec = 'Disable';
		}
		
		if($liveParams->hasTag(liveParams::TAG_INGEST))
		{
			$video->addChild('Codec', $videoCodec);
			$audio->addChild('Codec', $audioCodec);
			$audio->addChild('Bitrate', 96000);
			
			return;
		}
		
		if($liveParams->getWidth() || $liveParams->getHeight() || $liveParams->getFrameRate())
		{
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
		
			if($liveParams->getAudioSampleRate() || $liveParams->getAudioChannels())
			{
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
			}
		}
		
		$video->addChild('Transcoder', $mediaServer ? $mediaServer->getTranscoder() : WowzaMediaServerNode::DEFAULT_TRANSCODER);
		$video->addChild('GPUID', $mediaServer ? $mediaServer->getGPUID() : WowzaMediaServerNode::DEFAULT_GPUID);
		$frameSize = $video->addChild('FrameSize');
	
		if(!$liveParams->getWidth() && !$liveParams->getHeight())
		{
			$frameSize->addChild('FitMode', 'match-source');
		}
		elseif($liveParams->getWidth() && $liveParams->getHeight())
		{
			$frameSize->addChild('FitMode', 'fit-height');
			$frameSize->addChild('Width', $liveParams->getWidth());
			$frameSize->addChild('Height', $liveParams->getHeight());
		}
		elseif($liveParams->getWidth())
		{
			$frameSize->addChild('FitMode', 'fit-width');
			$frameSize->addChild('Width', $liveParams->getWidth());
		}
		elseif($liveParams->getHeight())
		{
			$frameSize->addChild('FitMode', 'fit-height');
			$frameSize->addChild('Height', $liveParams->getHeight());
		}
		
		$video->addChild('Codec', $videoCodec);
		$video->addChild('Profile', $profile);
		$video->addChild('Bitrate', $liveParams->getVideoBitrate() ? $liveParams->getVideoBitrate() * 1024 : 240000);
		$keyFrameInterval = $video->addChild('KeyFrameInterval');
		$keyFrameInterval->addChild('FollowSource', 'true');
		$keyFrameInterval->addChild('Interval', 60);
		
		$audio->addChild('Codec', $audioCodec);
		$audio->addChild('Bitrate', $liveParams->getAudioBitrate() ? $liveParams->getAudioBitrate() * 1024 : 96000);
	}
}
