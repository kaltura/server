<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveStreamScheduleEvent extends EntryScheduleEvent implements ILiveStreamScheduleEvent
{
	const PROJECTED_AUDIENCE = 'projected_audience';
	const SOURCE_ENTRY_ID = 'source_entry_id';
	const PRE_START_TIME = 'pre_start_time';

	/**
	 * @param int $v
	 */
	public function setProjectedAudience($v)
	{
		$this->putInCustomData(self::PROJECTED_AUDIENCE, $v);
	}

	/**
	 * @return int
	 */
	public function getProjectedAudience()
	{
		return $this->getFromCustomData(self::PROJECTED_AUDIENCE);
	}

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

	/**
	 * @param int $v
	 */
	public function setPreStartTime($v)
	{
		$this->putInCustomData(self::PRE_START_TIME, $v);
	}

	/**
	 * @return int
	 */
	public function getPreStartTime()
	{
		return $this->getFromCustomData(self::PRE_START_TIME);
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
			if ($liveEntry && !$liveEntry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY))
			{
				$liveEntry->addCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY);
				$liveEntry->save($con);
			}
	}

	public function getStartTime()
	{
		$preStartTime = !is_null($this->getPreStartTime()) ? $this->getPreStartTime() : 0;
		return parent::getStartTime() - $preStartTime;
	}

}