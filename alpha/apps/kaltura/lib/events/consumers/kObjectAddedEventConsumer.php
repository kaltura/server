<?php
/**
 * Applicative event that raised implicitly by the developer
 */
interface kObjectAddedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectAdded(BaseObject $object);
}