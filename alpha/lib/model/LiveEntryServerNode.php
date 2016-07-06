<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveEntryServerNode extends EntryServerNode
{
	const OM_CLASS = 'LiveEntryServerNode';
	const DEFAULT_CACHE_EXPIRY = 120;
	
	const CUSTOM_DATA_STREAMS = "streams";
	const CUSTOM_DATA_APPLICATION_NAME = "application_name";
	const CUSTOM_DATA_DC = "dc";
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_MEDIA_SERVER, __METHOD__.":: serverType=".$this->getServerType().":serverNodeId=".$this->getServerNodeId().":status=".$this->getStatus().":dc=".$this->getDc());
		
		$liveEntry = $this->getLiveEntry();
		if($liveEntry)
		{
			if($this->getServerType() === EntryServerNodeType::LIVE_PRIMARY)
			{
				$liveEntry->setPrimaryServerNodeId($this->getServerNodeId());
				
				if(!$liveEntry->getCurrentBroadcastStartTime())
					$liveEntry->setCurrentBroadcastStartTime(time());
			}
			
			if(!$liveEntry->save())
				$liveEntry->indexToSearchIndex();
		}
		
		parent::postInsert($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPDATE_MEDIA_SERVER, __METHOD__.":: serverType=".$this->getServerType().":serverNodeId=".$this->getServerNodeId().":status=".$this->getStatus().":dc=".$this->getDc());
		
		$liveEntry = $this->getLiveEntry();
		if($liveEntry)
		{
			if($this->isColumnModified(EntryServerNodePeer::SERVER_NODE_ID) && $this->getServerType() === EntryServerNodeType::LIVE_PRIMARY && $liveEntry->getPrimaryServerNodeId() !== $this->getServerNodeId())
				$liveEntry->setPrimaryServerNodeId($this->getServerNodeId());
			
			if($this->isColumnModified(EntryServerNodePeer::STATUS) && $this->getStatus() === EntryServerNodeStatus::PLAYABLE)
				$liveEntry->setLastBroadcast(time());
			
			if(!$liveEntry->getCurrentBroadcastStartTime() && $this->isColumnModified(EntryServerNodePeer::STATUS) && $this->getStatus() === EntryServerNodeStatus::AUTHENTICATED && $this->getServerType() === EntryServerNodeType::LIVE_PRIMARY)
				$liveEntry->setCurrentBroadcastStartTime(time());
			
			if(!$liveEntry->save())
				$liveEntry->indexToSearchIndex();
		}

		parent::postUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postDelete()
	 */
	public function postDelete(PropelPDO $con = null)
	{
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_DELETE_MEDIA_SERVER, __METHOD__.":: serverType=".$this->getServerType().":serverNodeId=".$this->getServerNodeId().":dc=".$this->getDc());
		
		$liveEntry = $this->getLiveEntry();
		if($liveEntry)
		{
			/* @var $liveEntry LiveEntry */
			$entryServerNodes = EntryServerNodePeer::retrieveByEntryId($liveEntry->getId());
			if(!count($entryServerNodes))
				$liveEntry->unsetMediaServer();
			
			$liveEntry->setLastBroadcastEndTime(kApiCache::getTime());
			
			if(!$liveEntry->save())
				$liveEntry->indexToSearchIndex();
		}
		
		parent::postDelete($con);
	}

	public function setStreams(array $v) 
	{ 
		$this->putInCustomData(self::CUSTOM_DATA_STREAMS, serialize($v));
	}
	
	public function getStreams()
	{
		$streams = $this->getFromCustomData(self::CUSTOM_DATA_STREAMS, null, array());
		
		if(count($streams))
			$streams = unserialize($streams);
		
		return $streams;
	}
	
	public function setApplicationName($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_APPLICATION_NAME, $v);
	}
	
	public function getApplicationName()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_APPLICATION_NAME);
	}
	
	public function setDc($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DC, $v);
	}
	
	public function getDc()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DC);
	}
	
	private function getLiveEntry()
	{
		$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
		if(!$liveEntry)
		{
			KalturaLog::debug("Live entry with id [" . $this->getEntryId() . "] not found, live entry data will not be updated");
			return null;
		}
		
		return $liveEntry;
	}
	
	public function validateEntryServerNode()
	{	
		if($this->getDc() === kDataCenterMgr::getCurrentDcId() && !$this->isCacheValid())
		{
			KalturaLog::info("Removing media server id [" . $this->getServerNodeId() . "] from liveEntry [" . $this->getEntryId() . "]");
			$this->delete();
		}
	}
	
	private function getCacheKey()
	{
		return $this->getEntryId()."_".$this->getServerNodeId()."_".$this->getServerType();
	}
	
	private static function getCacheType()
	{
		return kCacheManager::CACHE_TYPE_LIVE_MEDIA_SERVER . '_' . kDataCenterMgr::getCurrentDcId();
	}
	
	/**
	 * Stores given value in cache for with the given key as an identifier
	 * @param string $key
	 * @return bool
	 * @throws Exception
	 */
	public function storeInCache()
	{
		$key = $this->getCacheKey();
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
	 * @param LiveEntryServerNode $liveEntryServerNode
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function isCacheValid()
	{
		$cacheType = self::getCacheType();
		$cacheStore = kCacheManager::getSingleLayerCache($cacheType);
		if(!$cacheStore)
		{
			KalturaLog::warning("Cache store [$cacheType] not found");
			$lastUpdate = time() - $this->getUpdatedAt(null);
			$expiry = kConf::get('media_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY);
	
			return $lastUpdate <= $expiry;
		}
	
		$key = $this->getCacheKey();
		$ans = $cacheStore->get($key);
		KalturaLog::debug("Get cache key [$key] from store [$cacheType] returned [$ans]");
		return $ans;
	}
}