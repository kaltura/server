<?php
interface kObjectDeletedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDeleted(BaseObject $object);
}