<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaBlackoutScheduleEvent extends KalturaScheduleEvent
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new BlackoutScheduleEvent();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}

	/**
	 * {@inheritDoc}
	 * @see KalturaScheduleEvent::getScheduleEventType()
	 */
	public function getScheduleEventType()
	{
		return ScheduleEventType::BLACKOUT;
	}
}