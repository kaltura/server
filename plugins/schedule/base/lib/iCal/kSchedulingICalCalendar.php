<?php

class kSchedulingICalCalendar extends kSchedulingICalComponent
{
	/**
	 * @param string $data
	 * @param KalturaScheduleEventType $eventsType
	 */
	public function __construct($data = null, $eventsType = null)
	{
		$this->setKalturaType($eventsType);
		parent::__construct($data);
	}
	
	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::getType()
	 */
	protected function getType()
	{
		return kSchedulingICal::TYPE_CALENDAR;
	}
}
