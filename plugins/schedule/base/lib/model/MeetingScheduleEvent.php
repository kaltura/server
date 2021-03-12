<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class MeetingScheduleEvent extends EntryScheduleEvent
{
	const PRE_START_TIME = 'pre_start_time';
	
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
		return $this->getFromCustomData(self::PRE_START_TIME, null, 0);
	}
	
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues ()
	{
		parent ::applyDefaultValues();
		$this -> setType(ScheduleEventType::MEETING);
	}
}