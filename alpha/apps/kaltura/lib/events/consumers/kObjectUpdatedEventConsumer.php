<?php
/**
 * Applicative event that raised implicitly by the developer
 */
interface kObjectUpdatedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectUpdated(BaseObject $object);
}