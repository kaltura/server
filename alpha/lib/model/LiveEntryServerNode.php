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
	 * @see BaseEntryServerNode::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if ($this->isColumnModified(EntryServerNodePeer::STATUS))
		{
			$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
			if($liveEntry)
			{
				KalturaLog::debug("Live Status for entry [" . $this->getEntryId() . "] updated to [" . $this->getStatus() . "] re-indexing entry with new live status");
				$liveEntry->indexToSearchIndex();
			}
			else 
			{
				KalturaLog::debug("Live entry with id [" . $this->getEntryId() . "] not found, live entry will not be re-indexed to sphinx with new status [" . $this->getStatus() . "]");
			}
		}
			
		parent::preSave($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postDelete()
	 */
	public function postDelete(PropelPDO $con = null)
	{
		$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
		if($liveEntry)
		{
			KalturaLog::debug("Live Entry server node for entry [" . $this->getEntryId() . "] has been deleted, re-indexing entry with new live status");
			$liveEntry->indexToSearchIndex();
		}
		else
		{
			KalturaLog::debug("Live Entry server node for entry [" . $this->getEntryId() . "] has been deleted, but live entry was not found, will not be re-indexed to sphinx with new status");
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
}