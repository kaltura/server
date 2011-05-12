<?php
/**
 * Applicative event that raised implicitly by the developer
 */
interface kObjectDeletedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null);
}