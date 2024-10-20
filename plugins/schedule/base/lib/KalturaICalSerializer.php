<?php

class KalturaICalSerializer extends KalturaSerializer
{
	private $calendar;
	protected $timeZoneBlockArray;


	public function __construct()
	{
		$this->calendar = new kSchedulingICalCalendar();
		$this->timeZoneBlockArray = array();
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

	protected function injectTimeZoneBlocks($iCalString)
	{
		$position = strpos($iCalString, 'BEGIN:' . kSchedulingICal::TYPE_EVENT);

		if ($position !== false)
		{
			$iCalBeforeEvents = substr($iCalString, 0, $position);
			$iCalWithEvents = substr($iCalString, $position);
			// Clean array from duplicated transitions
			$this->timeZoneBlockArray = array_unique($this->timeZoneBlockArray);
			// Add BEGIN/END timezone tags
			array_unshift($this->timeZoneBlockArray, "BEGIN:VTIMEZONE\r\n");
			$this->timeZoneBlockArray[] = "END:VTIMEZONE\r\n";
			//Inject to the iCal
			$timeZoneBlocksCollection = implode('', $this->timeZoneBlockArray);
			return $iCalBeforeEvents . $timeZoneBlocksCollection . $iCalWithEvents;
		}
		return $iCalString;
	}

	protected function innerSerialize($object)
	{
		$event = kSchedulingICalEvent::fromObject($object);
		return $event->write($object, $this->timeZoneBlockArray);
	}

	/**
	 * {@inheritDoc}
	 * @see KalturaSerializer::serialize()
	 */
	public function serialize($object)
	{
		if($object instanceof KalturaScheduleEvent)
		{
			$scheduleEventArray = new KalturaScheduleEventArray();
			$scheduleEventArray[] = $object;
			return $this->serialize($scheduleEventArray);
		}
		elseif($object instanceof KalturaScheduleEventArray)
		{
			$ret = '';
			foreach($object as $item)
			{
				$ret .= $this->innerSerialize($item);
			}
			return $this->injectTimeZoneBlocks($ret);
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