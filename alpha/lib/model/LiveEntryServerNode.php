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
		$te = new TrackEntry();
		$te->setEntryId($this->getEntryId());
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_MEDIA_SERVER);
		$te->setDescription(__METHOD__.":: serverType=".$this->getServerType().":serverNodeId=".$this->getServerNodeId().":status=".$this->getStatus().":dc=".$this->getDc());
		TrackEntry::addTrackEntry($te);
		
		parent::postInsert($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$te = new TrackEntry();
		$te->setEntryId($this->getEntryId());
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPDATE_MEDIA_SERVER);
		$te->setDescription(__METHOD__.":: serverType=".$this->getServerType().":serverNodeId=".$this->getServerNodeId().":status=".$this->getStatus().":dc=".$this->getDc());
		TrackEntry::addTrackEntry($te);
		
		if($this->isColumnModified(EntryServerNodePeer::STATUS))
		{
			$dbLiveEntry = entryPeer::retrieveByPK($this->getEntryId());
			
			if(!$dbLiveEntry)
			{
				KalturaLog::debug("Live entry with id [" . $this->getEntryId() . "] not found, live entry will not be re-indexed to sphinx with new status [" . $this->getStatus() . "]");
				return parent::postUpdate($con);
			}
			
			$dbLiveEntry->indexToSearchIndex();
		}

		parent::postUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postDelete()
	 */
	public function postDelete(PropelPDO $con = null)
	{
		$te = new TrackEntry();
		$te->setEntryId($this->getEntryId());
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_DELETE_MEDIA_SERVER);
		$te->setDescription(__METHOD__);
		TrackEntry::addTrackEntry($te);
		
		$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
		if(!$liveEntry)
		{
			KalturaLog::debug("Live Entry server node for entry [" . $this->getEntryId() . "] has been deleted, but live entry was not found, will not be re-indexed to sphinx with new status");
			return parent::postDelete($con); 
		}
	
		KalturaLog::debug("Live Entry server node for entry [" . $this->getEntryId() . "] has been deleted, re-indexing entry with new live status");
		$liveEntry->indexToSearchIndex();	
		
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
}