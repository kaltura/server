<?php

class KalturaICalSerializer extends KalturaSerializer
{
	private $calendar;
	
	public function __construct()
	{
		$this->calendar = new kSchedulingICalCalendar();
	}
	/**
	 * {@inheritDoc}
	 * @see KalturaSerializer::setHttpHeaders()
	 */
	public function setHttpHeaders()
	{
		header("Content-Type: text/calendar; charset=UTF-8");		
	}

	/**
	 * {@inheritDoc}
	 * @see KalturaSerializer::getHeader()
	 */
	public function getHeader() 
	{
		$this->calendar->begin();
	}


	/**
	 * {@inheritDoc}
	 * @see KalturaSerializer::serialize()
	 */
	public function serialize($object)
	{
		if($object instanceof KalturaScheduleEvent)
		{
			$event = kSchedulingICalEvent::fromObject($object);
			$event->write();
		}
		else
		{
			$this->calendar->writeField('BEGIN', get_class($object));
			$this->calendar->writeField('END', get_class($object));
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see KalturaSerializer::getFooter()
	 */
	public function getFooter($execTime = null)
	{
		if($execTime)
			$this->calendar->writeField('x-kaltura-execution-time', $execTime);
		
		$this->calendar->end();
	}
}