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
		return $this->calendar->begin();
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
			return $event->write();
		}
		else
		{
			$ret = $this->calendar->writeField('BEGIN', get_class($object));
			$ret .= $this->calendar->writeField('END', get_class($object));
			
			return $ret;
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
		
		return $this->calendar->end();
	}
}