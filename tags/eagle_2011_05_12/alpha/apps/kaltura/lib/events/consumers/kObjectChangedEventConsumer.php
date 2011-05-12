<?php
/**
 * Auto-raised event that raised by the propel generated objects
 */
interface kObjectChangedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool true if should continue to the next consumer
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns);
}