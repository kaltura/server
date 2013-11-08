<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class LiveEntry extends entry
{
	const DEFAULT_CACHE_EXPIRY = 70;
	
	/* (non-PHPdoc)
	 * @see entry::getLocalThumbFilePath()
	 */
	public function getLocalThumbFilePath($version , $width , $height , $type , $bgcolor ="ffffff" , $crop_provider=null, $quality = 0,
		$src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $vid_sec = -1, $vid_slice = 0, $vid_slices = -1, $density = 0, $stripProfiles = false, $flavorId = null, $fileName = null)
	{
		if ($this->getStatus () == entryStatus::DELETED || $this->getModerationStatus () == moderation::MODERATION_STATUS_BLOCK) {
			KalturaLog::log ( "rejected live stream entry - not serving thumbnail" );
			KExternalErrors::dieError ( KExternalErrors::ENTRY_DELETED_MODERATED );
		}
		
		$contentPath = myContentStorage::getFSContentRootPath ();
		$msgPath = $contentPath . "content/templates/entry/thumbnail/live_thumb.jpg";
		return myEntryUtils::resizeEntryImage ( $this, $version, $width, $height, $type, $bgcolor, $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $msgPath, $density, $stripProfiles );
	}
	
	public function setOfflineMessage ( $v )	{	$this->putInCustomData ( "offlineMessage" , $v );	}
	public function getOfflineMessage (  )		{	return $this->getFromCustomData( "offlineMessage" );	}

	public function setStreamBitrates(array $v)
	{
		$this->putInCustomData("streamBitrates", $v);
	}
	
	public function getStreamBitrates()
	{
		$streamBitrates = $this->getFromCustomData("streamBitrates");
		if(is_array($streamBitrates) && count($streamBitrates))
			return $streamBitrates;
				
		if($this->getSource() == EntrySourceType::LIVE_STREAM)
		{
			$liveParams = assetParamsPeer::retrieveByProfile($this->getConversionProfileId());
			$streamBitrates = array();
			foreach($liveParams as $liveParamsItem)
			{
				/* @var $liveParamsItem liveParams */
				
				$streamBitrate = array(
					'bitrate' => $liveParamsItem->getVideoBitrate(),
					'width' => $liveParamsItem->getWidth(),
					'height' => $liveParamsItem->getHeight(),
					'tags' => $liveParamsItem->getTags(),
				);
				$streamBitrates[] = $streamBitrate;
			}
			return $streamBitrates;
		}
		
		return array(
			array(
				'bitrate' => 300,
				'width' => 320,
				'height' => 240,
			)
		);
	}
	
	public function getRecordedEntryId ()
	{
	    return $this->getFromCustomData("recorded_entry_id");
	}
	
	public function setRecordedEntryId ($v)
	{
	    $this->putInCustomData("recorded_entry_id", $v);
	}
	
	public function getRecordStatus ()
	{
	    return $this->getFromCustomData("record_status");
	}
	
	public function setRecordStatus ($v)
	{
	    $this->putInCustomData("record_status", $v);
	}

	public function getDvrStatus ()
	{
	    return $this->getFromCustomData("dvr_status");
	}
	
	public function setDvrStatus ($v)
	{
	    $this->putInCustomData("dvr_status", $v);
	}
	
    public function getDvrWindow ()
	{
	    return $this->getFromCustomData("dvr_window");
	}
	
	public function setDvrWindow ($v)
	{
	    $this->putInCustomData("dvr_window", $v);
	}
	
	public function setLiveStreamConfigurations (array $v)
	{
		$this->putInCustomData('live_stream_configurations', $v);
	}
	
	public function getLiveStreamConfigurationByProtocol($format, $protocol, $tag = null)
	{
		$configurations = $this->getLiveStreamConfigurations($protocol, $tag);
		foreach($configurations as $configuration)
		{
			/* @var $configuration kLiveStreamConfiguration */
			if ($configuration->getProtocol() == $format)
				return $configuration;
		}

		return null;
	}
	
	public function getLiveStreamConfigurations($protocol = 'http', $tag = null)
	{
		$configurations = $this->getFromCustomData('live_stream_configurations');
		if($configurations)
			return $configurations;
			
		$configurations = array();
		$mediaServer = $this->getMediaServer();
		if($mediaServer)
		{
			$streamName = $this->getStreamName();
			if(is_null($tag) && $this->getConversionProfileId())
				$tag = 'all';
			
			$manifestUrl = $mediaServer->getManifestUrl($protocol) . ($tag ? "ngrp:{$streamName}_{$tag}" : $streamName);
			
			$hlsStreamUrl	= "$manifestUrl/playlist.m3u8";
			$hdsStreamUrl	= "$manifestUrl/manifest.f4m";
			$mpdStreamUrl	= "$manifestUrl/manifest.mpd";
			$slStreamUrl	= "$manifestUrl/Manifest";
			
			if($this->getDvrStatus() == DVRStatus::ENABLED)
			{
				$hlsStreamUrl	.= "?DVR";
				$hdsStreamUrl	.= "?DVR";
				$slStreamUrl	.= "?dvr";
			}
				
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::RTMP);
			$configuration->setUrl($mediaServer->getRtmpUrl());
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::HDS);
			$configuration->setUrl($hdsStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::HLS);
			$configuration->setUrl($hlsStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::APPLE_HTTP);
			$configuration->setUrl($hlsStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::SILVER_LIGHT);
			$configuration->setUrl($slStreamUrl);
			$configurations[] = $configuration;
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::MPEG_DASH);
			$configuration->setUrl($mpdStreamUrl);
			$configurations[] = $configuration;
		}
			
		return $configurations;
	}

	/**
	 * @return MediaServer
	 */
	public function getMediaServer($currentDcOnly = false)
	{
		$mediaServers = $this->getMediaServers();
		if(!count($mediaServers))
			return null;
			
		foreach($mediaServers as $mediaServer)
		{
			/* @var $mediaServer kLiveMediaServer */
			if($mediaServer->getDc() == kDataCenterMgr::getCurrentDcId())
				return $mediaServer->getMediaServer();
		}
		if($currentDcOnly)
			return null;
		
		$mediaServer = reset($mediaServers);
		return $mediaServer->getMediaServer();
	}

	private function isCacheValid(kLiveMediaServer $mediaServer)
	{
		$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LIVE_MEDIA_SERVER);
		if(!$cacheStore)
		{
			$lastUpdate = time() - $mediaServer->getTime();
			$expiry = kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY);
			
			return $lastUpdate <= $expiry;
		}
	
		$key = $this->getId() . '_' . $mediaServer->getMediaServerId() . '_' . $mediaServer->getIndex();
		return $cacheStore->get($key);
	}
	
	/**
	 *
	 * Store given value in cache for with the given key as an identifier
	 * @param string $key
	 */
	private function storeInCache($key)
	{
		$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LIVE_MEDIA_SERVER);
		if(!$cacheStore)
			return false;
			
		return $cacheStore->set($key, true, kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY));
	}
	
	public function setMediaServer($index, $serverId, $hostname)
	{
		$key = $this->getId() . "_{$serverId}_{$index}";
		if($this->storeInCache($key) && $this->isMediaServerRegistered($index, $serverId))
			return;
			
		$servers = $this->getMediaServers();
		$servers[$index] = new kLiveMediaServer($index, $serverId, $hostname);
		
		$this->putInCustomData("mediaServers", $servers);	
	}
	
	protected function isMediaServerRegistered($index, $serverId)
	{
		$servers = $this->getMediaServers();
		if(isset($servers[$index]) && $servers[$index]->getMediaServerId() == $serverId)
			return true;
			
		return false;
	}
	
	public function unsetMediaServer($index, $serverId)
	{
		$servers = $this->getMediaServers();
		if(isset($servers[$index]) && $servers[$index]->getMediaServerId() == $serverId)
			unset($servers[$index]);
		
		$this->putInCustomData("mediaServers", $servers);	
	}
	
	/**
	 * @return array<kLiveMediaServer>
	 */
	public function getMediaServers()
	{
		$mediaServers = $this->getFromCustomData("mediaServers", null, array());
		foreach($mediaServers as $index => $mediaServer)
		{
			if(!$this->isCacheValid($mediaServer))
				unset($mediaServers[$index]);
		}
		
		return $mediaServers;	
	}
}
