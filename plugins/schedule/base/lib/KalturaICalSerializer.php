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
		elseif($object instanceof KalturaScheduleEventArray)
		{
			$ret = '';
			foreach($object as $item)
			{
				$ret .= $this->serialize($item);
			}
			return $ret;
		}
		elseif($object instanceof KalturaScheduleEventListResponse)
		{
			$ret = $this->serialize($object->objects);
			$ret .= $this->calendar->writeField('X-KALTURA-TOTAL-COUNT', $object->totalCount);
			return $ret;
		}
		elseif($object instanceof KalturaAPIException)
		{
			$ret = $this->calendar->writeField('BEGIN', 'VERROR');
			$ret .= $this->calendar->writeField('X-KALTURA-CODE', $object->getCode());
			$ret .= $this->calendar->writeField('X-KALTURA-MESSAGE', $object->getMessage());
			$ret .= $this->calendar->writeField('X-KALTURA-ARGUMENTS', implode(';', $object->getArgs()));
			$ret .= $this->calendar->writeField('END', 'VERROR');
			return $ret;
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