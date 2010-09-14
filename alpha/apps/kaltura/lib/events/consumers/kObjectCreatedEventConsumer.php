<?php
interface kObjectCreatedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BaseObject $object
	 */
	public function objectCreated(BaseObject $object);
}