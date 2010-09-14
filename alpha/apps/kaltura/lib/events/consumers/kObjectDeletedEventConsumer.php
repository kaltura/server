<?php
interface kObjectDeletedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 */
	public function objectDeleted(BaseObject $object);
}