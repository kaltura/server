<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class LiveEntry extends entry
{
	const IS_LIVE = 'isLive';
	const DEFAULT_CACHE_EXPIRY = 70;
	
	/* (non-PHPdoc)
	 * @see entry::getLocalThumbFilePath()
	 */
	public function getLocalThumbFilePath($version, $width, $height, $type, $bgcolor = "ffffff", $crop_provider = null, $quality = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $vid_sec = -1, $vid_slice = 0, $vid_slices = -1, $density = 0, $stripProfiles = false, $flavorId = null, $fileName = null)
	{
		if($this->getStatus() == entryStatus::DELETED || $this->getModerationStatus() == moderation::MODERATION_STATUS_BLOCK)
		{
			KalturaLog::log("rejected live stream entry - not serving thumbnail");
			KExternalErrors::dieError(KExternalErrors::ENTRY_DELETED_MODERATED);
		}
		
		$contentPath = myContentStorage::getFSContentRootPath();
		$msgPath = $contentPath . "content/templates/entry/thumbnail/live_thumb.jpg";
		return myEntryUtils::resizeEntryImage($this, $version, $width, $height, $type, $bgcolor, $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $msgPath, $density, $stripProfiles);
	}
	
	/* (non-PHPdoc)
	 * @see entry::validateFileSyncSubType($sub_type)
	 */
	protected static function validateFileSyncSubType($sub_type)
	{
		if(	$sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY || 
			$sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY || 
			$sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_THUMB || 
			$sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB )
			{
				return true;
			}
			
			KalturaLog::log("Sub type provided [$sub_type] is not one of knowen LiveEntry sub types validating from parent");
			return parent::validateFileSyncSubType($sub_type);
		
	}
	
	/* (non-PHPdoc)
	 * 
	 * There should be only one version of recorded segments directory
	 * New segments are appended to the existing directory
	 * 
	 * @see entry::getVersionForSubType($sub_type, $version)
	 */
	protected function getVersionForSubType($sub_type, $version = null)
	{
		if($sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY && $sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY)
			return 1;
			
		return parent::getVersionForSubType($sub_type, $version);
	}
	
	/* (non-PHPdoc)
	 * @see entry::generateFilePathArr($sub_type, $version)
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		static::validateFileSyncSubType($sub_type);
		
		if($sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY || $sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY)
		{
			$res = myContentStorage::getGeneralEntityPath('entry/data', $this->getIntId(), $this->getId(), $sub_type);
			return array(myContentStorage::getFSContentRootPath(), $res);
		}
		
		return parent::generateFilePathArr($sub_type, $version);
	}
	
	/* (non-PHPdoc)
	 * @see entry::generateFileName($sub_type, $version)
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		if($sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY || $sub_type == self::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY)
		{
			return $this->getId() . '_' . $sub_type;
		}
		
		return parent::generateFileName($sub_type, $version);
	}
	
	protected $decidingLiveProfile = false;
	
	/* (non-PHPdoc)
	 * @see Baseentry::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
			
		if(!$this->decidingLiveProfile && $this->conversion_profile_id && isset($this->oldCustomDataValues['mediaServers']))
		{
			$this->decidingLiveProfile = true;
			kBusinessConvertDL::decideLiveProfile($this);
		}
			
		return parent::postUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see Baseentry::postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		if(!$this->wasObjectSaved())
			return;
			
		parent::postInsert($con);
	
		if ($this->conversion_profile_id)
			kBusinessConvertDL::decideLiveProfile($this);
	}
	
	public function setOfflineMessage($v)
	{
		$this->putInCustomData("offlineMessage", $v);
	}
	public function getOfflineMessage()
	{
		return $this->getFromCustomData("offlineMessage");
	}
	
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
				
				$streamBitrate = array('bitrate' => $liveParamsItem->getVideoBitrate(), 'width' => $liveParamsItem->getWidth(), 'height' => $liveParamsItem->getHeight(), 'tags' => $liveParamsItem->getTags());
				$streamBitrates[] = $streamBitrate;
			}
			return $streamBitrates;
		}
		
		return array(array('bitrate' => 300, 'width' => 320, 'height' => 240));
	}
	
	public function getRecordedEntryId()
	{
		return $this->getFromCustomData("recorded_entry_id");
	}
	
	public function setRecordedEntryId($v)
	{
		$this->putInCustomData("recorded_entry_id", $v);
	}
	
	public function getRecordStatus()
	{
		return $this->getFromCustomData("record_status");
	}
	
	public function setRecordStatus($v)
	{
		$this->putInCustomData("record_status", $v);
	}
	
	public function getDvrStatus()
	{
		return $this->getFromCustomData("dvr_status");
	}
	
	public function setDvrStatus($v)
	{
		$this->putInCustomData("dvr_status", $v);
	}
	
	public function getDvrWindow()
	{
		return $this->getFromCustomData("dvr_window");
	}
	
	public function setDvrWindow($v)
	{
		$this->putInCustomData("dvr_window", $v);
	}
	
	public function setStreamName ( $v )	{	$this->putInCustomData ( "streamName" , $v );	}
	public function getStreamName (  )	{	return $this->getFromCustomData( "streamName", null, $this->getId() );	}
	
	
	public function setLiveStreamConfigurations(array $v)
	{
		$this->putInCustomData('live_stream_configurations', $v);
	}
	
	public function getLiveStreamConfigurationByProtocol($format, $protocol, $tag = null)
	{
		$configurations = $this->getLiveStreamConfigurations($protocol, $tag);
		foreach($configurations as $configuration)
		{
			/* @var $configuration kLiveStreamConfiguration */
			if($configuration->getProtocol() == $format)
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
			if(is_null($tag) && ($this->getConversionProfileId() || $this->getType() == entryType::LIVE_CHANNEL))
				$tag = 'all';
			
			$manifestUrl = $mediaServer->getManifestUrl($protocol);
			if($tag)
				$streamName = "smil:{$streamName}_{$tag}.smil";
			
			$rtmpStreamUrl = $manifestUrl;
			
			$manifestUrl .= $streamName;
			$hlsStreamUrl = "$manifestUrl/playlist.m3u8";
			$hdsStreamUrl = "$manifestUrl/manifest.f4m";
			$mpdStreamUrl = "$manifestUrl/manifest.mpd";
			$slStreamUrl = "$manifestUrl/Manifest";
			
			if($this->getDvrStatus() == DVRStatus::ENABLED)
			{
				$hlsStreamUrl .= "?DVR";
				$hdsStreamUrl .= "?DVR";
				$slStreamUrl .= "?dvr";
			}
			
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol(PlaybackProtocol::RTMP);
			$configuration->setUrl($rtmpStreamUrl);
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
		if(! count($mediaServers))
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
	
	/**
	 * @return boolean
	 */
	public function hasMediaServer($currentDcOnly = false)
	{
		$mediaServers = $this->getMediaServers();
		if(! count($mediaServers))
			return false;
		
		foreach($mediaServers as $mediaServer)
		{
			/* @var $mediaServer kLiveMediaServer */
			if($mediaServer->getDc() == kDataCenterMgr::getCurrentDcId())
				return true;
		}
		
		return !$currentDcOnly;
	}
	
	private static function getCacheType($dc)
	{
		return kCacheManager::CACHE_TYPE_LIVE_MEDIA_SERVER . "_$dc";
	}
	
	private function isCacheValid(kLiveMediaServer $mediaServer)
	{
		$cacheType = self::getCacheType($mediaServer->getDc());
		$cacheStore = kCacheManager::getSingleLayerCache($cacheType);
		if(! $cacheStore)
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
		$cacheType = self::getCacheType(kDataCenterMgr::getCurrentDcId());
		$cacheStore = kCacheManager::getSingleLayerCache($cacheType);
		if(! $cacheStore)
			return false;
		
		return $cacheStore->set($key, true, kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY));
	}
	
	public function setMediaServer($index, $serverId, $hostname)
	{
		$key = $this->getId() . "_{$serverId}_{$index}";
		if($this->storeInCache($key) && $this->isMediaServerRegistered($index, $serverId))
			return;
		
		$server = new kLiveMediaServer($index, $serverId, $hostname);
		$this->putInCustomData("server-$index", $server, 'mediaServers');
	}
	
	protected function isMediaServerRegistered($index, $serverId)
	{
		$server = $this->getFromCustomData("server-$index", 'mediaServers');
		if($server && $server->getMediaServerId() == $serverId)
			return true;
		
		return false;
	}
	
	public function unsetMediaServer($index, $serverId)
	{
		$server = $this->getFromCustomData("server-$index", 'mediaServers');
		if($server && $server->getMediaServerId() == $serverId)
			$server = $this->removeFromCustomData("server-$index", 'mediaServers');
	}
	
	/**
	 * @return bool true is list changed
	 */
	public function validateMediaServers()
	{
		$listChanged = false;
		$mediaServers = $this->getFromCustomData(null, 'mediaServers', array());
		foreach($mediaServers as $key => $mediaServer)
		{
			if(! $this->isCacheValid($mediaServer))
			{
				$listChanged = true;
				$this->removeFromCustomData($key, 'mediaServers');
			}
		}
		
		return $listChanged;
	}
	
	/**
	 * @return array<kLiveMediaServer>
	 */
	public function getMediaServers()
	{
		return $this->getFromCustomData(null, 'mediaServers', array());
	}
	
	/* (non-PHPdoc)
	 * @see entry::getDynamicAttributes()
	 */
	public function getDynamicAttributes()
	{
		return array(LiveEntry::IS_LIVE => intval($this->hasMediaServer()));
	}
	
	/**
	 * @param entry $entry
	 */
	public function attachPendingMediaEntry(entry $entry, $requiredDuration)
	{
		$attachedPendingMediaEntries = $this->getAttachedPendingMediaEntries();
		$attachedPendingMediaEntries[$entry->getId()] = new kPendingMediaEntry($entry->getId(), kDataCenterMgr::getCurrentDcId(), $requiredDuration);
		
		$this->setAttachedPendingMediaEntries($attachedPendingMediaEntries);
	}
	
	/**
	 * @param string $entryId
	 */
	public function dettachPendingMediaEntry($entryId)
	{
		$attachedPendingMediaEntries = $this->getAttachedPendingMediaEntries();
		if(isset($attachedPendingMediaEntries[$entryId]))
			unset($attachedPendingMediaEntries[$entryId]);
		
		$this->setAttachedPendingMediaEntries($attachedPendingMediaEntries);
	}
	
	/**
	 * @param array $attachedPendingMediaEntries
	 */
	protected function setAttachedPendingMediaEntries(array $attachedPendingMediaEntries)
	{
		$this->putInCustomData("attached_pending_media_entries", $attachedPendingMediaEntries);
	}
	
	/**
	 * @return array
	 */
	public function getAttachedPendingMediaEntries()
	{
		return $this->getFromCustomData('attached_pending_media_entries', null, array());
	}
	
	/**
	 * @param int $jobId
	 */
	public function addConvertingSegment($jobId)
	{
		$convertingSegments = $this->getConvertingSegments();
		$convertingSegments[$jobId] = true;
		
		$this->setConvertingSegments($convertingSegments);
	}
	
	/**
	 * @param int $jobId
	 */
	public function removeConvertingSegment($jobId)
	{
		$convertingSegments = $this->getConvertingSegments();
		if(isset($convertingSegments[$jobId]))
			unset($convertingSegments[$jobId]);
		
		$this->setConvertingSegments($convertingSegments);
	}
	
	/**
	 * @param array $convertingSegments
	 */
	protected function setConvertingSegments(array $convertingSegments)
	{
		$this->putInCustomData("converting_segments", $convertingSegments);
	}
	
	/**
	 * @return array
	 */
	protected function getConvertingSegments()
	{
		return $this->getFromCustomData('converting_segments', null, array());
	}
	
	/**
	 * @return boolean
	 */
	public function isConvertingSegments()
	{
		$convertingSegments = $this->getConvertingSegments();
		return count($convertingSegments) > 0;
	}
}
