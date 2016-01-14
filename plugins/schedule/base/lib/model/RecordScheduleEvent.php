<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class RecordScheduleEvent extends EntryScheduleEvent
{
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::RECORD);
	}
}