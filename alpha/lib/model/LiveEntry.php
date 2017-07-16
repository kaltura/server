<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class LiveEntry extends entry
{
	const IS_LIVE = 'isLive';
	const PRIMARY_HOSTNAME = 'primaryHostname';
	const SECONDARY_HOSTNAME = 'backupHostname';
	const FIRST_BROADCAST = 'first_broadcast';
	const RECORDED_ENTRY_ID = 'recorded_entry_id';

	const DEFAULT_CACHE_EXPIRY = 120;
	const DEFAULT_SEGMENT_DURATION_MILLISECONDS = 10000;
	
	const CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS = 'mediaServers';
	const CUSTOM_DATA_RECORD_STATUS = 'record_status';
	const CUSTOM_DATA_RECORD_OPTIONS = 'recording_options';
	const CUSTOM_DATA_SEGMENT_DURATION = 'segmentDuration';
	static $kalturaLiveSourceTypes = array(EntrySourceType::LIVE_STREAM, EntrySourceType::LIVE_CHANNEL, EntrySourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS);
	
	protected $decidingLiveProfile = false;
	
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
	
	/**
	 * Code to be run before updating the object in database
	 * @param PropelPDO $con
	 * @return bloolean
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(entryPeer::CONVERSION_PROFILE_ID) || $this->isCustomDataModified(LiveEntry::CUSTOM_DATA_RECORD_STATUS)
				|| $this->isCustomDataModified(LiveEntry::CUSTOM_DATA_RECORD_OPTIONS))
		{
			$this->setRecordedEntryId(null);
			$this->setRedirectEntryId(null);
			$this->setCustomDataObj();
		}
		
		return parent::preUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see Baseentry::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		//When working with Kaltura live recording the recorded entry is playable immediately after the first duration reporting
		//Check the entry is no the replacement one to avoid marking replacement recorded entries as Ready before all conversion are done 
		if($this->isColumnModified(entryPeer::LENGTH_IN_MSECS) && $this->getLengthInMsecs() > 0 && $this->getRecordStatus() !== RecordStatus::DISABLED && $this->getRecordedEntryId())
		{
			$recordedEntry = entryPeer::retrieveByPK($this->getRecordedEntryId());
			if($recordedEntry && $recordedEntry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE)
			{
				if($recordedEntry->getStatus() != entryStatus::READY)
					$recordedEntry->setStatus(entryStatus::READY);
				$recordedEntry->setRecordedLengthInMsecs($this->getLengthInMsecs());
				$recordedEntry->save();
			}
		}
		
		if((!$this->decidingLiveProfile && $this->conversion_profile_id && isset($this->oldCustomDataValues[LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS])) || $this->isColumnModified(entryPeer::CONVERSION_PROFILE_ID))
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
		if($v && $v != $this->getRecordedEntryId())
			$this->incInCustomData("recorded_entry_index");
		
		$this->putInCustomData("recorded_entry_id", $v);
	}
	
	public function getRecordedEntryIndex()
	{
		return $this->getFromCustomData("recorded_entry_index", null, 0);
	}
	
	public function getRecordStatus()
	{
		return $this->getFromCustomData(LiveEntry::CUSTOM_DATA_RECORD_STATUS);
	}
	
	public function setRecordStatus($v)
	{
		$this->putInCustomData(LiveEntry::CUSTOM_DATA_RECORD_STATUS, $v);
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
	
	protected function setFirstBroadcast ( $v )	{	$this->putInCustomData ( "first_broadcast" , $v );	}
	public function getFirstBroadcast (  )	{	return $this->getFromCustomData( "first_broadcast");	}
	
	public function setCurrentBroadcastStartTime( $v )	{ $this->putInCustomData ( "currentBroadcastStartTime" , $v ); }
	public function getCurrentBroadcastStartTime()		{ return $this->getFromCustomData( "currentBroadcastStartTime", null, 0 ); }

	public function setLastBroadcast ( $v )	{	$this->putInCustomData ( "last_broadcast" , $v );	}
	public function getLastBroadcast (  )	{	return $this->getFromCustomData( "last_broadcast");	}
	
	public function setLastBroadcastEndTime ( $v )	{	$this->putInCustomData ( "last_broadcast_end_time" , $v );	}
	public function getLastBroadcastEndTime (  )	{	return (int) $this->getFromCustomData( "last_broadcast_end_time", null, 0);	}
	
	public function setLastCuePointSyncTime ( $v )	{	$this->putInCustomData ( "last_cue_point_sync_time" , $v );	}
	public function getLastCuePointSyncTime (  )	{	return (int) $this->getFromCustomData("last_cue_point_sync_time");	}
	
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
	
	public function getCustomLiveStreamConfigurations()
	{
		return $this->getFromCustomData('live_stream_configurations', null, array());
	}
	
	public function getLiveStreamConfigurationByProtocol($format, $protocol, $tag = null, $currentDcOnly = false, array $flavorParamsIds = array())
	{
		$configurations = $this->getLiveStreamConfigurations($protocol, $tag, $currentDcOnly, $flavorParamsIds);
		foreach($configurations as $configuration)
		{
			/* @var $configuration kLiveStreamConfiguration */
			if($configuration->getProtocol() == $format)
				return $configuration;
		}
		
		return null;
	}
	
	public function getLiveStreamConfigurations($protocol = 'http', $tag = null, $currentDcOnly = false, array $flavorParamsIds = array())
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

		$cdnApiHost = kConf::get("cdn_api_host");
		$baseManifestUrl = "$protocol://$cdnApiHost/p/{$this->partner_id}/sp/{$this->partner_id}00/playManifest/entryId/{$this->id}/protocol/$protocol";

		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::HDS);
		$configuration->setUrl($baseManifestUrl . "/format/hds/a.f4m");
		$configurations[] = $configuration;

		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::HLS);
		$configuration->setUrl($baseManifestUrl . "/format/applehttp/a.m3u8");
		$configurations[] = $configuration;

		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::APPLE_HTTP);
		$configuration->setUrl($baseManifestUrl . "/format/applehttp/a.m3u8");
		$configurations[] = $configuration;

		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::APPLE_HTTP_TO_MC);
		$configuration->setUrl($baseManifestUrl . "/format/applehttp_to_mc/a.m3u8");
		$configurations[] = $configuration;

		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::SILVER_LIGHT);
		$configuration->setUrl($baseManifestUrl . "/format/sl/Manifest");
		$configurations[] = $configuration;

		$configuration = new kLiveStreamConfiguration();
		$configuration->setProtocol(PlaybackProtocol::MPEG_DASH);
		$configuration->setUrl($baseManifestUrl . "/format/mpegdash/manifest.mpd");
		$configurations[] = $configuration;

		if ($this->getPushPublishEnabled())
		{
			$pushPublishConfigurations = $this->getPushPublishPlaybackConfigurations();
			$configurations = array_merge($configurations, $pushPublishConfigurations);
		}

		return $configurations;
	}

	/**
	 * @return MediaServerNode
	 */
	public function getMediaServer($currentDcOnly = false)
	{
		$liveEntryServerNodes = $this->getPlayableEntryServerNodes();
		if(!count($liveEntryServerNodes))
			return null;
		
		/* @var LiveEntryServerNode $liveEntryServerNode*/
		foreach($liveEntryServerNodes as $liveEntryServerNode)
		{
			/* @var WowzaMediaServerNode $serverNode */
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if($serverNode)
			{
				KalturaLog::debug("mediaServer->getDc [" . $serverNode->getDc() . "] == kDataCenterMgr::getCurrentDcId [" . kDataCenterMgr::getCurrentDcId() . "]");
				if($serverNode->getDc() == kDataCenterMgr::getCurrentDcId())
					return $serverNode;
			}
		}
		
		if($currentDcOnly)
			return null;
		
		$liveEntryServerNode = reset($liveEntryServerNodes);
		if ($liveEntryServerNode)
			return ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());

		KalturaLog::info("No Valid Media Servers Were Found For Current Live Entry [" . $this->getEntryId() . "]" );
		return null;
	}

	protected function getMediaServersHostnames()
	{
		$hostnames = array();
		$liveEntryServerNodes = $this->getPlayableEntryServerNodes();

		/* @var LiveEntryServerNode $liveEntryServerNode*/
		foreach($liveEntryServerNodes as $liveEntryServerNode)
		{
			/* @var WowzaMediaServerNode $serverNode*/
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if ($serverNode)
				$hostnames[$liveEntryServerNode->getServerType()] = $serverNode->getHostname();
		}
		
		KalturaLog::info("media servers hostnames: " . print_r($hostnames,true));
		return $hostnames;
	}
	
	/**
	 * @return boolean
	 */
	public function hasMediaServer($currentDcOnly = false)
	{
		$liveEntryServerNodes = $this->getPlayableEntryServerNodes();
		if(!count($liveEntryServerNodes))
			return false;

		/* @var LiveEntryServerNode $liveEntryServerNode*/
		foreach($liveEntryServerNodes as $liveEntryServerNode)
		{
			/* @var WowzaMediaServerNode $serverNode*/
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if($serverNode->getDc() == kDataCenterMgr::getCurrentDcId())
				return true;
		}
		
		return !$currentDcOnly;
	}
	
	private static function getCacheType()
	{
		return kCacheManager::CACHE_TYPE_LIVE_MEDIA_SERVER . '_' . kDataCenterMgr::getCurrentDcId();
	}

	/**
	 * @param LiveEntryServerNode $liveEntryServerNode
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function isCacheValid(LiveEntryServerNode $liveEntryServerNode)
	{
		if(!$liveEntryServerNode)
			return false;
		
		$cacheType = self::getCacheType();
		$cacheStore = kCacheManager::getSingleLayerCache($cacheType);
		if(! $cacheStore)
		{
			KalturaLog::warning("Cache store [$cacheType] not found");
			$lastUpdate = time() - $liveEntryServerNode->getUpdatedAt(null);
			$expiry = kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY);
			
			return $lastUpdate <= $expiry;
		}
		
		$key = $this->getEntryServerNodeCacheKey($liveEntryServerNode);
		$ans = $cacheStore->get($key);
		KalturaLog::debug("Get cache key [$key] from store [$cacheType] returned [$ans]");
		return $ans;
	}

	/**
	 * Stores given value in cache for with the given key as an identifier
	 * @param string $key
	 * @return bool
	 * @throws Exception
	 */
	private function storeInCache($key)
	{
		$cacheType = self::getCacheType();
		$cacheStore = kCacheManager::getSingleLayerCache($cacheType);
		if(! $cacheStore) {
			KalturaLog::debug("cacheStore is null. cacheType: $cacheType . returning false");
			return false;
		}
		KalturaLog::debug("Set cache key [$key] from store [$cacheType] ");
		return $cacheStore->set($key, true, kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY));
	}

	/**
	 * @param EntryServerNodeType $mediaServerIndex
	 * @param $hostname
	 * @throws Exception
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws kCoreException
	 */
	public function setMediaServer($mediaServerIndex, $hostname, $liveEntryStatus, $applicationName = null)
	{
		/* @var $mediaServerNode MediaServerNode */
		$mediaServerNode = ServerNodePeer::retrieveActiveMediaServerNode($hostname);
		if (!$mediaServerNode)
			throw new kCoreException("Media server with host name [$hostname] not found", kCoreException::MEDIA_SERVER_NOT_FOUND);

		$dbLiveEntryServerNode = $this->getLiveEntryServerNode($hostname, $mediaServerIndex, $liveEntryStatus, $mediaServerNode->getId(), $applicationName);
		
		if($liveEntryStatus === EntryServerNodeStatus::PLAYABLE)
		{
			if(is_null($this->getFirstBroadcast()))
				$this->setFirstBroadcast(kApiCache::getTime());
			
			$key = $this->getEntryServerNodeCacheKey($dbLiveEntryServerNode);
			if($this->storeInCache($key) && $this->isMediaServerRegistered($mediaServerIndex, $hostname))
			{
				KalturaLog::debug("cached and registered - index: $mediaServerIndex, hostname: $hostname");
				return;
			}
		}
		
		return $dbLiveEntryServerNode;
	}
	
	private function getLiveEntryServerNode($hostname, $mediaServerIndex, $liveEntryStatus, $serverNodeId, $applicationName = null)
	{
		$shouldSave = false;
		/* @var $dbLiveEntryServerNode LiveEntryServerNode */
		$dbLiveEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($this->getId(), $mediaServerIndex);
		
		if (!$dbLiveEntryServerNode)
		{
			KalturaLog::debug("About to register new media server with index: [$mediaServerIndex], hostname: [$hostname], status: [$liveEntryStatus]");
			$shouldSave = true;
			$dbLiveEntryServerNode = new LiveEntryServerNode();
			$dbLiveEntryServerNode->setEntryId($this->getId());
			$dbLiveEntryServerNode->setServerType($mediaServerIndex);
			$dbLiveEntryServerNode->setServerNodeId($serverNodeId);
			$dbLiveEntryServerNode->setPartnerId($this->getPartnerId());
			$dbLiveEntryServerNode->setStatus($liveEntryStatus);
			$dbLiveEntryServerNode->setDc(kDataCenterMgr::getCurrentDcId());
			
			if($applicationName)
				$dbLiveEntryServerNode->setApplicationName($applicationName);
		}
		
		if ($dbLiveEntryServerNode->getStatus() !== $liveEntryStatus)
		{
			$shouldSave = true;
			$dbLiveEntryServerNode->setStatus($liveEntryStatus);	
		}
		
		if (kDataCenterMgr::getCurrentDcId() !== $dbLiveEntryServerNode->getDc())
		{
			$shouldSave = true;
			$dbLiveEntryServerNode->setDc(kDataCenterMgr::getCurrentDcId());
		}
		
		if ($dbLiveEntryServerNode->getServerNodeId() !== $serverNodeId)
		{
			$shouldSave = true;
			KalturaLog::debug("Updating media server id from [" . $dbLiveEntryServerNode->getServerNodeId() . "] to [$serverNodeId]");
			$dbLiveEntryServerNode->setServerNodeId($serverNodeId);
		}
		
		if($shouldSave)
			$dbLiveEntryServerNode->save();
		
		return $dbLiveEntryServerNode;
	}

	/**
	 * Call this function only if there are no EntryServerNodes for this entry
	 */
	public function unsetMediaServer()
	{
		if ($this->getRecordedEntryId())
		{
			$this->setRedirectEntryId($this->getRecordedEntryId());
		}

		if ( $this->getCurrentBroadcastStartTime() )
		{
			$this->setCurrentBroadcastStartTime( 0 );
		}
	}


	private function getEntryServerNodeCacheKey(EntryServerNode $entryServerNode)
	{
		return $entryServerNode->getEntryId()."_".$entryServerNode->getServerNodeId()."_".$entryServerNode->getServerType();
	}

	protected function isMediaServerRegistered($index, $hostname)
	{
		/* @var $dbLiveEntryServerNode LiveEntryServerNode*/
		$dbLiveEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($this->getId(), $index);
		if ($dbLiveEntryServerNode)
			return true;
		KalturaLog::info("mediaServer is not registered. hostname: $hostname , index: $index ");
		return false;
	}
	
	/**
	 * removes the relevant Entry Server Nodes
	 */
	public function validateMediaServers()
	{
		$dbLiveEntryServerNodes = EntryServerNodePeer::retrieveByEntryId($this->getId());
		/* @var $dbLiveEntryServerNode LiveEntryServerNode */
		foreach($dbLiveEntryServerNodes as $dbLiveEntryServerNode)
		{
			if ($dbLiveEntryServerNode->getDc() === kDataCenterMgr::getCurrentDcId() && !$this->isCacheValid($dbLiveEntryServerNode))
			{
				KalturaLog::info("Removing media server id [" . $dbLiveEntryServerNode->getServerNodeId() . "]");
				$dbLiveEntryServerNode->deleteOrMarkForDeletion();
			}
		}
	}

	public function getLiveStatus()
	{
		$entryServerNodes = EntryServerNodePeer::retrieveByEntryId($this->getId());
		
		$status = EntryServerNodeStatus::STOPPED;
		foreach ($entryServerNodes as $entryServerNode)
		{
			/* @var $entryServerNode EntryServerNode */
			$status = self::maxLiveEntryStatus($status, $entryServerNode->getStatus());
		}
		
		return $status;
	}

	public function setLiveStatus ($v, $mediaServerIndex)
	{
		throw new KalturaAPIException("This function is deprecated - you cannot set the live status");
	}

	public function setSegmentDuration($v)
	{
		$this->putInCustomData (LiveEntry::CUSTOM_DATA_SEGMENT_DURATION , $v);
	}

	public function getSegmentDuration()
	{
		$segmentDuration = $this->getFromCustomData(LiveEntry::CUSTOM_DATA_SEGMENT_DURATION, null, null);
		
		$partner = $this->getPartner();
		if($partner && !$segmentDuration)
			$segmentDuration = $partner->getDefaultLiveStreamSegmentDuration();
		
		if(!$segmentDuration)
			$segmentDuration = LiveEntry::DEFAULT_SEGMENT_DURATION_MILLISECONDS;
		
		return $segmentDuration;
	}

	/**
	 * @return array<LiveEntryServerNode>
	 */
	public function getPlayableEntryServerNodes()
	{
		return EntryServerNodePeer::retrievePlayableByEntryId($this->getId());
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
		$mediaServersHostnames = $this->getMediaServersHostnames();
		if (isset($mediaServersHostnames[EntryServerNodeType::LIVE_PRIMARY])) {
			$dynamicAttributes[LiveEntry::PRIMARY_HOSTNAME] = $mediaServersHostnames[EntryServerNodeType::LIVE_PRIMARY];
		}
		if (isset($mediaServersHostnames[EntryServerNodeType::LIVE_BACKUP])) {
			$dynamicAttributes[LiveEntry::SECONDARY_HOSTNAME] = $mediaServersHostnames[EntryServerNodeType::LIVE_BACKUP];
		}
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
	
	public function setRecordingOptions(kLiveEntryRecordingOptions $recordingOptions)
	{
		$this->putInCustomData(LiveEntry::CUSTOM_DATA_RECORD_OPTIONS, serialize($recordingOptions));
	}
	
	/**
	 * @return kLiveEntryRecordingOptions
	 */
	public function getRecordingOptions()
	{
		$recordingOptions = $this->getFromCustomData(LiveEntry::CUSTOM_DATA_RECORD_OPTIONS);
		
		if($recordingOptions)
			$recordingOptions = unserialize($recordingOptions);
		
		return $recordingOptions; 
	}

	public static function maxLiveEntryStatus($primaryMediaServerStatus, $secondaryMediaServerStatus)
	{
		if ($primaryMediaServerStatus == EntryServerNodeStatus::PLAYABLE || $secondaryMediaServerStatus == EntryServerNodeStatus::PLAYABLE)
			return EntryServerNodeStatus::PLAYABLE;
		elseif ($primaryMediaServerStatus == EntryServerNodeStatus::BROADCASTING || $secondaryMediaServerStatus == EntryServerNodeStatus::BROADCASTING)
			return EntryServerNodeStatus::BROADCASTING;
		elseif ($primaryMediaServerStatus == EntryServerNodeStatus::AUTHENTICATED || $secondaryMediaServerStatus == EntryServerNodeStatus::AUTHENTICATED)
			return EntryServerNodeStatus::AUTHENTICATED;
		else
			return EntryServerNodeStatus::STOPPED;
	}
	
	public function isStreamAlreadyBroadcasting()
	{
		$mediaServer = $this->getMediaServer(true);
		if($mediaServer)
		{
			$url = null;
			$protocol = null;
			foreach (array(KalturaPlaybackProtocol::HLS, KalturaPlaybackProtocol::APPLE_HTTP) as $hlsProtocol)
			{
				$config = $this->getLiveStreamConfigurationByProtocol($hlsProtocol, requestUtils::PROTOCOL_HTTP, null, true);
				if ($config)
				{
					$url = $config->getUrl();
					$protocol = $hlsProtocol;
					break;
				}
			}
				
			if($url)
			{
				KalturaLog::info('Determining status of live stream URL [' .$url. ']');
				$dpda= new DeliveryProfileDynamicAttributes();
				$dpda->setEntryId($this->getEntryId());
				$dpda->setFormat($protocol);
				$deliveryProfile = DeliveryProfilePeer::getLiveDeliveryProfileByHostName(parse_url($url, PHP_URL_HOST), $dpda);
				if($deliveryProfile && $deliveryProfile->isLive($url))
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function getCurrentDuration($currChunkDuration, $maxRecordingDuration)
	{
		$lastDuration = 0;
		$recordedEntry = null;
		$recordedEntryId = $this->getRecordedEntryId();
		
		if ($recordedEntryId)
		{
			$recordedEntry = entryPeer::retrieveByPK($recordedEntryId);
			if ($recordedEntry) 
			{
				if ($recordedEntry->getReachedMaxRecordingDuration()) 
				{
					KalturaLog::err("Entry [{$this->getId()}] has already reached its maximal recording duration.");
					return $maxRecordingDuration + 1;
				}
				// if entry is in replacement, the replacement duration is more accurate
				if ($recordedEntry->getReplacedEntryId()) 
				{
					$replacementRecordedEntry = entryPeer::retrieveByPK($recordedEntry->getReplacedEntryId());
					if ($replacementRecordedEntry) 
					{
						$lastDuration = $replacementRecordedEntry->getLengthInMsecs();
					}
				}
				else 
				{
					$lastDuration = $recordedEntry->getLengthInMsecs();
				}
			}
		}
		
		$liveSegmentDurationInMsec = (int)($currChunkDuration * 1000);
		$currentDuration = $lastDuration + $liveSegmentDurationInMsec;
		
		if($currentDuration > $maxRecordingDuration)
		{
			if ($recordedEntry) {
				$recordedEntry->setReachedMaxRecordingDuration(true);
				$recordedEntry->save();
			}
			KalturaLog::err("Entry [{$this->getId()}] duration [" . $lastDuration . "] and current duration [$currentDuration] is more than max allowed duration [$maxRecordingDuration]");
			return $maxRecordingDuration + 1;
		}
		
		return $currentDuration;
	}

	public function getObjectParams($params = null)
	{
		$body = array(
			'recorded_entry_id' => $this->getRecordedEntryId(),
			'push_publish' => $this->getPushPublishEnabled(),
		);
		return array_merge(parent::getObjectParams($params), $body);
	}


}
