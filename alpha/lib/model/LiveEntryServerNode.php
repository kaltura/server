<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveEntryServerNode extends EntryServerNode
{
	const OM_CLASS = 'LiveEntryServerNode';
	
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
			
			if($this->isColumnModified(EntryServerNodePeer::STATUS) && $this->getStatus() === EntryServerNodeStatus::MARKED_FOR_DELETION)
			{
				$playableServerNodes = EntryServerNodePeer::retrievePlayableByEntryId($this->getEntryId());
				if(!count($playableServerNodes))
				{
					$liveEntry->unsetMediaServer();
				}
				
				$liveEntry->setLastBroadcastEndTime(kApiCache::getTime());
			}
			
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
		// First call parent to clear query cache
		parent::postDelete($con);
		
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_DELETE_MEDIA_SERVER, __METHOD__.":: serverType=".$this->getServerType().":serverNodeId=".$this->getServerNodeId().":dc=".$this->getDc());
		
		$liveEntry = $this->getLiveEntry();
		if($liveEntry && $this->getStatus() !== EntryServerNodeStatus::MARKED_FOR_DELETION)
		{
			/* @var $liveEntry LiveEntry */
			$entryServerNodes = EntryServerNodePeer::retrieveByEntryId($liveEntry->getId());
			if(!count($entryServerNodes))
				$liveEntry->unsetMediaServer();
			
			$liveEntry->setLastBroadcastEndTime(kApiCache::getTime());
			
			if(!$liveEntry->save())
				$liveEntry->indexToSearchIndex();
		}
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
		$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
		if(!$liveEntry)
		{
			KalturaLog::err("Entry with id [{$this->getEntryId()}] not found, clearing entry server node from db");
			$this->delete();
			return;
		}
		
		/* @var $liveEntry LiveEntry */
		$timeFromLastUpdate = time() - $this->getUpdatedAt(null);
		if($this->getDc() === kDataCenterMgr::getCurrentDcId() && !$liveEntry->isCacheValid($this) && $timeFromLastUpdate > LiveEntry::DEFAULT_CACHE_EXPIRY)
		{
			KalturaLog::info("Removing media server id [" . $this->getServerNodeId() . "] from liveEntry [" . $this->getEntryId() . "]");
			$this->deleteOrMarkForDeletion($liveEntry);
		}
	}
	
	public function deleteOrMarkForDeletion($entry = null)
	{
		$liveEntry = $entry ? $entry : entryPeer::retrieveByPK($this->getEntryId());
		if(!$liveEntry)
		{
			KalturaLog::debug("Entry with id [{$this->getEntryId()}] not found, clearing entry server node from db");
			$this->delete();
			return;
		}
		
		$recordStatus = $liveEntry->getRecordStatus();
		if($recordStatus && $recordStatus !== RecordStatus::DISABLED)
		{
			$recordedEntryId = $liveEntry->getRecordedEntryId();
			$recordedEntry = $recordedEntryId ? entryPeer::retrieveByPK($recordedEntryId) : null;
			if(!$recordedEntry)
			{
				KalturaLog::debug("Recorded entry with id [{$this->getEntryId()}] not found, clearing entry server node from db");
				$this->delete();
				return;
			}
			
			if(!myEntryUtils::shouldServeVodFromLive($recordedEntry, false))
			{
				KalturaLog::debug("Recorded entry with id [{$this->getEntryId()}] found and ready or recorded is of old source type, clearing entry server node from db");
				$this->delete();
				return;
			}
			
			$this->setStatus(EntryServerNodeStatus::MARKED_FOR_DELETION);
			$this->save();
			return;
		}
		
		KalturaLog::debug("Live entry with id [{$liveEntry->getId()}], is set with recording disabled, clearing entry server node id [{$this->getId()}] from db");
		$this->delete();
	}
}