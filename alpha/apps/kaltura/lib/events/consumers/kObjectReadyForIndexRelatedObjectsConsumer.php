<?php
/**
 * Applicative event that raised implicitly by the developer
 * @package Core
 * @subpackage events
 */
interface kObjectReadyForIndexRelatedObjectsConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectReadyForIndexRelatedObjects(BaseObject $object);

	/**
	 * @param BaseObject $object
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeReadyForIndexRelatedObjectsEvent(BaseObject $object);
}