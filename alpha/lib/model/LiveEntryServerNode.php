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
		
		$this->updateLiveEntryData($this->getServerNodeId());
		
		parent::postInsert($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPDATE_MEDIA_SERVER, __METHOD__.":: serverType=".$this->getServerType().":serverNodeId=".$this->getServerNodeId().":status=".$this->getStatus().":dc=".$this->getDc());
		
		$entrySaved = $this->updateLiveEntryData($this->getServerNodeId());		
		if($this->isColumnModified(EntryServerNodePeer::STATUS) && !$entrySaved)
			$this->indexLiveEntry();

		parent::postUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postDelete()
	 */
	public function postDelete(PropelPDO $con = null)
	{
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_DELETE_MEDIA_SERVER, __METHOD__);
		
		$entrySaved = $this->updateLiveEntryData(null);
		if (!$entrySaved)
			$this->indexLiveEntry();
		
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
	
	private function indexLiveEntry()
	{
		$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
		if(!$liveEntry)
		{
			KalturaLog::debug("Live Entry server node for entry [" . $this->getEntryId() . "] has been deleted, but live entry was not found, will not be re-indexed to sphinx with new status");
			return;
		}
		
		KalturaLog::debug("Live Entry server node for entry [" . $this->getEntryId() . "] has been deleted, re-indexing entry with new live status");
		$liveEntry->indexToSearchIndex();
	}
	
	private function updateLiveEntryData($serverNodeId)
	{
		$shouldSave = false;
		
		$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
		/* @var $liveEntry LiveEntry */
		if(!$liveEntry)
		{
			KalturaLog::debug("Live entry with id [" . $this->getEntryId() . "] not found, live entry data will not be updated");
			return $shouldSave;
		}
		
		if($this->getServerType() === EntryServerNodeType::LIVE_PRIMARY && $liveEntry->getPrimaryServerNodeId() !== $serverNodeId)
		{
			$liveEntry->setPrimaryServerNodeId($serverNodeId);
			$shouldSave = true;
		}
		
		if($this->isColumnModified(EntryServerNodePeer::STATUS) && $this->getStatus() === EntryServerNodeStatus::PLAYABLE)
		{
			$liveEntry->setLastBroadcast(kApiCache::getTime());
			$shouldSave = true;
		}
		
		if($shouldSave)
			$liveEntry->save();
		
		return $shouldSave;
	}
}