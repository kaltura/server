<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
abstract class BaseLiveStreamScheduleEvent extends EntryScheduleEvent
	implements
	ILiveStreamScheduleEvent
{
	const SOURCE_ENTRY_ID = 'source_entry_id';
	
	/**
	 * @param string $v
	 */
	public function setSourceEntryId($v)
	{
		$this->putInCustomData(self::SOURCE_ENTRY_ID, $v);
	}
	
	/**
	 * @return string
	 */
	public function getSourceEntryId()
	{
		return $this->getFromCustomData(self::SOURCE_ENTRY_ID);
	}
	
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::LIVE_STREAM);
	}
	
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		$this->addCapabilityToTemplateEntry($con);
	}
	
	public function postUpdate(PropelPDO $con = null)
	{
		parent::postUpdate($con);
		$this->addCapabilityToTemplateEntry($con);
	}
	
	protected function addCapabilityToTemplateEntry($con)
	{
		$liveEntry = entryPeer::retrieveByPK($this->getTemplateEntryId());
		if ($liveEntry)
		{
			$shouldSave = false;
			if (!$liveEntry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY))
			{
				$liveEntry->addCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY);
				$shouldSave = true;
			}
			if ($this->getSourceEntryId() && !$liveEntry->hasCapability(LiveEntry::SIMULIVE_CAPABILITY))
			{
				$liveEntry->addCapability(LiveEntry::SIMULIVE_CAPABILITY);
				$shouldSave = true;
			}
			if ($shouldSave)
			{
				$liveEntry->save($con);
			}
		}
	}
}