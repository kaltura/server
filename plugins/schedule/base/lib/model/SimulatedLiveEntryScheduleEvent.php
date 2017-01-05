<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class SimulatedLiveEntryScheduleEvent extends EntryScheduleEvent
{
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::SIMU_LIVE);
		$this->setRecurrenceType(ScheduleEventRecurrenceType::NONE);
	}

}