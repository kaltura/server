<?php
/**
 * @package Core
 * @subpackage events
 */
interface kObjectErasedEventConsumer extends KalturaEventConsumer
{
    /**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectErased(BaseObject $object);
	
	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeErasedEvent(BaseObject $object);
}