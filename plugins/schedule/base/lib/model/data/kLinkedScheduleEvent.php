<?php
/**
 * @package plugins.schedule
 * @subpackage model.data
 */
class kLinkedScheduleEvent
{
	/**
	 * @var int
	 */
	public $offset;
	
	/**
	 * @var int
	 */
	public $eventId;
	
	public function setOffset(int $offset)
	{
		$this->offset = $offset;
	}
	
	public function setEventId(int $eventId)
	{
		$this->eventId = $eventId;
	}
	
	public function getOffset()
	{
		return $this->offset;
	}
	
	public function getEventId()
	{
		return $this->eventId;
	}
}
