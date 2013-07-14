<?php
/**
 * Interface denoting an event which happened in a specific object scope which allows the event to be retrieved.
 *
 * @package Core
 * @subpackage events
 */
interface IKalturaObjectRelatedEvent 
{
	/**
	 * Function returns the object in the context of which the event occured.
	 * @return BaseObject
	 */
	public function getObject();
}