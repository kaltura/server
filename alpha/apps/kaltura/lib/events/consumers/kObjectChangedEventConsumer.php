<?php
/**
 * Auto-raised event that raised by the propel generated objects
 * @package Core
 * @subpackage events
 */
interface kObjectChangedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool true if should continue to the next consumer
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns);
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns);
}