<?php
/**
 * Applicative event that raised by the developer when object is ready for update
 */
interface kObjectReadyForIndexInheritedTreeEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectReadyForIndexInheritedTreeEvent(BaseObject $object, BatchJob $raisedJob = null);
	
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeReadyForIndexInheritedTreeEvent(BaseObject $object);
}