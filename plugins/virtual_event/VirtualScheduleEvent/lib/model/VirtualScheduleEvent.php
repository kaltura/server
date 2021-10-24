<?php
/**
 * @package plugins.virtualEvent
 * @subpackage model
 */

class VirtualScheduleEvent extends ScheduleEvent
{

	const VIRTUAL_EVENT_ID = 'virtual_event_id';
	const VIRTUAL_SCHEDULE_EVENT_SUB_TYPE = 'virtual_schedule_event_sub_type';
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
	public function setVirtualScheduleEventSubType($v)
	{
		$this->putInCustomData(self::VIRTUAL_SCHEDULE_EVENT_SUB_TYPE, $v);
	}
	
	/**
	 * @return string
	 */
	public function getVirtualScheduleEventSubType()
	{
		return $this->getFromCustomData(self::VIRTUAL_SCHEDULE_EVENT_SUB_TYPE);
	}
	
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues ()
	{
		parent ::applyDefaultValues();
		$this -> setType(VirtualScheduleEventType::VIRTUAL);
	}
}