<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveStreamScheduleEvent extends EntryScheduleEvent
{
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::LIVE_STREAM);
	}
}