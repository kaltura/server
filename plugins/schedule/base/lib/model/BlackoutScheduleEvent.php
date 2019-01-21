<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class BlackoutScheduleEvent extends ScheduleEvent
{
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::BLACKOUT);
	}
}