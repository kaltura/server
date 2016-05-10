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
		
		if($this->getServerType() === EntryServerNodeType::LIVE_PRIMARY)
		{
			$liveEntry = $this->getLiveEntry();
			if($liveEntry)
			{
					$liveEntry->setPrimaryServerNodeId($this->getServerNodeId());
					
					if(!$liveEntry->getCurrentBroadcastStartTime() && $this->getStatus() === EntryServerNodeStatus::AUTHENTICATED)
						$liveEntry->setCurrentBroadcastStartTime(time());
					
					$liveEntry->save();
			}	
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
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_DELETE_MEDIA_SERVER, __METHOD__);
		
		$liveEntry = $this->getLiveEntry();
		if($liveEntry)
		{
			if($this->getServerType() === EntryServerNodeType::LIVE_PRIMARY)
			{
				if($liveEntry->getCurrentBroadcastStartTime())
					$liveEntry->setCurrentBroadcastStartTime(0);
			}
			
			if(!$liveEntry->save())
				$liveEntry->indexToSearchIndex();
		}
		
		parent::postDelete($con);
	}

	public function setStreams(KalturaLiveStreamParamsArray $v) 
	{ 
		$this->putInCustomData(self::CUSTOM_DATA_STREAMS, $v); 
	}
	
	public function getStreams()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_STREAMS);
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
}