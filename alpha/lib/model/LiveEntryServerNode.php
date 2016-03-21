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
	
	private $statusChanged = false;
	
	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postSave()
	 */
	public function postSave(PropelPDO $con = null)
	{
		if($this->statusChanged)
		{
			$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
			if($liveEntry)
			{
				KalturaLog::debug("Live Status for entry [" . $this->getEntryId() . "] updated to [" . $this->getStatus() . "] re-indexing entry with new live status");
				$liveEntry->indexToSearchIndex();
			}
			
			$this->statusChanged = false;
		}
			
		parent::postSave($con);
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
	
	public function setStatus($v)
	{
		if($this->getStatus() !== $v)
			$this->statusChanged = true;
		
		return parent::setStatus($v);
	}
}