<?php
/**
 * Applicative event that raised by the developer when indexed object is ready for indexing in the index server
 */
interface kObjectReadyForIndexEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectReadyForIndex(BaseObject $object, BatchJob $raisedJob = null);
	
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeReadyForIndexEvent(BaseObject $object);
}