<?php
/**
 * Auto-raised event that raised by the propel generated objects
 */
interface kObjectCreatedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectCreated(BaseObject $object);
}