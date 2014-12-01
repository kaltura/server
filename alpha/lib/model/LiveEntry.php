<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class LiveEntry extends entry
{
	const IS_LIVE = 'isLive';
	const FIRST_BROADCAST = 'first_broadcast';
	const RECORDED_ENTRY_ID = 'recorded_entry_id';

	const DEFAULT_CACHE_EXPIRY = 120;
	
	const CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS = 'mediaServers';
	
	static $kalturaLiveSourceTypes = array(EntrySourceType::LIVE_STREAM, EntrySourceType::LIVE_CHANNEL, EntrySourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS);
	
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
		
		$liveEntryExist = false;
		$liveThumbEntry = null;
		$liveThumbEntryId = null;
		
		$partner = $this->getPartner();
		if ($partner)
			$liveThumbEntryId = $partner->getLiveThumbEntryId();
		if ($liveThumbEntryId)
			$liveThumbEntry = entryPeer::retrieveByPK($liveThumbEntryId);

		if ($liveThumbEntry && $liveThumbEntry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$fileSyncVersion = $partner->getLiveThumbEntryVersion();
			$liveEntryKey = $liveThumbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA,$fileSyncVersion);
			$contentPath = kFileSyncUtils::getLocalFilePathForKey($liveEntryKey);
			if ($contentPath)
			{
				$msgPath = $contentPath;
				$liveEntryExist = true;
			}
			else
				KalturaLog::err('no local file sync for audio entry id');
		}

		if (!$liveEntryExist)
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
			
		if(!$this->decidingLiveProfile && $this->conversion_profile_id && isset($this->oldCustomDataValues[LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS]))
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
		
		if(in_array($this->getSource(), array(EntrySourceType::LIVE_STREAM, EntrySourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS)))
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
	
	public function getLastElapsedRecordingTime()		{ return $this->getFromCustomData( "lastElapsedRecordingTime", null, 0 ); }
	public function setLastElapsedRecordingTime( $v )	{ $this->putInCustomData( "lastElapsedRecordingTime" , $v ); }

	public function setStreamName ( $v )	{	$this->putInCustomData ( "streamName" , $v );	}
	public function getStreamName (  )	{	return $this->getFromCustomData( "streamName", null, $this->getId() . '_%i' );	}
	
	public function setFirstBroadcast ( $v )	{	$this->putInCustomData ( "first_broadcast" , $v );	}
	public function getFirstBroadcast (  )	{	return $this->getFromCustomData( "first_broadcast");	}
	
	public function setCurrentBroadcastStartTime( $v )	{ $this->putInCustomData ( "currentBroadcastStartTime" , $v ); }
	public function getCurrentBroadcastStartTime()		{ return $this->getFromCustomData( "currentBroadcastStartTime", null, 0 ); }

	public function setLastBroadcast ( $v )	{	$this->putInCustomData ( "last_broadcast" , $v );	}
	public function getLastBroadcast (  )	{	return $this->getFromCustomData( "last_broadcast");	}
	
	public function getPushPublishEnabled()
	{
		return $this->getFromCustomData("push_publish_enabled", null, false);
	}
	
	public function setPushPublishEnabled($v)
	{
		$this->putInCustomData("push_publish_enabled", $v);
	}
	
	public function getSyncDCs()
	{
		return $this->getFromCustomData("sync_dcs", null, false);
	}
	
	public function setSyncDCs($v)
	{
		$this->putInCustomData("sync_dcs", $v);
	}
	
	public function setLiveStreamConfigurations(array $v)
	{
		if (!in_array($this->getSource(), self::$kalturaLiveSourceTypes) )
			$this->putInCustomData('live_stream_configurations', $v);
	}
	
	public function getLiveStreamConfigurationByProtocol($format, $protocol, $tag = null, $currentDcOnly = false)
	{
		$configurations = $this->getLiveStreamConfigurations($protocol, $tag, $currentDcOnly);
		foreach($configurations as $configuration)
		{
			/* @var $configuration kLiveStreamConfiguration */
			if($configuration->getProtocol() == $format)
				return $configuration;
		}
		
		return null;
	}
	
	public function getLiveStreamConfigurations($protocol = 'http', $tag = null, $currentDcOnly = false)
	{
		$configurations = array();
		if (!in_array($this->getSource(), self::$kalturaLiveSourceTypes))
		{
			$configurations = $this->getFromCustomData('live_stream_configurations', null, array());
			if($configurations && $this->getPushPublishEnabled())
			{
				$pushPublishConfigurations = $this->getPushPublishConfigurations();
				$configurations = array_merge($configurations, $pushPublishConfigurations);
			}
			return $configurations;
		}
		
		$primaryMediaServer = null;
		$backupMediaServer = null;
		$primaryApplicationName = null;
		$backupApplicationName = null;
		
		$kMediaServers = $this->getMediaServers();
		if(count($kMediaServers))
		{
			foreach($kMediaServers as $key => $kMediaServer)
			{
				if($kMediaServer && $kMediaServer instanceof kLiveMediaServer)
				{
					KalturaLog::debug("mediaServer->getDc [" . $kMediaServer->getDc() . "] == kDataCenterMgr::getCurrentDcId [" . kDataCenterMgr::getCurrentDcId() . "]");
					if($kMediaServer->getDc() == kDataCenterMgr::getCurrentDcId())
					{
						$primaryMediaServer = $kMediaServer->getMediaServer();
						$primaryApplicationName = $kMediaServer->getApplicationName();
						unset($kMediaServers[$key]);
					}
				}
			}
			
			if(!$primaryMediaServer)
			{
				if($currentDcOnly)
					return array();
					
				$kMediaServer = array_shift($kMediaServers);
				if($kMediaServer && $kMediaServer instanceof kLiveMediaServer)
				{
					$primaryMediaServer = $kMediaServer->getMediaServer();
					$primaryApplicationName = $kMediaServer->getApplicationName();
				}
			}
			
			
				
			if(!$currentDcOnly && count($kMediaServers))
			{
				$kMediaServer = reset($kMediaServers);
				if($kMediaServer && $kMediaServer instanceof kLiveMediaServer)
				{
					$backupMediaServer = $kMediaServer->getMediaServer();
					$backupApplicationName = $kMediaServer->getApplicationName();
				}
			}
		}
		
		$manifestUrl = null;
		$backupManifestUrl = null;
		
		if (count ($this->getPartner()->getLiveStreamPlaybackUrlConfigurations()))
		{
			$partnerConfigurations = $this->getPartner()->getLiveStreamPlaybackUrlConfigurations();
			
			if (isset($partnerConfigurations[$protocol]))
				$manifestUrl = $partnerConfigurations[$protocol];
		}
		elseif($primaryMediaServer)
		{
			$manifestUrl = $primaryMediaServer->getManifestUrl($protocol, $this->getPartner()->getMediaServersConfiguration());
			if($backupMediaServer)
			{
				$backupManifestUrl = $backupMediaServer->getManifestUrl($protocol, $this->getPartner()->getMediaServersConfiguration());
			}
		}
		
		$rtmpStreamUrl = null;
		$hlsStreamUrl = null;
		$hdsStreamUrl = null;
		$slStreamUrl = null;
		$mpdStreamUrl = null;
		$hlsBackupStreamUrl = null;
		$hdsBackupStreamUrl = null;
		
		if ($manifestUrl)
		{
			$manifestUrl .= "$primaryApplicationName/";
			$streamName = $this->getId();
			if(is_null($tag) && ($this->getConversionProfileId() || $this->getType() == entryType::LIVE_CHANNEL))
				$tag = 'all';
			
			if($tag)
				$streamName = "smil:{$streamName}_{$tag}.smil";
			
			$rtmpStreamUrl = $manifestUrl;
			
			$manifestUrl .= $streamName;
			$hlsStreamUrl = "$manifestUrl/playlist.m3u8";
			$hdsStreamUrl = "$manifestUrl/manifest.f4m";
			$slStreamUrl = "$manifestUrl/Manifest";
			$mpdStreamUrl = "$manifestUrl/manifest.mpd";
			
			if($backupManifestUrl)
			{
				$backupManifestUrl .= "$backupApplicationName/";
				$backupManifestUrl .= $streamName;
				$hlsBackupStreamUrl = "$backupManifestUrl/playlist.m3u8";
				$hdsBackupStreamUrl = "$backupManifestUrl/manifest.f4m";
			}
			
			if($this->getDvrStatus() == DVRStatus::ENABLED)
			{
				$hlsStreamUrl .= "?DVR";
				$hdsStreamUrl .= "?DVR";
				$slStreamUrl .= "?dvr";
				
				if($backupManifestUrl)
				{
					$hlsBackupStreamUrl .= "?DVR";
					$hdsBackupStreamUrl .= "?DVR";
				}
			}
		}
			
//		TODO - enable it and test it in non-SaaS environment
//		$configuration = new kLiveStreamConfiguration();
//		$configuration->setProtocol(PlaybackProtocol::RTMP);
//		$configuration->setUrl($rtmpStreamUrl);
//		$configurations[] = $configuration;
		
		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::HDS);
		$configuration->setUrl($hdsStreamUrl);
		$configuration->setBackupUrl($hdsBackupStreamUrl);
		$configurations[] = $configuration;
		
		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::HLS);
		$configuration->setUrl($hlsStreamUrl);
		$configuration->setBackupUrl($hlsBackupStreamUrl);
		$configurations[] = $configuration;
		
		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::APPLE_HTTP);
		$configuration->setUrl($hlsStreamUrl);
		$configuration->setBackupUrl($hlsBackupStreamUrl);
		$configurations[] = $configuration;
		
		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::SILVER_LIGHT);
		$configuration->setUrl($slStreamUrl);
		$configurations[] = $configuration;
		
		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::MPEG_DASH);
		$configuration->setUrl($mpdStreamUrl);
		$configurations[] = $configuration;
		
		if ($this->getPushPublishEnabled())
		{
			$pushPublishConfigurations = $this->getPushPublishPlaybackConfigurations();
			$configurations = array_merge($configurations, $pushPublishConfigurations);
		}
		
		return $configurations;
	}
	
	/**
	 * @return MediaServer
	 */
	public function getMediaServer($currentDcOnly = false)
	{
		$kMediaServers = $this->getMediaServers();
		if(! count($kMediaServers))
			return null;
		
		foreach($kMediaServers as $kMediaServer)
		{
			if($kMediaServer && $kMediaServer instanceof kLiveMediaServer)
			{
				KalturaLog::debug("mediaServer->getDc [" . $kMediaServer->getDc() . "] == kDataCenterMgr::getCurrentDcId [" . kDataCenterMgr::getCurrentDcId() . "]");
				if($kMediaServer->getDc() == kDataCenterMgr::getCurrentDcId())
					return $kMediaServer->getMediaServer();
			}
		}
		if($currentDcOnly)
			return null;
		
		$kMediaServer = reset($kMediaServers);
		if($kMediaServer && $kMediaServer instanceof kLiveMediaServer)
			return $kMediaServer->getMediaServer();
			
		KalturaLog::debug("No Valid Media Servers Were Found For Current Live Entry [" . $this->getEntryId() . "]" );
		return null;
	}
	
	/**
	 * @return boolean
	 */
	public function hasMediaServer($currentDcOnly = false)
	{
		$kMediaServers = $this->getMediaServers();
		if(! count($kMediaServers))
			return false;
		
		foreach($kMediaServers as $kMediaServer)
		{
			if($kMediaServer instanceof kLiveMediaServer)
			{
				/* @var $kMediaServer kLiveMediaServer */
				if($kMediaServer->getDc() == kDataCenterMgr::getCurrentDcId())
					return true;
			}
		}
		
		return !$currentDcOnly;
	}
	
	private static function getCacheType()
	{
		return kCacheManager::CACHE_TYPE_LIVE_MEDIA_SERVER . '_' . kDataCenterMgr::getCurrentDcId();
	}
	
	private function isCacheValid(kLiveMediaServer $kMediaServer)
	{
		$cacheType = self::getCacheType();
		$cacheStore = kCacheManager::getSingleLayerCache($cacheType);
		if(! $cacheStore)
		{
			KalturaLog::warning("Cache store [$cacheType] not found");
			$lastUpdate = time() - $kMediaServer->getTime();
			$expiry = kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY);
			
			return $lastUpdate <= $expiry;
		}
		
		$key = $this->getId() . '_' . $kMediaServer->getHostname() . '_' . $kMediaServer->getIndex();
		KalturaLog::debug("Get cache key [$key] from store [$cacheType]");
		return $cacheStore->get($key);
	}
	
	/**
	 *
	 * Store given value in cache for with the given key as an identifier
	 * @param string $key
	 */
	private function storeInCache($key)
	{
		$cacheType = self::getCacheType();
		$cacheStore = kCacheManager::getSingleLayerCache($cacheType);
		if(! $cacheStore)
			return false;
		
		return $cacheStore->set($key, true, kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY));
	}
	
	public function setMediaServer($index, $hostname, $applicationName = null)
	{
		$mediaServer = MediaServerPeer::retrieveByHostname($hostname);
		if (!$mediaServer)
		{
			KalturaLog::info("External media server with hostname [$hostname] is being used to stream this entry");
		}
		
		$key = $this->getId() . "_{$hostname}_{$index}";
		if($this->storeInCache($key) && $this->isMediaServerRegistered($index, $hostname))
			return;
		
		$this->setLastBroadcast(time());
		$server = new kLiveMediaServer($index, $hostname, $mediaServer ? $mediaServer->getDc() : null, $mediaServer ? $mediaServer->getId() : null, 
			$applicationName ? $applicationName : MediaServer::DEFAULT_APPLICATION);
		$this->putInCustomData("server-$index", $server, LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS);
	}
	
	protected function isMediaServerRegistered($index, $hostname)
	{
		$server = $this->getFromCustomData("server-$index", LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS);
		/* @var $server kLiveMediaServer */
		if($server && $server->getHostname() == $hostname)
			return true;
		
		return false;
	}
	
	public function unsetMediaServer($index, $hostname)
	{	
		$server = $this->getFromCustomData("server-$index", LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS);
		if($server && $server->getHostname() == $hostname)
			$server = $this->removeFromCustomData("server-$index", LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS);
	}
	
	/**
	 * @return bool true is list changed
	 */
	public function validateMediaServers()
	{
		$listChanged = false;
		$kMediaServers = $this->getFromCustomData(null, LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS, array());
		foreach($kMediaServers as $key => $kMediaServer)
		{
			if(!$kMediaServer || ! $this->isCacheValid($kMediaServer))
			{
				$listChanged = true;
				KalturaLog::debug("Removing media server [" . ($kMediaServer ? $kMediaServer->getHostname() : $key) . "]");
				$this->removeFromCustomData($key, LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS);
			}
		}
		
		return $listChanged;
	}
	
	/**
	 * @return array<kLiveMediaServer>
	 */
	public function getMediaServers()
	{
		return $this->getFromCustomData(null, LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS, array());
	}
	
	/* (non-PHPdoc)
	 * @see entry::getDynamicAttributes()
	 */
	public function getDynamicAttributes()
	{
		$dynamicAttributes = array(
				LiveEntry::IS_LIVE => intval($this->hasMediaServer()),
				LiveEntry::FIRST_BROADCAST => $this->getFirstBroadcast(),
				LiveEntry::RECORDED_ENTRY_ID => $this->getRecordedEntryId(),
		);
		
		return array_merge( $dynamicAttributes, parent::getDynamicAttributes() ); 
	}
	
	/**
	 * @param entry $entry
	 */
	public function attachPendingMediaEntry(entry $entry, $requiredDuration, $offset, $duration)
	{
		$attachedPendingMediaEntries = $this->getAttachedPendingMediaEntries();
		$attachedPendingMediaEntries[$entry->getId()] = new kPendingMediaEntry($entry->getId(), kDataCenterMgr::getCurrentDcId(), $requiredDuration, $offset, $duration);
		
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
	
	public function getPushPublishPlaybackConfigurations ()
	{
		return $this->getFromCustomData('push_publish_playback_configurations',null, array());
	}
	
	public function setPushPublishPlaybackConfigurations ($v)
	{
		$this->putInCustomData('push_publish_playback_configurations', $v);
	}
	
	public function getPublishConfigurations ()
	{
		return $this->getFromCustomData('push_publish_configurations', null, array());
	}
	
	public function setPublishConfigurations ($v)
	{
		$this->putInCustomData('push_publish_configurations', $v);
	}
	
	/**
	 * @return boolean
	 */
	public function isConvertingSegments()
	{
		$criteria = new Criteria();
		$criteria->add(BatchJobLockPeer::PARTNER_ID, $this->getPartnerId());
		$criteria->add(BatchJobLockPeer::ENTRY_ID, $this->getId());
		$criteria->add(BatchJobLockPeer::JOB_TYPE, BatchJobType::CONVERT_LIVE_SEGMENT);
		$criteria->add(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId());
		
		$batchJob = BatchJobLockPeer::doSelectOne($criteria);
		if($batchJob)
			return true;
			
		return false;
	}
	
	protected function getTrackColumns ()
	{
		$basicColumns = parent::getTrackColumns();
		return array_merge($basicColumns, array ('mediaServers' => array('server-0'),));
	}
	
	/**
	 * 
	 * This function returns the tracking object's string value
	 */
	protected function getTrackEntryString ($namespace, $customDataColumn, $value)
	{
		if ($namespace == 'mediaServers' && $value instanceof kLiveMediaServer)
		{
			return $value->getHostname();
		}
	}
	
	
}
