<?php
/**
 * Applicative event that raised implicitly by the developer
 * @package Core
 * @subpackage events
 */
interface kObjectDataChangedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDataChanged(BaseObject $object, $previousVersion = null, BatchJob $raisedJob = null);
	
	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeDataChangedEvent(BaseObject $object, $previousVersion = null);
}