<?php
/**
 * Applicative event that raised explicitly by the developer
 * @package Core
 * @subpackage events
 */
interface kObjectReplacedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectReplaced(BaseObject $object, BatchJob $raisedJob = null);
	
	/**
	 * @param BaseObject $object
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeReplacedEvent(BaseObject $object);
}