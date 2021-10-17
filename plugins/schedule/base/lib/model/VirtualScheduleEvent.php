<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */

class VirtualScheduleEvent extends ScheduleEvent
{

	const VIRTUAL_EVENT_ID = 'virtual_event_id';
	const VIRTUAL_SCHEDULE_EVENT_TYPE = 'virtual_schedule_event_type';
	/**
	 * @param string $v
	 */
	public function setVirtualEventId($v)
	{
		$this->putInCustomData(self::VIRTUAL_EVENT_ID, $v);
	}
	
	/**
	 * @return string
	 */
	public function getVirtualEventId()
	{
		return $this->getFromCustomData(self::VIRTUAL_EVENT_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setVirtualScheduleEventType($v)
	{
		$this->putInCustomData(self::VIRTUAL_SCHEDULE_EVENT_TYPE, $v);
	}
	
	/**
	 * @return string
	 */
	public function getVirtualScheduleEventType()
	{
		return $this->getFromCustomData(self::VIRTUAL_SCHEDULE_EVENT_TYPE);
	}
	
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues ()
	{
		parent ::applyDefaultValues();
		$this -> setType(ScheduleEventType::VIRTUAL);
	}
}