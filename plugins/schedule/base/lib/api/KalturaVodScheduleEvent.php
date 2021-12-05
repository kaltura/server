<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaVodScheduleEvent extends KalturaEntryScheduleEvent
{
	
	const MAX_DURATION_YEARS = 5;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new VodScheduleEvent();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}

	/**
	 * {@inheritDoc}
	 * @see KalturaScheduleEvent::getScheduleEventType()
	 */
	public function getScheduleEventType()
	{
		return ScheduleEventType::VOD;
	}
	
	protected function getScheduleEventMaxDuration()
	{
		return self::MAX_DURATION_YEARS * kTimeConversion::YEARS;
	}
	
	protected function getSingleScheduleEventMaxDuration()
	{
		return $this->getScheduleEventMaxDuration();
	}
}