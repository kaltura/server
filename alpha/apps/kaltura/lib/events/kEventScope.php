<?php
/**
 * @package Core
 * @subpackage events
 */
class kEventScope extends kScope
{
	/**
	 * @var KalturaEvent
	 */
	protected $event;
	
	/**
	 * @param KalturaEvent $v
	 */
	public function setEvent(KalturaEvent $v)
	{
		$this->event = $v;
	}
	
	/**
	 * @return KalturaEvent
	 */
	public function getEvent()
	{
		return $this->event;
	}
}