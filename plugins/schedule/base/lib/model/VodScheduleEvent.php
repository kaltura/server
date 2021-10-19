<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class VodScheduleEvent extends EntryScheduleEvent
{
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::VOD);
	}
}